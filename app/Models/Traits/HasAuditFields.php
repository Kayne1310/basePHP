<?php

namespace App\Models\Traits;

trait HasAuditFields
{
    protected $fillable = [
        'entity',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
     public function initializeHasAuditFields(): void
    {
        $this->fillable = array_unique(array_merge($this->fillable, $this->auditFields));
    }
}
