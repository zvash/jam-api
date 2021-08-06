<?php

namespace App\Repositories;


use App\Exceptions\ActivationCodeNotFoundException;
use App\Exceptions\InvalidPhoneNumberException;
use App\Exceptions\WrongPasswordException;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActivationCode;
use App\Models\Role;
use App\Traits\Passport\PassportToken;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    use PassportToken;

    /**
     * @param array $inputs
     * @return array
     */
    public function register(array $inputs)
    {
        $user = User::query()
            ->where('phone', $inputs['phone'])
            ->whereNull('phone_verified_at')
            ->first();
        if (! $user) {
            $user = new User($inputs);
        } else {
            $tokens = $user->tokens ?? [];
            foreach ($tokens as $token) {
                $token->revoke();
            }
        }
        $user->first_name = $inputs['first_name'];
        $user->last_name = $inputs['last_name'];
        $user->password = Hash::make($inputs['password']);
        $user->created_at = Carbon::now();
        $user->save();

        $normal = Role::create(['name' => 'normal']);
        $user->roles()->attach($normal->id);

        $activationCode = ActivationCode::createForUser($user);
        $activationCode->send();
        $loginResponse = $this->logUserInWithoutPassword($user);
        $loginResponse['code'] = $activationCode->code;
       return $loginResponse;
    }

    /**
     * @param User $user
     * @param array $inputs
     * @return User
     * @throws ActivationCodeNotFoundException
     */
    public function verifyPhone(User $user, array $inputs)
    {
        $code = $inputs['code'];
        $activationRecord = ActivationCode::query()
            ->where('phone', $user->phone)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();
        if ($activationRecord) {
            $user->phone_verified_at = Carbon::now();
            $user->save();
            ActivationCode::query()
                ->where('phone', $user->phone)
                ->delete();
            return $user;
        }
        throw new ActivationCodeNotFoundException(__('messages.error.invalid_activation_code'));
    }

    /**
     * @param User $user
     * @return array
     */
    public function resendPhoneActivation(User $user)
    {
        $activationCode = ActivationCode::createForUser($user);
        $activationCode->send();
        $number = substr_replace($user->phone, '......', -8, 6);
        return [
            'message' => __('messages.success.activation_sent', ['number' => $number]),
            'code' => $activationCode->code,
        ];
    }

    /**
     * @param string $phone
     * @return array
     * @throws InvalidPhoneNumberException
     */
    public function sendForgotPasswordRecoveryCode(string $phone)
    {
        $user = User::query()
            ->where('phone', $phone)
            ->whereNotNull('phone_verified_at')
            ->first();
        if(! $user) {
            throw new InvalidPhoneNumberException(__('messages.error.phone_number_invalid'));
        }
        $activationCode = ActivationCode::createWithPhone($phone);
        $activationCode->send();
        $number = $phone;
        return [
            'message' => __('messages.success.activation_sent', ['number' => $number]),
            'code' => $activationCode->code,
        ];
    }

    /**
     * @param string $phone
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|ActivationCode
     */
    public function getActivationCode(string $phone, string $code)
    {
        return ActivationCode::query()
            ->where('phone', $phone)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * @param User $user
     * @param string $oldPassword
     * @param string $newPassword
     * @return array|\League\OAuth2\Server\ResponseTypes\BearerTokenResponse
     * @throws WrongPasswordException
     */
    public function changePassword(User $user, string $oldPassword, string $newPassword)
    {
        if (Hash::check($oldPassword, $user->password)) {
            $user->password = Hash::make($newPassword);
            $user->save();
            $tokens = $user->tokens ?? [];
            foreach ($tokens as $token) {
                $token->revoke();
            }
            return $this->logUserInWithoutPassword($user);
        }

        throw new WrongPasswordException(__('messages.error.wrong_password'));
    }

    /**
     * @param ProfileUpdateRequest $request
     * @return mixed
     */
    public function updateUserProfile(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $inputs = $request->validated();
        $path = $this->saveFileFromRequest($request, 'avatar', 'users/avatars');
        if ($path) {
            $inputs['avatar'] = $path;
        }
        foreach ($inputs as $key => $value) {
            $user->setAttribute($key, $value);
        }
        $user->save();
        return $user;
    }
}