<?php

namespace App\Nova\Actions;

use App\Models\Config;
use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laraning\NovaTimeField\TimeField;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\KeyValue;
use Michielfb\Time\Time;
use Milanjam\KeyTwoValues\KeyTwoValues;

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
        $updatePriceTime = $fields->update_price_time;
        $prices = $fields->price_list;
        foreach ($prices as $price) {
            Item::query()->where('name', $price['key'])
                ->update(['price' => $price['value'], 'next_price' => $price['value2']]);
        }
        $config = Config::where('key', 'update_price_time')->first();
        if ($config) {
            $config->value = $updatePriceTime;
            $config->save();
        } else {
            Config::query()->create(['key' => 'update_price_time', 'value' => $updatePriceTime]);
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
        $items = Item::all();
        $pricesByName = [];
        foreach ($items as $item) {
            $pricesByName[$item->name] = [$item->price, $item->next_price ?? $item->price];
        }
        $config = Config::where('key', 'update_price_time')->first();
        $updatePriceTime = '00:00';
        if ($config) {
            $updatePriceTime = $config->value;
        }
        return [
            Time::make(__('nova.update_price_time'), 'update_price_time')
                ->format('HH:mm')
                ->withSteps(15)
                ->default($updatePriceTime),
            KeyTwoValues::make('لیست قیمت', 'price_list')
                ->rules('json')
                ->disableAddingRows()
                ->disableEditingKeys()
                ->disableDeletingRows()
                ->keyLabel(__('nova.name'))
                ->valueLabel(__('nova.current_price'))
                ->value2Label(__('nova.next_price'))
                ->default(function() use ($pricesByName) {
                    return $pricesByName;
                }),
        ];
    }
}
