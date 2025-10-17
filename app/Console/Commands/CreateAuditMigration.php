<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateAuditMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:audit-migration {name : The name of the migration} {--create : Create table} {--table= : Table name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migration with audit fields inheritance';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $isCreate = $this->option('create');
        $tableName = $this->option('table') ?: Str::snake(Str::plural($name));
        
        // Generate migration file name
        $timestamp = now()->format('Y_m_d_His');
        $migrationName = $timestamp . '_' . Str::snake($name) . '.php';
        $migrationPath = database_path('migrations/' . $migrationName);
        
        // Generate class name
        $className = Str::studly($name);
        
        // Generate migration content
        $content = $this->generateMigrationContent($className, $tableName, $isCreate);
        
        // Write migration file
        File::put($migrationPath, $content);
        
        $this->info("Migration created: {$migrationPath}");
        $this->info("Table name: {$tableName}");
        $this->info("You can now add custom fields in the customFields() method");
        
        return Command::SUCCESS;
    }
    
    private function generateMigrationContent($className, $tableName, $isCreate)
    {
        $baseMigrationPath = database_path('migrations/0000_00_00_000000_base_audit_migration.php');
        
        if (!File::exists($baseMigrationPath)) {
            $this->error('Base audit migration not found! Please create it first.');
            return '';
        }
        
        $baseContent = File::get($baseMigrationPath);
        
        // Replace abstract class with concrete class
        $content = str_replace('abstract class BaseAuditMigration', "class {$className}", $baseContent);
        
        // Add table name property
        $content = str_replace(
            'protected $tableName;',
            "protected \$tableName = '{$tableName}';",
            $content
        );
        
        // Add custom fields method with example
        $customFieldsMethod = "
    /**
     * Add your custom fields here
     * 
     * @param Blueprint \$table
     * @return void
     */
    protected function customFields(Blueprint \$table)
    {
        // Example custom fields - remove and add your own
        // \$table->string('name');
        // \$table->integer('age');
        // \$table->string('email')->unique();
        // \$table->text('description')->nullable();
        // \$table->boolean('is_active')->default(true);
    }";
        
        // Insert custom fields method before the last closing brace
        $content = str_replace(
            '    // Override in your migration class
}',
            $customFieldsMethod . '
}',
            $content
        );
        
        return $content;
    }
}
