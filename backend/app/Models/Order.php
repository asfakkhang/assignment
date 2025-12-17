<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'symbol', 'side', 'price', 'amount', 'status'];

    const OPEN = 1;
    const FILLED = 2;
    const CANCELLED = 3;
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}