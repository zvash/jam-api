<?php

return [
    'validation' => [
        'invalid_phone' => 'The provided value for phone number is not valid!',
        'invalid_national_code' => 'The provided value for national code is not valid',
        'wrong_driver_token' => 'Wrong driver token',
    ],
    'error' => [
        'invalid_activation_code' => 'Activation code is invalid',
        'invalid_recovery_code' => 'Recovery code is invalid',
        'phone_not_verified' => 'Your Phone Number is not verified.',
        'wrong_credentials' => 'Wrong credentials.',
        'phone_already_verified' => 'Your phone number is already verified',
        'phone_number_invalid' => 'Given phone number is not valid',
        'wrong_password' => 'Current password is not correct',
        'max_locations' => 'You already defined :maxAddressCount addresses',
        'content_was_not_found' => 'Content was not found',
        'order_creation_error' => 'Error occurred wile registering the new order.',
        'operation_is_not_permitted' => 'Operation is not permitted',
        'order_price_is_not_specified' => 'Order price is not specified',
        'just_one_resource_error' => 'Only one :resource can be processed.',
        'not_possible_operation' => 'Performing this operation is not possible.',
        'order_not_cancelable' => 'Order is not Cancelable.',
    ],
    'success' => [
        'activation_sent' => 'Activation code is sent to :number',
        'password_recovery_code_sent' => 'Password recovery code was sent to :number',
        'successful_operation' => 'Operation was successful.',
        'driver_order_registered' => 'Order acceptance request will be processed within few moment.',
        'order_accepted_by_you' => 'Order is successfully accepted by you.',
        'order_was_taken' => 'Order is taken by another driver before you.',
    ],
];