<?php

namespace App\Jobs;

use App\Models\Config;
use App\Models\Item;
use App\Models\ItemPriceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePriceList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $config = Config::where('key', 'update_price_time')->first();
        $now = date('H:i');
        if ($config && $config->value == $now) {
            $items = Item::all();
            foreach ($items as $item) {
                ItemPriceHistory::query()
                    ->create([
                        'item_id' => $item->id,
                        'price' => $item->price,
                    ]);
                if ($item->next_price) {
                    $item->last_price = $item->price;
                    $item->price = $item->next_price;
                    $item->save();
                }
            }
        }
    }
}
