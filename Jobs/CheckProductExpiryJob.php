<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckProductExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $products = Product::whereNotNull('expiry_date')
            ->where('is_available', true)
            ->get();

        $disabledCount = 0;

        foreach ($products as $product) {
            if ($product->shouldBeDisabledByExpiry()) {
                $product->updateQuietly(['is_available' => false]);
                $disabledCount++;
                
                Log::info("Produto automaticamente desabilitado por validade", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'pharmacy_id' => $product->pharmacy_id,
                    'expiry_date' => $product->expiry_date,
                ]);
            }
        }

        if ($disabledCount > 0) {
            Log::info("Verificação automática de validade concluída", [
                'disabled_products' => $disabledCount
            ]);
        }
    }
}
