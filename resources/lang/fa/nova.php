<?php

use App\Enums\OrderStatus;

return [
    'id' => 'شناسه',
    'user' => 'کاربر',
    'driver' => 'راننده',
    'location' => 'مکان',
    'requires_driver' => 'نیازمند راننده',
    'final_price_needed' => 'نیازمند قیمت قطعی',
    'pickup_date' => 'تاریخ تحویل',
    'description' => 'توضیحات',
    'status' => 'وضعیت',
    OrderStatus::PENDING => 'در انتظار',
    OrderStatus::REJECTED => 'رد شده',
    OrderStatus::CANCELED_BY_CUSTOMER => 'لغو مشتری',
    OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL => 'در انتظار مشتری',
    OrderStatus::ACCEPTED_WAITING_FOR_DRIVER => 'در انتظار راننده',
    OrderStatus::ACCEPTED_BY_DRIVER => 'راننده پذیرفت',
    OrderStatus::DRIVER_HEADING_TO_LOCATION => 'راننده در راه',
    OrderStatus::PICKED_UP => 'مرسوله تحویل گرفته شد',
    OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED => 'بدون راننده پذیرفته شد',
    OrderStatus::DELIVERED => 'دریافت شد',
    OrderStatus::FINISHED => 'انجام شد',
    'approximate_weight' => 'وزن تقریبی',
    'final_weight' => 'وزن نهایی',
    'approximate_price' => 'قیمت پیشنهادی',
    'final_price' => 'قیمت نهایی',
    'final_driver_price' => 'کرایه بار',
    'waybill_image' => 'تصویر بارنامه',
    'waybill_number' => 'شماره بارنامه',
    'evacuation_permit_image' => 'تصویر مجوز خروج',
    'evacuation_permit_number' => 'شماره مجوز خروج',
    'images' => 'تصاویر',
    'image' => 'تصویر',
    'display' => 'نمایش',
    'MilanJam' => 'میلان جم',
    'Order' => 'سفارش',
    'Orders' => 'سفارش ها',
    'order_waybill_not_uploaded' => 'تصویر بارنامه این سفارش آپلود نشده است.',
    'suggested_price' => 'قیمت پیشنهادی',
    'suggested_price_help' => 'در صورتیکه کاربر مایل به دانستن قیمت پیشنهادی شما برای این سفارش است قیمت پیشنهادی را اینجا بنویسید. در غیر این صورت این فیلد را خالی بگذارید.',
    'accept_pending_order' => 'پذیرفتن سفارش',
    'accept' => 'پذیرفتن',
    'cancel' => 'انصراف',
    'reject_order' => 'رد سفارش',
    'reject' => 'رد',
    'reject_order_confirmation' => 'این سفارش را رد می کنید؟',
    'promoted_to_admin' => 'نقش کاربر به ادمین ارتقا یافت.',
    'something_went_wrong' => 'بروز خطا هنگام انجام عملیات.',
    'promote_user' => 'ارتقا به ادمین',
    'promote_user_confirm_text' => 'نقش کاربر را به ادمین ارتقا می دهید؟',
    'yes' => 'بله',
];