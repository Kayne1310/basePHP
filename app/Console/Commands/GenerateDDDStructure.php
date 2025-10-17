<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateDDDStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ddd {name : The name of the model} {--migration : Create migration file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate DDD structure: Model, Repository, Service with auto-binding';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $createMigration = $this->option('migration');
        
        $this->info("Generating DDD structure for: {$name}");
        
        // Generate Model
        $this->generateModel($name);
        
        // Generate Repository Interface
        $this->generateRepositoryInterface($name);
        
        // Generate Repository
        $this->generateRepository($name);
        
        // Generate Service Interface
        $this->generateServiceInterface($name);
        
        // Generate Service
        $this->generateService($name);
        
        // Generate Migration if requested
        if ($createMigration) {
            $this->generateMigration($name);
        }
        
        // Update AppServiceProvider
        $this->updateServiceProvider($name);
        
        $this->info("DDD structure generated successfully!");
        $this->info("Run: composer dump-autoload");
        
        return Command::SUCCESS;
    }
    
    private function generateModel($name)
    {
        $modelName = Str::studly($name);
        $tableName = Str::snake(Str::plural($name));
        
        $modelPath = app_path("Models/{$modelName}.php");
        
        if (File::exists($modelPath)) {
            $this->warn("Model {$modelName} already exists!");
            return;
        }
        
        // Try to find migration for this table
        $migrationFields = $this->getFieldsFromMigration($tableName);
        
        $content = "<?php

namespace App\Models;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class {$modelName} extends BaseModel
{
    protected \$table = '{$tableName}';
    
    protected \$fillable = [
        {$migrationFields['fillable']}
    ];
    
    protected \$casts = [
        'id' => 'string',
        {$migrationFields['casts']}
    ];
    
    // Add your relationships here
    // public function someRelation(): HasMany
    // {
    //     return \$this->hasMany(SomeModel::class);
    // }
}";
        
        File::put($modelPath, $content);
        $this->info("Model created: {$modelPath}");
    }
    
    /**
     * Get fields from migration file
     */
    private function getFieldsFromMigration($tableName)
    {
        $fillable = [];
        $casts = [];
        
        // Find migration file for this table
        $migrationFiles = glob(database_path('migrations/*.php'));
        
        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            
            // Check if this migration creates the table we want
            if (strpos($content, "'{$tableName}'") !== false || 
                strpos($content, "\"{$tableName}\"") !== false) {
                
                // Extract fields from migration
                $fields = $this->extractFieldsFromMigrationContent($content);
                $fillable = $fields['fillable'];
                $casts = $fields['casts'];
                break;
            }
        }
        
        return [
            'fillable' => implode(",\n        ", $fillable),
            'casts' => implode(",\n        ", $casts)
        ];
    }
    
    /**
     * Extract fields from migration content
     */
    private function extractFieldsFromMigrationContent($content)
    {
        $fillable = [];
        $casts = [];
        
        // Common field patterns
        $fieldPatterns = [
            'string' => '/\$table->string\([\'"]([^\'"]+)[\'"]\)/',
            'integer' => '/\$table->integer\([\'"]([^\'"]+)[\'"]\)/',
            'boolean' => '/\$table->boolean\([\'"]([^\'"]+)[\'"]\)/',
            'text' => '/\$table->text\([\'"]([^\'"]+)[\'"]\)/',
            'decimal' => '/\$table->decimal\([\'"]([^\'"]+)[\'"]/',
            'date' => '/\$table->date\([\'"]([^\'"]+)[\'"]\)/',
            'datetime' => '/\$table->datetime\([\'"]([^\'"]+)[\'"]\)/',
            'json' => '/\$table->json\([\'"]([^\'"]+)[\'"]\)/',
        ];
        
        foreach ($fieldPatterns as $type => $pattern) {
            preg_match_all($pattern, $content, $matches);
            
            foreach ($matches[1] as $field) {
                // Skip audit fields (already in BaseModel)
                if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'])) {
                    continue;
                }
                
                $fillable[] = "'{$field}'";
                
                // Add appropriate casts
                switch ($type) {
                    case 'boolean':
                        $casts[] = "'{$field}' => 'boolean'";
                        break;
                    case 'integer':
                    case 'decimal':
                        $casts[] = "'{$field}' => 'integer'";
                        break;
                    case 'json':
                        $casts[] = "'{$field}' => 'array'";
                        break;
                    case 'date':
                    case 'datetime':
                        $casts[] = "'{$field}' => 'datetime'";
                        break;
                }
            }
        }
        
        // If no fields found, add placeholder
        if (empty($fillable)) {
            $fillable[] = "// Add your fillable fields here";
        }
        
        if (empty($casts)) {
            $casts[] = "// Add your casts here";
        }
        
        return [
            'fillable' => $fillable,
            'casts' => $casts
        ];
    }
    
    private function generateRepositoryInterface($name)
    {
        $modelName = Str::studly($name);
        $interfaceName = "I{$modelName}Repository";
        
        $dir = app_path("Repository/{$modelName}Repository");
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        
        $interfacePath = "{$dir}/{$interfaceName}.php";
        
        if (File::exists($interfacePath)) {
            $this->warn("Repository Interface {$interfaceName} already exists!");
            return;
        }
        
        $content = "<?php

namespace App\Repository\\{$modelName}Repository;

use App\Repository\Core\IRepositoryBase;

interface {$interfaceName} extends IRepositoryBase
{
    // Add your specific repository methods here
    // public function findByEmail(string \$email);
    // public function findByStatus(string \$status);
}";
        
        File::put($interfacePath, $content);
        $this->info("Repository Interface created: {$interfacePath}");
    }
    
    private function generateRepository($name)
    {
        $modelName = Str::studly($name);
        $interfaceName = "I{$modelName}Repository";
        $repositoryName = "{$modelName}Repository";
        
        $dir = app_path("Repository/{$modelName}Repository");
        $repositoryPath = "{$dir}/{$repositoryName}.php";
        
        if (File::exists($repositoryPath)) {
            $this->warn("Repository {$repositoryName} already exists!");
            return;
        }
        
        $content = "<?php

namespace App\Repository\\{$modelName}Repository;

use App\Models\\{$modelName};
use App\Repository\Core\RepositoryBase;

class {$repositoryName} extends RepositoryBase implements {$interfaceName}
{
    public function __construct({$modelName} \$model)
    {
        parent::__construct(\$model);
    }
    
    // Add your specific repository methods here
    // public function findByEmail(string \$email)
    // {
    //     return \$this->model->newQuery()->where('email', \$email)->first();
    // }
}";
        
        File::put($repositoryPath, $content);
        $this->info("Repository created: {$repositoryPath}");
    }
    
    private function generateServiceInterface($name)
    {
        $modelName = Str::studly($name);
        $interfaceName = "I{$modelName}Service";
        
        $dir = app_path("Service/{$modelName}Service");
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        
        $interfacePath = "{$dir}/{$interfaceName}.php";
        
        if (File::exists($interfacePath)) {
            $this->warn("Service Interface {$interfaceName} already exists!");
            return;
        }
        
        $content = "<?php

namespace App\Service\\{$modelName}Service;

use App\Service\Core\IServiceBase;

interface {$interfaceName} extends IServiceBase
{
    // Add your specific service methods here
    // public function processData(array \$data);
    // public function validateData(array \$data);
}";
        
        File::put($interfacePath, $content);
        $this->info("Service Interface created: {$interfacePath}");
    }
    
    private function generateService($name)
    {
        $modelName = Str::studly($name);
        $interfaceName = "I{$modelName}Service";
        $serviceName = "{$modelName}Service";
        $repositoryInterfaceName = "I{$modelName}Repository";
        
        $dir = app_path("Service/{$modelName}Service");
        $servicePath = "{$dir}/{$serviceName}.php";
        
        if (File::exists($servicePath)) {
            $this->warn("Service {$serviceName} already exists!");
            return;
        }
        
        $content = "<?php

namespace App\Service\\{$modelName}Service;

use App\Repository\\{$modelName}Repository\\{$repositoryInterfaceName};
use App\Service\Core\ServiceBase;

class {$serviceName} extends ServiceBase implements {$interfaceName}
{
    public function __construct({$repositoryInterfaceName} \$repository)
    {
        parent::__construct(\$repository);
    }
    
    // Add your specific service methods here
    // public function processData(array \$data)
    // {
    //     // Your business logic here
    //     return \$this->repository->create(\$data);
    // }
}";
        
        File::put($servicePath, $content);
        $this->info("Service created: {$servicePath}");
    }
    
    private function generateMigration($name)
    {
        $tableName = Str::snake(Str::plural($name));
        $migrationName = "create_{$tableName}_table";
        
        $this->call('make:migration', [
            'name' => $migrationName,
            '--create' => $tableName
        ]);
        
        $this->info("Migration created for table: {$tableName}");
    }
    
    private function updateServiceProvider($name)
    {
        $modelName = Str::studly($name);
        $repositoryInterface = "I{$modelName}Repository";
        $repositoryClass = "{$modelName}Repository";
        $serviceInterface = "I{$modelName}Service";
        $serviceClass = "{$modelName}Service";
        
        $providerPath = app_path('Providers/AppServiceProvider.php');
        $content = File::get($providerPath);
        
        // Add bindings if not already present
        $bindings = [
            "        \$this->app->bind({$repositoryInterface}::class, {$repositoryClass}::class);",
            "        \$this->app->bind({$serviceInterface}::class, {$serviceClass}::class);"
        ];
        
        foreach ($bindings as $binding) {
            if (strpos($content, $binding) === false) {
                // Find the register method and add binding
                $pattern = '/(public function register\(\)\s*\{[^}]*?)(\s*})/s';
                $replacement = '$1' . "\n" . $binding . '$2';
                $content = preg_replace($pattern, $replacement, $content);
            }
        }
        
        File::put($providerPath, $content);
        $this->info("ServiceProvider updated with bindings");
    }
}
