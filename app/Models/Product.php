<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static self create(array $data)
 */
class Product extends Model
{
    use HasFactory;

    const BURGER = 'burger';
    protected $guarded = [];

     //########################################### Constants ################################################


     //########################################### Accessors ################################################


     //########################################### Scopes ###################################################


     //########################################### Relations ################################################
    public function ingredients():BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class,ProductIngredient::class)->withPivot('quantity')->withTimestamps();
    }

}
