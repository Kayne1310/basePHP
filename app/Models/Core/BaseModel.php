<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Auto-set created_by if not set
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            // Auto-set updated_by if not set
            if (empty($model->updated_by) && auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            // Auto-set deleted_by if not set
            if (empty($model->deleted_by) && auth()->check()) {
                $model->deleted_by = auth()->id();
            }
        });
    }

    /**
     * Get the name of the "created by" column.
     */
    public function getCreatedByColumn()
    {
        return 'created_by';
    }

    /**
     * Get the name of the "updated by" column.
     */
    public function getUpdatedByColumn()
    {
        return 'updated_by';
    }

    /**
     * Get the name of the "deleted by" column.
     */
    public function getDeletedByColumn()
    {
        return 'deleted_by';
    }

    /**
     * Get the user who created this model.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user who last updated this model.
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this model.
     */
    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }
}