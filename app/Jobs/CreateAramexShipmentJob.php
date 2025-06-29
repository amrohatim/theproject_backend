<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\AramexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateAramexShipmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    protected $order;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @param  \App\Services\AramexService  $aramexService
     * @return void
     */
    public function handle(AramexService $aramexService)
    {
        try {
            Log::info('Creating Aramex shipment for order', ['order_id' => $this->order->id]);
            
            // Call the service to create a shipment via Aramex API
            $result = $aramexService->createShipment($this->order);
            
            if ($result['success']) {
                // Update order shipping status
                $this->order->shipping_status = 'processing';
                $this->order->save();
                
                Log::info('Aramex shipment created successfully', [
                    'order_id' => $this->order->id,
                    'awb_number' => $result['awb_number']
                ]);
                
                // In a real application, you might want to notify the customer here
                // that their shipment has been arranged
            } else {
                Log::error('Failed to create Aramex shipment', [
                    'order_id' => $this->order->id,
                    'message' => $result['message']
                ]);
                
                // If this is the last retry, mark the order as having an issue
                if ($this->attempts() >= $this->tries) {
                    $this->order->shipping_status = 'failed';
                    $this->order->save();
                    
                    // In a real application, you might want to notify an admin here
                }
            }
        } catch (\Exception $e) {
            Log::error('Exception creating Aramex shipment', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If this is the last retry, mark the order as having an issue
            if ($this->attempts() >= $this->tries) {
                $this->order->shipping_status = 'failed';
                $this->order->save();
            }
            
            // Rethrow the exception to trigger a retry
            throw $e;
        }
    }
}
