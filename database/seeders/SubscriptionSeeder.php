<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::create([
            'name' => 'Starter',
            'price' => 0,
            'duration_label' => '/ 30 hari',
            'duration_in_days' => 30,
            'description' => 'Akses awal untuk sekolah yang baru memulai digitalisasi BK.',
            'features' => [
                'Akses Penuh Fitur Dasar',
                'Kuota 50 Data Siswa',
                'Maksimal 2 Akun Konselor',
                'Aplikasi Android Siswa',
                'Cetak Rapor BK Otomatis', 
                'Eksport Data Terbatas',
            ],
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 1,
            'button_label' => 'Mulai Gratis',
            'button_url' => '/app/register',
        ]);

        SubscriptionPlan::create([
            'name' => 'Pro Semester',
            'price' => 249000, 
            'duration_label' => '/ 6 bulan',
            'duration_in_days' => 180,
            'description' => 'Pilihan cerdas untuk sekolah tingkat menengah.',
            'features' => [
                'Semua Fitur Starter',
                'Kuota 500 Data Siswa',
                'Maksimal 5 Akun Konselor',
                'Laporan Bulanan PDF',
                'Prioritas Support Standard',
            ],
            'is_featured' => false, 
            'is_active' => true,
            'sort_order' => 2,
            'button_label' => 'Pilih Paket Pro',
            'button_url' => '/app/register',
        ]);

        SubscriptionPlan::create([
            'name' => 'Enterprise',
            'price' => 499000, 
            'duration_label' => '/ tahun',
            'duration_in_days' => 365,
            'description' => 'Solusi tanpa batas untuk sekolah maju dan unggulan.',
            'features' => [
                'Semua Fitur Pro',
                'Siswa Unlimited', 
                'Konselor Unlimited', 
                'Support Prioritas VIP 24/7',
            ],
            'is_featured' => true, 
            'is_active' => true,
            'sort_order' => 3,
            'button_label' => 'Ambil Promo Tahunan',
            'button_url' => '/app/register',
        ]);
    }
}