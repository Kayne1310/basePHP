<?php

namespace App\Providers;

use App\Repository\Core\RepositoryBase;
use App\Repository\Core\IRepositoryBase;
use App\Service\Core\ServiceBase;
use App\Service\Core\IServiceBase;
use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind tay cho Core (ổn định)
        $this->app->bind(IRepositoryBase::class, RepositoryBase::class);
        $this->app->bind(IServiceBase::class, ServiceBase::class);
        
        // Auto-bind cho các folder con trong app/Repository ngoại trừ Core
        $this->autoBindRepositoriesExceptCore('App\\Repository', app_path('Repository'));
        
        // Auto-bind cho các folder con trong app/Service ngoại trừ Core
        $this->autoBindServicesExceptCore('App\\Service', app_path('Service'));
       

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Auto-bind theo convention: trong mỗi namespace con (trừ Core),
     * nếu có {Name}Repository và I{Name}Repository cùng namespace thì bind vào nhau.
     */
    protected function autoBindRepositoriesExceptCore(string $baseNamespace, string $basePath): void
    {
        if (!is_dir($basePath)) {
            return;
        }

        // Duyệt tất cả file .php dưới app/Repository
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace([$basePath . DIRECTORY_SEPARATOR, '.php'], '', $file->getPathname());
            $relative = str_replace(['/', '\\'], '\\', $relative);

            // Bỏ qua Core/*
            if (str_starts_with($relative, 'Core\\')) {
                continue;
            }

            $class = $baseNamespace . '\\' . $relative;

            if (!class_exists($class)) {
                continue;
            }

            $ref = new ReflectionClass($class);
            if ($ref->isAbstract() || $ref->isInterface()) {
                continue;
            }

            $short = $ref->getShortName();
            // Chỉ xử lý *Repository
            if (!str_ends_with($short, 'Repository')) {
                continue;
            }

            $domain = substr($short, 0, -strlen('Repository'));
            $iface = $ref->getNamespaceName() . '\\I' . $domain . 'Repository';

            if (interface_exists($iface)) {
                $this->app->bind($iface, $class);
            }
        }
    }

    /**
     * Auto-bind theo convention: trong mỗi namespace con (trừ Core),
     * nếu có {Name}Service và I{Name}Service cùng namespace thì bind vào nhau.
     */
    protected function autoBindServicesExceptCore(string $baseNamespace, string $basePath): void
    {
        if (!is_dir($basePath)) {
            return;
        }

        // Duyệt tất cả file .php dưới app/Service
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace([$basePath . DIRECTORY_SEPARATOR, '.php'], '', $file->getPathname());
            $relative = str_replace(['/', '\\'], '\\', $relative);

            // Bỏ qua Core/*
            if (str_starts_with($relative, 'Core\\')) {
                continue;
            }

            $class = $baseNamespace . '\\' . $relative;

            if (!class_exists($class)) {
                continue;
            }

            $ref = new ReflectionClass($class);
            if ($ref->isAbstract() || $ref->isInterface()) {
                continue;
            }

            $short = $ref->getShortName();
            // Chỉ xử lý *Service
            if (!str_ends_with($short, 'Service')) {
                continue;
            }

            $domain = substr($short, 0, -strlen('Service'));
            $iface = $ref->getNamespaceName() . '\\I' . $domain . 'Service';

            if (interface_exists($iface)) {
                $this->app->bind($iface, $class);
            }
        }
    }
}
