<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'orderer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
