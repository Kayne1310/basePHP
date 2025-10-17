<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Base Migration Template with Audit Fields
 * 
 * This is a template migration that provides common audit fields.
 * Other migrations should extend this class and call parent::up() and parent::down()
 * 
 * Usage:
 * 1. Copy this file to your new migration
 * 2. Rename the class to your migration class name
 * 3. In up() method, call parent::up() first, then add your custom fields
 * 4. In down() method, call parent::down() last
 */
abstract class BaseAuditMigration extends Migration
{
    /**
     * The table name for this migration
     * Override this in your migration class
     */
    protected $tableName;

    /**
     * Run the migrations.
     * Override this method and call parent::up() first
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            // Audit fields
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('deleted_by')->nullable();
            
            // Call custom fields method if exists
            if (method_exists($this, 'customFields')) {
                $this->customFields($table);
            }
        });
    }

    /**
     * Reverse the migrations.
     * Override this method and call parent::down() last
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }

    /**
     * Override this method in your migration to add custom fields
     * 
     * @param Blueprint $table
     * @return void
     */
    protected function customFields(Blueprint $table)
    {
        // Override in your migration class
    }
}
