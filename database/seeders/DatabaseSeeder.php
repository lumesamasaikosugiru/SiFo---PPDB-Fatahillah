<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!Schema::hasTable('roles')) {
            $this->command->error('Tabel "roles" belum ada. Jalankan dulu:');
            $this->command->error('  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"');
            $this->command->error('  php artisan migrate');
            return;
        }

        $this->call([
            RolePermissionSeeder::class,
            SekolahSeeder::class,
            UserSeeder::class,
            TahunAkademikSeeder::class,
            JurusanSeeder::class,
            PendaftaranSeeder::class,
            MuridSeeder::class,
            WaliMuridSeeder::class,
            MetodePembayaranSeeder::class,
            PembayaranSeeder::class,
        ]);
    }
}
