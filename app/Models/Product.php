<?php

namespace App\Models;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    use HasFactory;
  
    protected $table = 'product';

    
     protected $fillable = [
        'entity',
        'name',
        'price',
        'stock',
        'description',
    ];


    protected $casts = [
        'name' => 'string',
        'price' => 'float',
        'stock' => 'integer',
        'description' => 'string',
    ];

}
