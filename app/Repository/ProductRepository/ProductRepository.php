<?php

namespace App\Repository\ProductRepository;

use App\Models\Product;
use App\Repository\Core\RepositoryBase;

class ProductRepository extends RepositoryBase implements IProductRepository
{
 
    public function model()
    {
        return Product::class;
    }
    // Add your specific repository methods here
    // public function findByEmail(string $email)
    // {
    //     return $this->model->newQuery()->where('email', $email)->first();
    // }
}