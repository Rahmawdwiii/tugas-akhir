<?php

namespace App\Libraries;

class FPGrowth
{
    private $baseUrl = "http://127.0.0.1:8000";

    public function rekomendasiPrioritas($namaAlat, $lokasi, $unit)
    {
        $payload = json_encode([
            "nama_alat" => $namaAlat,
            "lokasi" => $lokasi,
            "unit" => $unit
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . "/rekomendasi-prioritas",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            curl_close($curl);

            return [
                "success" => false,
                "data" => null
            ];
        }

        curl_close($curl);

        return json_decode($response, true);
    }
}