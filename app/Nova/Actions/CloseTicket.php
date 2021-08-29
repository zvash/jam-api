<?php

namespace App\Nova\Actions;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Textarea;

class CloseTicket extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('nova.close_ticket');
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
            return Action::danger(__('messages.error.just_one_resource_error', ['resource' => __('nova.Ticket')]));
        }
        foreach ($models as $model) {
            if ($model instanceof Ticket) {
                $model->is_open = false;
                if ($fields->response) {
                    $model->response = $fields->response;
                }
                $model->save();
                return Action::message(__('messages.success.successful_operation'));
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
        return [
            Textarea::make(__('nova.response'), 'response')
                ->nullable(),
        ];
    }
}
