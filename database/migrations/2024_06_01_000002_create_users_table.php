<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If users table already exists, modify it
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'username')) {
                    $table->string('username')->nullable()->after('id');
                }

                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable()->after('email');
                }

                if (!Schema::hasColumn('users', 'role_id')) {
                    $table->unsignedBigInteger('role_id')->default(1)->after('phone');

                    // Add foreign key if roles table exists
                    if (Schema::hasTable('roles')) {
                        $table->foreign('role_id')->references('id')->on('roles');
                    }
                }

                if (!Schema::hasColumn('users', 'status')) {
                    $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('password');
                }

                if (!Schema::hasColumn('users', 'last_login')) {
                    $table->timestamp('last_login')->nullable()->after('remember_token');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Drop foreign key if it exists
                if (Schema::hasColumn('users', 'role_id')) {
                    // Check for foreign key constraint
                    $foreignKeys = $this->listTableForeignKeys('users');
                    if (in_array('users_role_id_foreign', $foreignKeys)) {
                        $table->dropForeign(['role_id']);
                    }
                    $table->dropColumn('role_id');
                }

                // Drop other columns if they exist
                $columns = ['username', 'phone', 'status', 'last_login'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    /**
     * Get list of foreign keys for a table
     */
    private function listTableForeignKeys($table) {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        $foreignKeys = array_map(function($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));

        return $foreignKeys;
    }


};
