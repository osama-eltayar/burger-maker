<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static self create(array $data)
 */
class Order extends Model
{
    use HasFactory;

     protected $guarded = [];

     //########################################### Constants ################################################


     //########################################### Accessors ################################################


     //########################################### Scopes ###################################################


     //########################################### Relations ################################################
    public function products():BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

}
