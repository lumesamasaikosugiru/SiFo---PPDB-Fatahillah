<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AdminSekolah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =========================
        // SUPERADMIN
        // =========================
        $superadmin = User::firstOrCreate(
            ['email' => 'spmb.ypf@superadmin.dev'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123123123'),
            ]
        );
        $superadmin->syncRoles(['superadmin']);

        // =========================
        // ADMIN YAYASAN
        // =========================
        $adminYayasan = User::firstOrCreate(
            ['email' => 'spmb.ypf@admin.dev'],
            [
                'name' => 'Admin Yayasan',
                'password' => Hash::make('123123123'),
            ]
        );
        $adminYayasan->syncRoles(['admin_yayasan']);

        // =========================
        // ADMIN SMP
        // =========================
        $adminSmp = User::firstOrCreate(
            ['email' => 'spmb.smp@admin.dev'],
            [
                'name' => 'Admin SMP',
                'password' => Hash::make('123123123'),
            ]
        );
        $adminSmp->syncRoles(['admin_sekolah']);

        AdminSekolah::firstOrCreate([
            'user_id' => $adminSmp->id,
            'sekolah_id' => 4, // ID SMP
        ]);

        // =========================
        // KEPALA SEKOLAH SMP
        // =========================
        $kepalaSmp = User::firstOrCreate(
            ['email' => 'spmb.smp@kepseksmp.dev'],
            [
                'name' => 'Kepsek SMP',
                'password' => Hash::make('123123123'),
            ]
        );
        $kepalaSmp->syncRoles(['kepala_sekolah_smp']);

        AdminSekolah::firstOrCreate([
            'user_id' => $kepalaSmp->id,
            'sekolah_id' => 4,
        ]);


        // =========================
        // ADMIN SMK YPF 1 Kramatwatu
        // =========================
        $adminFatser = User::firstOrCreate(
            ['email' => 'spmb.fatser@admin.dev'],
            [
                'name' => 'Admin Fatser',
                'password' => Hash::make('123123123'),
            ]
        );
        $adminFatser->syncRoles(['admin_sekolah']);

        AdminSekolah::firstOrCreate([
            'user_id' => $adminFatser->id,
            'sekolah_id' => 1, // ID SMK Fatser
        ]);

        // =========================
        // KEPALA SEKOLAH SMK YPF 1 Kramatwatu
        // =========================
        $kepsekFatser = User::firstOrCreate(
            ['email' => 'spmb.fatser@kepsek.dev'],
            [
                'name' => 'Kepsek Fatser',
                'password' => Hash::make('123123123'),
            ]
        );
        $kepsekFatser->syncRoles(['kepala_sekolah_smk']);

        AdminSekolah::firstOrCreate([
            'user_id' => $kepsekFatser->id,
            'sekolah_id' => 1, // ID SMK Fatser
        ]);


        // =========================
        // ADMIN SMK YPF 1 Cilegon
        // =========================
        $adminFatcil1 = User::firstOrCreate(
            ['email' => 'spmb.fatcil1@admin.dev'],
            [
                'name' => 'Admin Fatcil 1',
                'password' => Hash::make('123123123'),
            ]
        );
        $adminFatcil1->syncRoles(['admin_sekolah']);

        AdminSekolah::firstOrCreate([
            'user_id' => $adminFatcil1->id,
            'sekolah_id' => 2, // ID SMK Fatcil 1
        ]);

        // =========================
        // KEPALA SEKOLAH SMK YPF 1 Cilegon
        // =========================
        $kepsekFatcil1 = User::firstOrCreate(
            ['email' => 'spmb.fatcil1@kepsek.dev'],
            [
                'name' => 'Kepsek Fatcil 1',
                'password' => Hash::make('123123123'),
            ]
        );
        $kepsekFatcil1->syncRoles(['kepala_sekolah_smk']);

        AdminSekolah::firstOrCreate([
            'user_id' => $kepsekFatcil1->id,
            'sekolah_id' => 2, // ID SMK Fatcil 1
        ]);


        // =========================
        // ADMIN SMK YPF 2 Cilegon
        // =========================
        $adminFatcil2 = User::firstOrCreate(
            ['email' => 'spmb.fatcil2@admin.dev'],
            [
                'name' => 'Admin Fatcil 2',
                'password' => Hash::make('123123123'),
            ]
        );
        $adminFatcil2->syncRoles(['admin_sekolah']);

        AdminSekolah::firstOrCreate([
            'user_id' => $adminFatcil2->id,
            'sekolah_id' => 3, // ID SMK Fatcil 2
        ]);

        // =========================
        // KEPALA SEKOLAH SMK YPF 2 Cilegon
        // =========================
        $kepsekFatcil2 = User::firstOrCreate(
            ['email' => 'spmb.fatcil2@kepsek.dev'],
            [
                'name' => 'Kepsek Fatcil2',
                'password' => Hash::make('123123123'),
            ]
        );
        $kepsekFatcil2->syncRoles(['kepala_sekolah_smk']);

        AdminSekolah::firstOrCreate([
            'user_id' => $kepsekFatcil2->id,
            'sekolah_id' => 3, // ID SMK
        ]);
    }
}
