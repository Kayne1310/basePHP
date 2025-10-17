<?php

namespace App\Repository\TiemChungRepository;

use App\Models\TiemChung;
use App\Repository\Core\RepositoryBase;

class TiemChungRepository extends RepositoryBase implements ITiemChungRepository
{
    
    
    // Add your specific repository methods here
    public function model()
    {
        return TiemChung::class;
    }
}