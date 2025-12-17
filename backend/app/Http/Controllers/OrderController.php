<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Asset;
use App\Services\OrderMatchingService;
use App\Events\OrderMatched;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $symbol = $request->query('symbol'); // ?symbol=BTC

        $query = Order::where('user_id', $user->id)->latest();

        if ($symbol) {
            $query->where('symbol', $symbol);
            $query->where('status', 1);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully',
            'data' => [
                'orders' => $orders,
            ],
        ], 200);
    }

    public function store(Request $request, OrderMatchingService $matcher)
    {
        $request->validate([
            'symbol' => 'required|in:BTC,ETH',
            'side'   => 'required|in:buy,sell',
            'price'  => 'required|numeric|min:0.0001',
            'amount' => 'required|numeric|min:0.0001',
        ]);

        $user = $request->user();

        try {
            $order = DB::transaction(function () use ($request, $user) {

                if ($request->side === 'buy') {
                    $cost = bcmul($request->price, $request->amount, 8);
                    $lockedUser = $user->lockForUpdate()->first();
                    if (bccomp($lockedUser->balance, $cost, 8) < 0) {
                        abort(400, 'Insufficient USD balance');
                    }
                    $lockedUser->balance = bcsub($lockedUser->balance, $cost, 8);
                    $lockedUser->save();
                }

                if ($request->side === 'sell') {
                    $asset = Asset::where('user_id', $user->id)
                        ->where('symbol', $request->symbol)
                        ->lockForUpdate()
                        ->first();

                    if (!$asset || bccomp($asset->amount, $request->amount, 8) < 0) {
                        abort(400, 'Insufficient asset balance');
                    }

                    $asset->amount = bcsub($asset->amount, $request->amount, 8);
                    $asset->locked_amount = bcadd($asset->locked_amount, $request->amount, 8);
                    $asset->save();
                }

                return Order::create([
                    'user_id' => $user->id,
                    'symbol'  => $request->symbol,
                    'side'    => $request->side,
                    'price'   => $request->price,
                    'amount'  => $request->amount,
                    'status'  => 1,
                ]);
            });

            // Match order and fire real-time events internally
            $matcher->match($order);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order' => $order->fresh(),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        DB::transaction(function () use ($order, $user) {
            if ($order->side === 'buy') {
                $user->balance = bcadd($user->balance, bcmul($order->price, $order->amount, 8), 8);
                $user->save();
            } else {
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $order->symbol)
                    ->lockForUpdate()
                    ->first();

                if ($asset) {
                    $asset->amount = bcadd($asset->amount, $order->amount, 8);
                    $asset->locked_amount = bcsub($asset->locked_amount, $order->amount, 8);
                    $asset->save();
                }
            }

            $order->status = 3; // cancelled
            $order->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => [
                'order' => $order->fresh()
            ]
        ]);
    }
}
