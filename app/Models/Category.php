<?php

namespace App\Models;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    protected $table = 'categories';
    
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'description'
    ];
    
    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];
    
    // Add your relationships here
    // public function someRelation(): HasMany
    // {
    //     return $this->hasMany(SomeModel::class);
    // }
}