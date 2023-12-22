<?php

namespace App\Jobs;

use App\Models\Ingredient;
use App\Notifications\BuyBackIngredient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CheckIngredientStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Collection $affectedIngredients)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->affectedIngredients as $ingredient)
            $this->NotifyMerchantIfNeeded($ingredient);

//        dd('done');
    }

    private function NotifyMerchantIfNeeded(Ingredient $ingredient)
    {

        if (!$ingredient->shouldNotifyMerchant())
            return ;

        $ingredient->merchant->notify(new BuyBackIngredient($ingredient));
        $ingredient->update(['merchant_notified_at' => now()]);
    }
}
