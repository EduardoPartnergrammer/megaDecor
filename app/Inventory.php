<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'servicio',
        'cantidad',
        'imagen',
        'precioUnitario',
    ];
}
