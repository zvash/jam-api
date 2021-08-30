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
use Laravel\Nova\Fields\Boolean;

class ChooseUserType extends Action
{
    use InteractsWithQueue, Queueable;

    protected $model;

    public function name()
    {
        return __('nova.choose_user_type');
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields $fields
     * @param  \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $admin = Role::query()->where('name', 'admin')->first();
            $driver = Role::query()->where('name', 'courier')->first();
            if ($model instanceof User) {
                $isAdmin = $model->isAdmin();
                $isDriver = $model->isCourier();
                $shouldBeVerified = false;
                if (!$fields->is_admin) {
                    $model->roles()->detach($admin->id);
                } else if (!$isAdmin) {
                    $model->roles()->attach($admin->id);
                    $shouldBeVerified = true;
                }
                if (!$fields->is_driver) {
                    $model->roles()->detach($driver->id);
                } else if (!$isDriver) {
                    $model->roles()->attach($driver->id);
                    $shouldBeVerified = true;
                }
                if ($shouldBeVerified && !$model->phone_verified_at) {
                    $model->phone_verified_at = \Carbon\Carbon::now();
                    $model->save();
                }
                return Action::message(__('messages.success.successful_operation'));
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
        $isAdmin = $this->model->isAdmin();
        $isDriver = $this->model->isCourier();
        $isSeller = $this->model->isSeller();

        return [
            Boolean::make(__('nova.user_is_admin'), 'is_admin')->default($isAdmin),
            Boolean::make(__('nova.user_is_driver'), 'is_driver')->default($isDriver),
            Boolean::make(__('nova.user_is_seller'), 'is_seller')
                ->withMeta(['extraAttributes' => [
                    'readonly' => true
                ]])
                ->default($isSeller),
        ];
    }
}
