<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseMaker;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ActivationCodeNotFoundException $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::INVALID_ACTIVATION_CODE);
        });

        $this->renderable(function (InvalidPhoneNumberException $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::PHONE_NUMBER_INVALID);
        });

        $this->renderable(function (WrongPasswordException $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::WRONG_PASSWORD);
        });

        $this->renderable(function (UserReachedMaxLocationsException $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::MAX_LOCATIONS);
        });

        $this->renderable(function (ContentWasNotFountException $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::CONTENT_WAS_NOT_FOUND, 404);
        });

        $this->renderable(function (OrderCreationError $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::ORDER_CREATION_ERROR);
        });

        $this->renderable(function (OrderIsNotCancelableException $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::ORDER_NOT_CANCELABLE);
        });

        $this->renderable(function (OperationNotPossibleException $e, $request) {
            return $this->failWithCode($e->getMessage(), ErrorCodes::OPERATION_NOT_POSSIBLE);
        });
    }
}
