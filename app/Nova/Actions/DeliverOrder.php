<?php

namespace App\Nova\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class DeliverOrder extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('nova.deliver_order');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if (count($models) !== 1) {
            return Action::danger(__('messages.error.just_one_resource_error', ['resource' => __('nova.Order')]));
        }
        foreach ($models as $model) {
            if ($model instanceof Order) {
                $newState = OrderStatus::DELIVERED;
                if ($model->stateTransitionIsPermitted($newState)) {
                    try {
                        $model->statePrerequisitesIsMet($newState);
                    } catch (\Exception $exception) {
                        $message = $exception->getMessage();
                        return Action::danger($message);
                    }
                    $model->updateOrderStatus($newState);
                    return Action::message(__('messages.success.successful_operation'));
                } else {

                }
            }
            return Action::danger(__('messages.error.not_possible_operation'));
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
