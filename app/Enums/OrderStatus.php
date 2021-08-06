<?php

namespace App\Enums;

use MyCLabs\Enum\Enum;

class OrderStatus extends Enum
{
    const PENDING = 'pending';
    const REJECTED = 'rejected';
    const CANCELED_BY_CUSTOMER = 'canceled_by_customer';
    const ACCEPTED_WAITING_CUSTOMER_APPROVAL = 'accepted_waiting_customer_approval';
    const ACCEPTED_WAITING_FOR_DRIVER = 'accepted_waiting_for_driver';
    const ACCEPTED_BY_DRIVER = 'accepted_by_driver';
    const DRIVER_HEADING_TO_LOCATION = 'driver_heading_to_location';
    const PICKED_UP = 'picked_up';
    const ACCEPTED_DRIVER_NOT_NEEDED = 'accepted_driver_not_needed';
    const DELIVERED = 'delivered';
    const FINISHED = 'finished';
}