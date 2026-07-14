<?php

namespace App\Services;

class FPGrowthService
{
    private $apiUrl = 'http://127.0.0.1:8000';

    public function getRekomendasi($namaAlat)
    {
        $url = $this->apiUrl . '/rekomendasi/' . urlencode($namaAlat);

        // Gunakan CURL agar lebih stabil dan bisa menangani error
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout 5 detik
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return []; // Kembalikan array kosong jika API mati
        }

        return json_decode($response, true); // Kembalikan array, bukan echo
    }

    public function generateRulesFromPython($params)
    {
        $url = $this->apiUrl . '/generate-rules'; // Sesuaikan endpoint API Python-mu
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['rules'] ?? [];
    }
}