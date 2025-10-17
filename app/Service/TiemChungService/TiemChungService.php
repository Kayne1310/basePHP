<?php

namespace App\Service\TiemChungService;

use App\Models\TiemChung;
use App\Repository\TiemChungRepository\ITiemChungRepository;
use App\Service\Core\ServiceBase;

class TiemChungService extends ServiceBase implements ITiemChungService
{
    protected $repository;
    public function __construct(ITiemChungRepository $repository)
    {
        $this->repository = $repository;
    }
    public function model()
    {
        return TiemChung::class;
    }
    
     public function test()
    {
        $data= $this->repository->all()
        ->where('status','active')
        ->select('name','age','phone','price','quantity')
        ->get();
        return $data;
    }
}