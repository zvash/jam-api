<?php

namespace App\Nova\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Http\Request;

class CloseOrder extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('nova.close_order');
    }

    public $model = null;

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
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

                if (!empty($fields->user_is_paid)) {
                    $model->user_is_paid = $fields->user_is_paid;
                }
                if (!empty($fields->waybill_number)) {
                    $model->waybill_number = $fields->waybill_number;
                }
                if (!empty($fields->evacuation_permit_number)) {
                    $model->evacuation_permit_number = $fields->evacuation_permit_number;
                }
                if (!empty($fields->driver_is_paid)) {
                    $model->driver_is_paid = $fields->driver_is_paid;
                }
                $model->save();

                $newState = OrderStatus::FINISHED;
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
        $fields = [];

        $fields[] = Text::make(__('nova.waybill_number'), 'waybill_number')
            ->default($this->model ? $this->model->waybill_number : null);

        $fields[] = Text::make(__('nova.evacuation_permit_number'), 'evacuation_permit_number')
            ->default($this->model ? $this->model->evacuation_permit_number : null);


        $fields[] = Boolean::make(__('nova.driver_is_paid'), 'driver_is_paid')
            ->default($this->model ? $this->model->driver_is_paid : null);


        $fields[] = Boolean::make(__('nova.user_is_paid'), 'user_is_paid')
            ->default($this->model ? $this->model->user_is_paid : null);
        return $fields;
    }
}
