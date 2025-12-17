<?php
// It is only for manually testing on local system.
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderMatchingService; 
use App\Models\Order;

class MatchOrders extends Command
{
    protected $signature = 'orders:match';
    protected $description = 'Match all open orders';

    protected $matcher;

    public function __construct(OrderMatchingService $matcher)
    {
        parent::__construct();
        $this->matcher = $matcher;
    }

    public function handle()
    {
        $orders = Order::where('status', 1)->get();

        foreach ($orders as $order) {
            $this->matcher->match($order);
        }

        $this->info('Orders matched!');
    }
}
