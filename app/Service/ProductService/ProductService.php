<?php

namespace App\Service\ProductService;

use App\Repository\ProductRepository\IProductRepository;
use App\Service\Core\ServiceBase;

class ProductService extends ServiceBase implements IProductService
{
    public function __construct(IProductRepository $repository)
    {
        parent::__construct($repository);
    }
    
    // Add your specific service methods here
    // public function processData(array $data)
    // {
    //     // Your business logic here
    //     return $this->repository->create($data);
    // }
}