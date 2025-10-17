<?php

namespace App\Models;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TiemChung extends BaseModel
{
    protected $table = 'tiem_chungs';
    
    protected $fillable = [
        
    ];
    
    protected $casts = [
        'id' => 'string',
        
    ];
    
    // Add your relationships here
    // public function someRelation(): HasMany
    // {
    //     return $this->hasMany(SomeModel::class);
    // }
}