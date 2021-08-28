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
    OrderStatus::FINISHED => 'بسته شد',
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
    'driver_is_paid' => 'تسویه با راننده',
    'user_is_paid' => 'تسویه با فروشنده',
    'close' => 'بستن',
    'close_order' => 'بستن سفارش',
    'deliver_order' => 'سفارش تحویل داده شد',
    'leave_empty_if_already_filled' => 'در صورتی که قبلا پر شده نیازی به پر کردن مجدد نیست.',
    'delivered' => 'تحویل شد',
    'was_order_delivered' => 'آیا سفارش تحویل داده شده است؟',
    'waybill_url' => 'تصویر بارنامه',
    'evacuation_permit_url' => 'تصویر مجوز خروج',
    'update_price_list' => 'بروز رسانی لیست قیمت',
    'comment' => 'متن',
    'open' => 'باز',
    'closed' => 'بسته شده',
    'Tickets' => 'تیکت های پشتیبانی',
    'Ticket' => 'تیکت پشتیبانی',
    'phone' => 'تلفن',
    'tickets_status' => 'وضعیت تیکت ها',
    'open_tickets' => 'تیکت های باز',
    'closed_tickets' => 'تیکت های بسته شده',
    'items_weight' => 'وزن اقلام',
    'items' => 'اقلام ضایعاتی',
    'item' => 'اقلام ضایعاتی',
    'locations' => 'آدرس ها',
    'users' => 'کاربران',
    'vehicle_types' => 'انواع ماشین ها',
    'vehicle_type' => 'نوع ماشین',
    'vehicles' => 'ماشین ها',
    'vehicle' => 'ماشین',
    'name' => 'نام',
    'email' => 'ایمیل',
    'national_code' => 'کد ملی',
    'is_courier' => 'راننده',
    'is_admin' => 'ادمین',
    'phone_is_verified' => 'تلفن معتبر',
    'email_is_verified' => 'ایمیل معتبر',
    'first_name' => 'نام',
    'last_name' => 'نام خانوادگی',
    'password' => 'کلمه عبور',
    'price' => 'قیمت',
    'monthly_challenges' => 'چالش های ماهانه',
    'monthly_challenge' => 'چالش ماهانه',
    'year' => 'سال',
    'month' => 'ماه',
    'challenge' => 'چالش',
    'goal' => 'هدف',
    'active' => 'فعال',
    'inactive' => 'غیر فعال',
    'challenge_name' => 'نام چالش',
    'challenge_prize' => 'جایزه چالش',
    'goal_order_transport_count' => 'تعداد سفارش های هدف',
    'add_edit_drivers_challenge' => 'ایجاد یا ویرایش چالش برای رانندگان',
    'create_challenge' => 'ایجاد چالش',
    'related_to' => 'مربوط به',
    'drivers' => 'رانندگان',
    'sellers' => 'فروشندگان',
    'add_edit_sellers_challenge' => 'ایجاد یا ویرایش چالش برای فروشندگان',
    'goal_orders_weight_sum' => 'مجموع وزن سفارش های هدف (کیلوگرم)',
    'progress' => 'پیشرفت',
    'amount' => 'مقدار',
    'monthly_challenges_stats' => 'وضعیت چالش های ماهانه',
    'campaign' => 'چالش دائمی',
    'campaigns' => 'چالش‌های دائمی',
    'title' => 'عنوان',
    'prize' => 'جایزه',
    'prizes' => 'جوایز',
    'subtitle' => 'زیرنویس',
    'milestone' => 'حد نصاب',
    'campaign_level' => 'مرحله چالش',
    'campaign_levels' => 'مراحل چالش',
    'user_campaign_level' => 'مراحل چالش دائمی برای کاربر',
    'user_campaign_levels' => 'مراحل چالش دائمی برای کاربران',
    'handed_over' => 'اهدا شده',
    'campaigns_and_challenges' => 'کمپین ها و چالش ها',
    'plate_number' => 'پلاک',
    'chassis_number' => 'شماره شاسی',
    'engine_number' => 'شماره موتور',
    'owner_full_name' => 'نام کامل مالک',
    'owner_phone' => 'تلفن مالک',
    'owner_national_code' => 'کد ملی مالک',
    'county' => 'شهر',
    'type' => 'نوع',
    'portable_weight' => 'ظرفیت',
    'state' => 'استان',
    'address' => 'آدرس',
    'postal_code' => 'کد پستی',
    'is_default' => 'پیش‌فرض',
    'weight' => 'وزن',
    'need_admin_action' => 'نیازمند توجه',
    'update_price_time' => 'ساعت به‌روزرسانی قیمت‌ها',
    'current_price' => 'قیمت فعلی',
    'next_price' => 'قیمت بعدی',
    'price_change_percent' => 'درصد تغییر قیمت',
    'user_sold_orders' => 'سفارشهای فروخته شده',
    'user_driven_orders' => 'سفارشهای حمل شده',
    'seller_campaign_level' => 'مرحله در کمپین فروشندگان',
    'driver_campaign_level' => 'مرحله در کمپین رانندگان',
];