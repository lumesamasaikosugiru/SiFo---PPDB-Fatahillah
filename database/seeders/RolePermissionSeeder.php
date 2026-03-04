<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'superadmin',
            'admin_yayasan',
            'admin_sekolah',
            'kepala_sekolah_smp',
            'kepala_sekolah_smk',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */
        $permissions = [
            // user
            'user.manage',

            // pendaftaran
            'pendaftaran.view',
            'pendaftaran.create',
            'pendaftaran.update_status',

            // pembayaran
            'pembayaran.view',
            'pembayaran.create',
            'pembayaran.verify',

            // master
            'master.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGN PERMISSIONS
        |--------------------------------------------------------------------------
        */

        // SUPERADMIN → ALL
        Role::findByName('superadmin')->syncPermissions(Permission::all());

        // ADMIN YAYASAN
        Role::findByName('admin_yayasan')->syncPermissions([
            'user.manage',
            'master.manage',
            'pendaftaran.view',
            'pembayaran.view',
        ]);

        // ADMIN SEKOLAH
        Role::findByName('admin_sekolah')->syncPermissions([
            'pendaftaran.view',
            'pendaftaran.create',
            'pendaftaran.update_status',
            'pembayaran.view',
            'pembayaran.create',
            'pembayaran.verify',
        ]);

        // KEPALA SEKOLAH SMP
        Role::findByName('kepala_sekolah_smp')->syncPermissions([
            'pendaftaran.view',
            'pembayaran.view',
        ]);

        // KEPALA SEKOLAH SMK
        Role::findByName('kepala_sekolah_smk')->syncPermissions([
            'pendaftaran.view',
            'pembayaran.view',
        ]);
    }
}