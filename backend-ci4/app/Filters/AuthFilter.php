<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Dijalankan SEBELUM controller
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika belum login
        if (!session()->get('logged_in')) {
            // Paksa kembali ke halaman login (Home::index)
            return redirect()->to('/');
        }

        if (!empty($arguments)) {
            $userRole = session()->get('role');
        
            if (!$userRole || !in_array($userRole, $arguments)) {
                return redirect()->to('/');
            }
        }        
    }

    /**
     * Dijalankan SETELAH controller
     */
    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Tidak perlu apa-apa
    }
}