<?php

return [
    'validation' => [
        'invalid_phone' => 'مقدار وارد شده برای شماره تلفن قابل قبول نیست.',
        'invalid_national_code' => 'مقدار وارد شده برای کد ملی قابل قبول نیست',
        'wrong_driver_token' => 'چنین شناسه ای برای سفارش مجود نیست.',
    ],
    'error' => [
        'invalid_activation_code' => 'کد فعال‌سازی وارد شده معتبر نیست.',
        'invalid_recovery_code' => 'کد بازیابی وارد شده معتبر نیست.',
        'phone_not_verified' => 'شماره تلفن شما تأیید نشده است.',
        'wrong_credentials' => 'اطلاعات داده شده برای احراز هویت نادرست هستند.',
        'phone_already_verified' => 'شماره تلفن شما قبلا تأیید شده است.',
        'phone_number_invalid' => 'شماره تلفن داده شده نامعتبر است.',
        'wrong_password' => 'کلمه عبور وارد شده صحیح نیست.',
        'max_locations' => 'امکان تعریف بیش از :maxAddressCount آدرس وجود ندارد.',
        'content_was_not_found' => 'هیچ موردی پیدا نشد.',
        'order_creation_error' => 'بروز خطا هنگام ثبت سفارش جدید.',
        'operation_is_not_permitted' => 'عدم وجود دسترسیهای لازم برای انجام عملیات.',
        'order_price_is_not_specified' => 'مبغ سفارش مشخص نشده است.',
        'just_one_resource_error' => 'امکان تغییر چند :resource به صورت همزمان وجود ندارد.',
        'not_possible_operation' => 'انجام این عمل امکانپذیر نیست.',
        'order_not_cancelable' => 'امکان لغو سفارش وجود ندارد.',
        'order_not_acceptable' => 'امکان تایید قیمت وجود ندارد.',
        'order_waybill_not_uploaded' => 'تصویر بارنامه آپلود نشده است.',
        'order_evacuation_permit_not_uploaded' => 'تصویر مجوز ترخیص بار آپلود نشده است.',
        'empty_waybill_number' => 'شماره بارنامه وارد نشده است',
        'empty_evacuation_permit_number' => 'شماره مجوز خروج وارد نشده است',
        'driver_is_not_paid' => 'هزینه حمل بار به راننده پرداخت نشده است',
        'user_is_not_paid' => 'هزینه مرسوله به فروشنده پرداخت نشده است',
        'empty_final_price' => 'قیمت نهایی وارد نشده است',
        'empty_final_weight' => 'وزن نهایی وارد نشده است',
    ],
    'success' => [
        'activation_sent' => 'کد فعال‌سازی به :number ارسال شد.',
        'password_recovery_code_sent' => 'کد بازیابی پسورد به شماره :number ارسال شد.',
        'successful_operation' => 'عملیات با موفقیت انجام شد.',
        'driver_order_registered' => 'درخواست شما با موفقیت ثبت شد و تا لحظاتی دیگر نتیجه آن اعلام می شود.',
        'order_accepted_by_you' => 'این سفارش پیش از این با موفقیت به شما اختصاص داده شده است.',
        'order_was_taken' => 'این سفارش پیش از شما توسط فرد دیگری پذیرفته شده است.',
        'driver_order_registered_successfully' => 'شما این سفارش را با موفقیت قبول کردید',
        'successfully_registered' => 'کد فعال سازی شما :activationCode است',
    ],
];