<?php

namespace App\Nova\Actions;

use App\Models\Role;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class PromoteToAdmin extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('nova.promote_user');
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
        foreach ($models as $model) {
            $admin = Role::query()->where('name', 'admin')->first();
            if ($model instanceof User) {
                $model->roles()->syncWithoutDetaching($admin->id);
                return Action::message(__('nova.promoted_to_admin'));
            }
            return Action::danger(__('nova.something_went_wrong'));
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
