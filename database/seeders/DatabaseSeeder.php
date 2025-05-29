<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // 1. Seed roles and permissions first
        $this->command->info('📋 Seeding roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // 2. Seed users (admin and farmers)
        $this->command->info('👥 Seeding users...');
        $this->call(UserSeeder::class);

        // 3. Seed farms for farmers
        $this->command->info('🚜 Seeding farms...');
        $this->call(FarmSeeder::class);

        // 4. Seed devices for farms
        $this->command->info('📱 Seeding devices...');
        $this->call(DeviceSeeder::class);

















}    }        $this->command->info('   Farmers: john@farmer.com, mary@farmer.com, etc. / password123');        $this->command->info('   Admin: admin@smartcrop.com / password123');        $this->command->info('🔐 You can login with:');        $this->command->info('📊 Database is now populated with sample data for testing.');        $this->command->info('✅ All seeders completed successfully!');        $this->call(CropHistorySeeder::class);        $this->command->info('🌾 Seeding crop history...');        // 6. Seed crop history        $this->call(SoilDataSeeder::class);        $this->command->info('🌱 Seeding soil data...');        // 5. Seed soil data for devices
