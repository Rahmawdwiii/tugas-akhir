<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // ini akan memanggil file: app/Views/landing/index.php
        return view('landing/index');
    }

    /**
     * API: Get statistik kerusakan berdasarkan tahun saat ini
     * Data akan berubah otomatis setiap tahun dan bertambah
     */
    public function get_statistics_year()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        try {
            $db = \Config\Database::connect();
            // Ambil tahun terakhir yang memiliki data perbaikan
            $lastYear = $db->query("
                SELECT MAX(YEAR(tanggal_laporan)) AS tahun
                FROM tb_laporan
            ")->getRowArray();
            $currentYear = $lastYear['tahun'] ?? date('Y');

            // Ambil data kerusakan per severity dari database untuk tahun ini
            $query = "
                SELECT
                    p.status_kerusakan,
                    COUNT(*) AS total
                FROM tb_perbaikan p
                JOIN tb_jadwal_perbaikan jp
                    ON jp.id_jadwal = p.id_jadwal
                JOIN tb_laporan l
                    ON l.id_laporan = jp.id_laporan
                WHERE YEAR(l.tanggal_laporan) = ?
                AND p.status_kerusakan IS NOT NULL
                GROUP BY p.status_kerusakan
                ";
            $result = $db->query($query, [$currentYear])->getResultArray();

            // Initialize default values dengan key uppercase sesuai enum
            $stats = [
                'Berat' => 0,
                'Sedang' => 0,
                'Ringan' => 0
            ];

            // Fill actual values from database
            foreach ($result as $row) {
                $status = strtoupper(trim($row['status_kerusakan']));
                $count = (int) $row['total'];

                if ($status === 'BERAT') {
                    $stats['Berat'] = $count;
                } elseif ($status === 'SEDANG') {
                    $stats['Sedang'] = $count;
                } elseif ($status === 'RINGAN') {
                    $stats['Ringan'] = $count;
                }
            }

            // If no data for current year, try to get all available data
            $totalCount = array_sum($stats);

            return $this->response->setJSON([
                'status' => 'success',
                'year' => $currentYear,
                'data' => [
                    'berat' => $stats['Berat'],
                    'sedang' => $stats['Sedang'],
                    'ringan' => $stats['Ringan']
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

