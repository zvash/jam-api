<?php

namespace App\Enums;

use MyCLabs\Enum\Enum;

class ErrorCodes extends Enum
{
    const INVALID_ACTIVATION_CODE = 4001;
    const PHONE_NOT_VERIFIED = 4002;
    const WRONG_CREDENTIALS = 4003;
    const PHONE_ALREADY_VERIFIED = 4004;
    const PHONE_NUMBER_INVALID = 4005;
    const INVALID_RECOVERY_CODE = 4006;
    const WRONG_PASSWORD = 4007;
    const MAX_LOCATIONS = 4008;
    const CONTENT_WAS_NOT_FOUND = 4009;
    const ORDER_CREATION_ERROR = 4010;
    const ORDER_NOT_CANCELABLE = 4011;
    const ORDER_NOT_Acceptable = 4012;
    const OPERATION_NOT_POSSIBLE = 4013;
}