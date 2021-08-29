<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Events\OrderStatusUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @property string status
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'location_id',
        'description',
        'requires_driver',
        'final_price_needed',
        'status',
        'pickup_date',
        'approximate_weight',
        'final_weight',
        'approximate_price',
        'final_price',
        'final_driver_price',
        'waybill_image',
        'waybill_number',
        'evacuation_permit_image',
        'evacuation_permit_number',
        'driver_is_paid',
        'user_is_paid',
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
    ];

    protected $hidden = [
        'driver_is_paid',
        'user_is_paid',
    ];

    protected $appends = [
        'waybill_url',
        'evacuation_permit_url',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, OrderItem::class)
            ->withPivot('weight', 'price');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(OrderImage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function possibleDrivers()
    {
        return $this->hasMany(DriverOrder::class);
    }

    /**
     * @param string $newState
     * @return bool
     */
    public function stateTransitionIsPermitted(string $newState)
    {
        $transitions = [
            OrderStatus::PENDING => [
                OrderStatus::REJECTED,
                OrderStatus::CANCELED_BY_CUSTOMER,
                OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL,
                OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED,
                OrderStatus::ACCEPTED_WAITING_FOR_DRIVER,
            ],
            OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL => [
                OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL,
                OrderStatus::ACCEPTED_WAITING_FOR_DRIVER,
                OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED,
                OrderStatus::CANCELED_BY_CUSTOMER,
            ],
            OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED => [
                OrderStatus::DELIVERED,
                OrderStatus::CANCELED_BY_CUSTOMER,
            ],
            OrderStatus::ACCEPTED_WAITING_FOR_DRIVER => [
                OrderStatus::CANCELED_BY_CUSTOMER,
                OrderStatus::ACCEPTED_BY_DRIVER,
            ],
            OrderStatus::ACCEPTED_BY_DRIVER => [
                OrderStatus::DRIVER_HEADING_TO_LOCATION,
            ],
            OrderStatus::DRIVER_HEADING_TO_LOCATION => [
                OrderStatus::PICKED_UP,
            ],
            OrderStatus::PICKED_UP => [
                OrderStatus::DELIVERED,
            ],
            OrderStatus::DELIVERED => [
                OrderStatus::FINISHED,
            ],
        ];
        $sourceStates = array_keys($transitions);
        if (! in_array($this->status, $sourceStates)) {
            return false;
        }
        $destinationStates = array_values($transitions[$this->status]);
        if (! in_array($newState, $destinationStates)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $newState
     * @throws \Exception
     */
    public function statePrerequisitesIsMet(string $newState)
    {
        $user = request()->user();
        if ($newState == OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL) {
            if ($this->final_price_needed && !$this->approximate_price) {
                throw new \Exception(__('messages.error.order_price_is_not_specified'));
            }
        }

        if ($newState == OrderStatus::PICKED_UP) {
            if (! $this->waybill_image) {
                throw new \Exception(__('messages.error.order_waybill_not_uploaded'));
            }
        }

        if ($newState == OrderStatus::DELIVERED) {
            if ($this->driver_id && ! $this->evacuation_permit_image && ! $user->isAdmin()) {
                throw new \Exception(__('messages.error.order_evacuation_permit_not_uploaded'));
            }
        }

        if ($newState == OrderStatus::FINISHED) {
            $errors = [];
            if ($this->driver_id && ! $this->waybill_number) {
                $errors[] = __('messages.error.empty_waybill_number');
            }
            if ($this->driver_id && ! $this->evacuation_permit_number) {
                $errors[] = __('messages.error.empty_evacuation_permit_number');
            }
            if ($this->driver_id && ! $this->driver_is_paid) {
                $errors[] = __('messages.error.driver_is_not_paid');
            }
            if(! $this->user_is_paid) {
                $errors[] = __('messages.error.user_is_not_paid');
            }
            if(! $this->final_price) {
                $errors[] = __('messages.error.empty_final_price');
            }
            if(! $this->final_weight) {
                $errors[] = __('messages.error.empty_final_weight');
            }

            if ($errors) {
                throw new \Exception(implode(' - ', $errors));
            }
        }

    }

    public function stateTransitionAccessCheck(User $user, string $newState)
    {

    }

    /**
     * @param string $newState
     * @return $this
     */
    public function updateOrderStatus(string $newState)
    {
        $this->status = $newState;
        if ($newState == OrderStatus::FINISHED) {
            $this->finished_at = \Carbon\Carbon::now();
        }
        $this->save();
        event(new OrderStatusUpdated($this));
        return $this;
    }

    /**
     * @return $this|Order
     */
    public function setDriverToken()
    {
        $token = make_random_hash_with_length(10);
        if (
            ! Order::query()->where('driver_token', $token)->first()
            && ! DriverOrder::query()->where('order_token', $token)->first()
        ) {
            $this->setAttribute('driver_token', $token)->save();
            return $this;
        } else {
            return $this->setDriverToken();
        }
    }

    /**
     * @return null|string
     */
    public function getEvacuationPermitUrlAttribute()
    {
        if (! $this->evacuation_permit_image) {
            return null;
        }

        return rtrim(env('APP_URL'), '/') . '/storage/' . $this->evacuation_permit_image;
    }

    /**
     * @return null|string
     */
    public function getWaybillUrlAttribute()
    {
        if (! $this->waybill_image) {
            return null;
        }

        return rtrim(env('APP_URL'), '/') . '/storage/' . $this->waybill_image;
    }
}
