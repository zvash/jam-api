<?php

namespace App\Nova\Actions;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\KeyValue;

class UpdatePriceList extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('nova.update_price_list');
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
        $prices = $fields->price_list;
        foreach ($prices as $name => $price) {
            Item::query()->where('name', $name)
                ->update(['price' => $price]);
        }
        $url = config('nova.path') . '/resources/items';
        return Action::redirect($url);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            KeyValue::make('لیست قیمت', 'price_list')
                ->rules('json')
                ->disableAddingRows()
                ->disableEditingKeys()
                ->disableDeletingRows()
                ->default(function() {
                    return  Item::all()->pluck('price', 'name')->all();
                }),
        ];
    }
}
