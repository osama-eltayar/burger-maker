<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @method static self create(array $data)
 */
class Merchant extends Model
{
    use HasFactory;
    use Notifiable;

     protected $guarded = [];

     //########################################### Constants ################################################


     //########################################### Accessors ################################################


     //########################################### Scopes ###################################################


     //########################################### Relations ################################################


}
