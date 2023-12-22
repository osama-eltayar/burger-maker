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
    public function isALLIngredientsAvailable(int $quantity = 1):bool
    {
        foreach ($this->ingredients as $ingredient){
            if (!($ingredient->current_stock >= $ingredient->pivot->quantity *  $quantity ))
                return false;
        }

        return true ;
    }


     //########################################### Scopes ###################################################


     //########################################### Relations ################################################
    public function ingredients():BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class,'product_ingredient')
            ->withPivot('quantity')
            ->withTimestamps();
    }

}
