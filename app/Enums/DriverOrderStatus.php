<?php

namespace App\Enums;

use MyCLabs\Enum\Enum;

class DriverOrderStatus extends Enum
{
    const SUGGESTED = 'suggested';
    const CANCELED_BY_DRIVER = 'canceled_by_driver';
    const ACCEPTED_BY_DRIVER = 'accepted_by_driver';
    const ASSIGNED_TO_DRIVER = 'assigned_to_driver';
    const ORDER_REJECTED = 'order_rejected';
    const EXPIRED = 'expired';
    const TAKEN = 'taken';
}