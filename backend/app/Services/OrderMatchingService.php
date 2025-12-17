<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Asset;
use App\Models\Trade;
use App\Events\OrderMatched;

class OrderMatchingService
{
    const COMMISSION_RATE = '0.015'; // 1.5%

    /**
     * Match a given order with a counter order
     *
     * @param Order $order
     * @return bool|null
     */
    public function match(Order $order)
    {
        return DB::transaction(function () use ($order) {

            // Lock incoming order for update
            $order = Order::where('id', $order->id)
                ->lockForUpdate()
                ->first();

            // Skip if order is not open
            if ($order->status !== 1) {
                return null;
            }

            // Find a matching counter order
            $counter = Order::where('symbol', $order->symbol)
                ->where('side', $order->side === 'buy' ? 'sell' : 'buy')
                ->where('status', 1)
                ->where(function ($q) use ($order) {
                    if ($order->side === 'buy') {
                        $q->where('price', '<=', $order->price);
                    } else {
                        $q->where('price', '>=', $order->price);
                    }
                })
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            // If no counter order found, return
            if (!$counter) {
                return null;
            }

            // Only exact match allowed (no partial fills)
            if (bccomp((string)$order->amount, (string)$counter->amount, 8) !== 0) {
                return null;
            }

            $price = $counter->price;
            $amount = $order->amount;
            $usdValue = bcmul((string)$price, (string)$amount, 8);   // USD value
            $commission = bcmul($usdValue, self::COMMISSION_RATE, 8);

            // Determine buyer and seller
            $buyOrder  = $order->side === 'buy' ? $order : $counter;
            $sellOrder = $order->side === 'sell' ? $order : $counter;

            // -------------------- Buyer: receive asset --------------------
            $buyerAsset = Asset::firstOrCreate(
                ['user_id' => $buyOrder->user_id, 'symbol' => $order->symbol],
                ['amount' => '0', 'locked_amount' => '0']
            );
            $buyerAsset->amount = bcadd((string)($buyerAsset->amount ?? '0'), (string)$amount, 8);
            $buyerAsset->save();

            // -------------------- Seller: release locked asset --------------------
            $sellerAsset = Asset::firstOrCreate(
                ['user_id' => $sellOrder->user_id, 'symbol' => $order->symbol],
                ['amount' => '0', 'locked_amount' => '0']
            );
            $sellerAsset->locked_amount = bcsub((string)($sellerAsset->locked_amount ?? '0'), (string)$amount, 8);
            $sellerAsset->save();

            // -------------------- Seller: receive USD --------------------
            DB::table('users')->where('id', $sellOrder->user_id)->increment(
                'balance',
                bcsub($usdValue, $commission, 8)
            );

            // -------------------- Mark orders filled --------------------
            $order->update(['status' => 2]);
            $counter->update(['status' => 2]);

            // -------------------- Record trade --------------------
            Trade::create([
                'buy_order_id'  => $buyOrder->id,
                'sell_order_id' => $sellOrder->id,
                'symbol'        => $order->symbol,
                'price'         => $price,
                'amount'        => $amount,
                'usd_value'     => $usdValue,
                'commission'    => $commission,
            ]);

            // -------------------- Fire real-time events --------------------
            event(new OrderMatched($buyOrder->user_id, [
                'type'   => 'buy',
                'symbol' => $order->symbol,
                'amount' => $amount,
                'price'  => $price,
            ]));

            event(new OrderMatched($sellOrder->user_id, [
                'type'   => 'sell',
                'symbol' => $order->symbol,
                'amount' => $amount,
                'price'  => $price,
            ]));

            return true;
        });
    }
}
