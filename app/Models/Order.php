<?php

namespace App\Models; 

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'user_id', 'payment_method', 'status', 'payment_status',
    ];

    public function store()
    {
        return $this->BelongsTo(store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)
        ->withDefault([
            'name'=>'Guest Customer',
        ]);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'oredr_items','order_id','product_id','id','id')
        ->using(OrderItem::class)
        ->as('order_item')
        ->withPivot([
            'product_name','price','quantity','options',
        ]);
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class,'order_id','id')
        ->where('type','=','billing');
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id')
            ->where('type', '=', 'shipping');
    }


    protected static function booted()
    {
        static::creating(function(Order $oredr){
            $oredr->number=Order::getNextOrderNumber();
        });
    }


    public static function getNextOrderNumber()
    {
        // SELECT MAX(number) FROM orders
        $year =  Carbon::now()->year;
        $number = Order::whereYear('created_at', $year)->max('number');
        if ($number) {
            return $number + 1;
        }
        return $year . '0001';
    }

}
