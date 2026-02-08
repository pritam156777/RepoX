<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'admin_id', 'name', 'photo', 'uuid'
    ];
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Automatically generate UUID when creating a category
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Use UUID for route model binding
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relationship: Category has many products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

