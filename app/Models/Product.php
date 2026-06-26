<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AlertasStock;
use App\Services\AlertasStockService;
use App\Models\MovimientoInventario;

class Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'id';

    protected $fillable = [

        'name',
        'price',
        'stock',
        'stock_minimo',
        'status',
        'categories_id',
        'brand_id',
        'image',
        'description'

    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset(
                'storage/' .
                $this->image
            )
            : null;
    }

    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'categories_id',
            'categories_id'
        );
    }

    public function brand()
    {
        return $this->belongsTo(
            Brand::class,
            'brand_id',
            'brand_id'
        );
    }

    // NUEVO
    public function movimientos()
    {
        return $this->hasMany(
            MovimientoInventario::class,
            'product_id',
            'id'
        );
    }

    protected static function booted()
    {
        static::updated(
            function ($product) {

                if (
                    $product->isDirty(
                        'stock'
                    )
                ) {

                    (
                        new AlertasStockService()
                    )->revisarStock(
                        $product
                    );

                }

            }
        );
    }
}