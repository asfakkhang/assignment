<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $assets = Asset::where('user_id', $user->id)
            ->get()
            ->keyBy('symbol')
            ->map(function ($asset) {
                return [
                    'available' => (string) $asset->amount,
                    'locked'    => (string) $asset->locked_amount,
                    'total'     => bcadd($asset->amount, $asset->locked_amount, 8),
                ];
            });

        return response()->json([
            'usd_balance' => (string) $user->balance,
            'assets'      => $assets,
        ]);
    }
}
