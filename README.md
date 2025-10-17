# Command Generate 

## 1) Tạo migration kế thừa các trường audit
```bash
php artisan make:audit-migration create_products_table --create --table=products
```

- Lệnh trên tạo 1 migration có sẵn các trường audit: `id (UUID)`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_at`, `deleted_by`.
- Bạn CHỈ thêm các trường nghiệp vụ trong method `customFields()` của file migration vừa tạo, ví dụ:
```php
protected function customFields(\Illuminate\Database\Schema\Blueprint $table)
{
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->boolean('is_active')->default(true);
    $table->string('sku')->unique();
}
```

Lưu ý: KHÔNG thêm lại các trường audit (`created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`, `id`) trong `customFields()`.

## 2) Chạy migration
```bash
php artisan migrate
```

## 3) Sinh DDD structure (Model, Repository, Service, auto-bind)
```bash
php artisan make:ddd Product
```
- Tạo các file:
  - `app/Models/Product.php` (kế thừa `App\Models\Core\BaseModel`)
  - `app/Repository/ProductRepository/IProductRepository.php`
  - `app/Repository/ProductRepository/ProductRepository.php`
  - `app/Service/ProductService/IProductService.php`
  - `app/Service/ProductService/ProductService.php`
- Tự động cập nhật binding trong `app/Providers/AppServiceProvider.php`.
- Model sẽ tự sinh `fillable` và `casts` bằng cách đọc các trường từ migration.

## 4) Cập nhật autoload khi có file/namespace mới
```bash
composer dump-autoload
```

## 5) Sử dụng trong Controller/Service (Constructor DI)
```php
use App\Service\ProductService\IProductService;

class ProductController extends Controller
{
    public function __construct(private IProductService $productService) {}
}
```

## Convention (quy ước)
- Repository: `App\Repository\{Name}Repository\I{Name}Repository` ↔ `App\Repository\{Name}Repository\{Name}Repository`
- Service: `App\Service\{Name}Service\I{Name}Service` ↔ `App\Service\{Name}Service\{Name}Service`
- Auto-binding đã được cấu hình trong `AppServiceProvider` cho các thư mục không phải `Core`.

## Lỗi thường gặp và cách sửa
- Báo lỗi “duplicate column created_by”: Bạn đã thêm lại các trường audit trong `customFields()`. Hãy xoá `created_by`, `updated_by`, `deleted_by` (và các audit khác) khỏi `customFields()`.
- Cảnh báo autoload/namespace: Đảm bảo namespace đúng PSR-4, ví dụ dưới `app/Service/Core` dùng `namespace App\Service\Core;`.

## Ghi chú
- `BaseModel` tự sinh `UUID` cho `id`, tự gán `created_by`, `updated_by`, `deleted_by` nếu có user đăng nhập, và hỗ trợ soft delete.
- Bạn có thể chạy lại generator sau khi cập nhật migration để model phản ánh các trường mới.
