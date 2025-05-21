<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'Farmer', 'description' => 'Regular farmer user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SectorAdmin', 'description' => 'Administrator for a sector', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DistrictAdmin', 'description' => 'Administrator for a district', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SystemAdmin', 'description' => 'System administrator', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
