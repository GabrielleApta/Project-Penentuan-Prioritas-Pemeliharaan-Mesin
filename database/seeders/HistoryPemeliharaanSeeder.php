<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoryPemeliharaanSeeder extends Seeder
{
    public function run(): void
    {
         Carbon::setLocale('id');
        $allMesinIds = DB::table('mesin')->pluck('id')->toArray();

        // Ambil hanya 50% mesin secara acak
        shuffle($allMesinIds);
        $selectedMesinIds = array_slice($allMesinIds, 0, ceil(count($allMesinIds) * 0.5));

        $teknisiList = ['Budi','Asep','Tatang','Boni','Rudi','Joko','Andi','Dedi','Wawan','Eko','Agus','Tono','Slamet','Imam'];

        $ringan = [
            'Betrel kendor','Sendok Colling lecet','Habasit lepas',
            'Klem piringan colling macet','Sensor column + cet rusak',
            'Klem baut piringan kendor','Box trucks baut pengikat box kendor',
            'Separator kendor','Baut kendor','Joint komponen transmisi lepas',
            'Cover penghubung bobon aluminium cover spindle macet'
        ];

        $berat = [
            'Motor utama jebol','Gear box rusak parah','Drive inverter failure',
            'Bearing utama overheat','Panel listrik short circuit',
            'Sistem kontrol utama error','V-belt utama putus',
            'Poros utama patah','Sensor kecepatan error total','Sistem pendingin mesin gagal'
        ];

        $rows = [];

        foreach ($selectedMesinIds as $mesinId) {
            for ($month = 1; $month <= 12; $month++) {
                $tanggal = Carbon::create(2024, $month, rand(1, 28));
                $isPreventive = rand(1, 100) <= 70; // 70% preventive

                $rows[] = [
                    'mesin_id' => $mesinId,
                    'jadwal_id' => null,
                    'tanggal' => $tanggal,
                    'jenis_pemeliharaan' => $isPreventive ? 'Preventive' : 'Corrective',
                    'deskripsi' => $isPreventive
                        ? $ringan[array_rand($ringan)]
                        : $berat[array_rand($berat)],
                    'durasi_jam' => $isPreventive ? rand(1, 2) : rand(3, 6),
                    'teknisi' => $teknisiList[array_rand($teknisiList)],
                    'foto_bukti' => null,
                    'verifikasi' => (bool)rand(0, 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Batch insert sekali saja
        DB::table('history_pemeliharaan')->insert($rows);
    }
}
