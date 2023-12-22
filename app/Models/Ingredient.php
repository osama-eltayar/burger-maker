<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static self create(array $data)
 */
class Ingredient extends Model
{
    use HasFactory;

     protected $guarded = [];

     //########################################### Constants ################################################


     //########################################### Accessors ################################################
    public function shouldNotifyMerchant()
    {
        return !$this->merchant_notified_at && $this->current_stock < $this->needed_stock / 2 ;
    }

     //########################################### Scopes ###################################################


     //########################################### Relations ################################################
    public function merchant():BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }


}
