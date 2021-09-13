<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordSendRecoveryCodeRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PhoneVerificationRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordByCodeRequest;
use App\Http\Requests\VerifyActivationCodeRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\Passport\PassportToken;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class UserController extends Controller
{
    use ResponseMaker,
        PassportToken;

    /**
     * @param RegisterUserRequest $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(RegisterUserRequest $request, UserRepository $repository)
    {
        return $this->success($repository->register($request->validated()));
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $loginResponse = $this->makeInternalLoginRequest($request);
        $content = json_decode($loginResponse->getContent(), 1);
        if (array_key_exists('error', $content)) {
            return $this->failWithCode(__('messages.error.wrong_credentials'), ErrorCodes::WRONG_CREDENTIALS, 401);
        }
        $user = User::findByUserName($request->get('username'));
        $content['phone_is_verified'] = !!$user->phone_verified_at;
        return $this->success($content);
    }

    /**
     * @param PhoneVerificationRequest $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\ActivationCodeNotFoundException
     */
    public function verifyPhone(PhoneVerificationRequest $request, UserRepository $repository)
    {
        $user = $request->user();
        $inputs = $request->validated();
        return $this->success($repository->verifyPhone($user, $inputs));
    }

    /**
     * @param Request $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resendPhoneActivation(Request $request, UserRepository $repository)
    {
        $user = $request->user();
        if ($user->phone_verified_at) {
            return $this->failWithCode(__('messages.error.phone_already_verified'), ErrorCodes::PHONE_ALREADY_VERIFIED, 400);
        }
        return $this->success($repository->resendPhoneActivation($user));
    }

    /**
     * @param ForgotPasswordSendRecoveryCodeRequest $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\InvalidPhoneNumberException
     */
    public function sendForgotPasswordRecoveryCode(ForgotPasswordSendRecoveryCodeRequest $request, UserRepository $repository)
    {
        $inputs = $request->validated();
        return $this->success($repository->sendForgotPasswordRecoveryCode($inputs['phone']));
    }

    /**
     * @param VerifyActivationCodeRequest $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function verifyResetPasswordActivationCode(VerifyActivationCodeRequest $request, UserRepository $repository)
    {
        $inputs = $request->validated();
        $activationCode = $repository->getActivationCode($inputs['phone'], $inputs['code']);
        if (!$activationCode) {
            return $this->failWithCode(__('messages.error.invalid_recovery_code'), ErrorCodes::INVALID_RECOVERY_CODE);
        }
        return $this->success(['message' => 'ok']);
    }

    /**
     * @param ResetPasswordByCodeRequest $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resetPasswordUsingRecoveryCode(ResetPasswordByCodeRequest $request, UserRepository $repository)
    {
        $inputs = $request->validated();
        $activationCode = $repository->getActivationCode($inputs['phone'], $inputs['code']);
        if (!$activationCode) {
            return $this->failWithCode(__('messages.error.invalid_recovery_code'), ErrorCodes::INVALID_RECOVERY_CODE);
        }
        $user = User::query()
            ->where('phone', $activationCode->phone)
            ->first();
        $user->password = Hash::make($inputs['password']);
        $user->save();
        $tokens = $user->tokens ?? [];
        foreach ($tokens as $token) {
            $token->revoke();
        }
        return $this->success($this->logUserInWithoutPassword($user));
    }

    /**
     * @param ChangePasswordRequest $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\WrongPasswordException
     */
    public function changePassword(ChangePasswordRequest $request, UserRepository $repository)
    {
        $inputs = $request->validated();
        $user = $request->user();
        return $this->success($repository->changePassword($user, $inputs['old_password'], $inputs['password']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        return $this->success($request->user());
    }

    /**
     * @param ProfileUpdateRequest $request
     * @param UserRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(ProfileUpdateRequest $request, UserRepository $repository)
    {
        return $this->success($repository->updateUserProfile($request));
    }

    /**
     * @param LoginRequest $request
     * @return mixed
     */
    private function makeInternalLoginRequest(LoginRequest $request)
    {
        $inputs = $request->all();
        $token = Request::create(
            'oauth/token',
            'POST',
            [
                'grant_type' => 'password',
                'client_id' => $inputs['client_id'],
                'client_secret' => $inputs['client_secret'],
                'username' => $inputs['username'],
                'password' => $inputs['password'],
                'scope' => $inputs['scope'],
            ]
        );

        $loginResponse = Route::dispatch($token);
        return $loginResponse;
    }
}
