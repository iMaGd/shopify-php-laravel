<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopifySession extends Model
{
    use HasFactory;

     /**
     * Get the user that owns the shop.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
