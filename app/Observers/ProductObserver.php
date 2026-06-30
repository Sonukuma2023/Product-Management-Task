<?php

namespace App\Observers;

use App\Models\Product;
use App\Notifications\LowStockAlert;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
          
        $threshold = 5;
        if ($product->quantity <= $threshold && $product->getOriginal('quantity') > $threshold) {
    
            $creator = $product->creator;
            if ($creator) {
                $creator->notify(new LowStockAlert($product));
            }
        } 
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
