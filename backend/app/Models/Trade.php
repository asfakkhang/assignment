<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = ['buy_order_id','sell_order_id','symbol','price','amount','usd_value','commission'];

    // Optional: cast numeric fields
    protected $casts = [
        'price' => 'decimal:8',
        'amount' => 'decimal:8',
        'usd_value' => 'decimal:8',
        'commission' => 'decimal:8',
    ];

    public function buyOrder() {
        return $this->belongsTo(Order::class,'buy_order_id')->withDefault();
    }

    public function sellOrder() {
        return $this->belongsTo(Order::class,'sell_order_id')->withDefault();
    }
}
