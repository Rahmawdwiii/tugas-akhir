<?php

namespace App\Controllers;

// Hapus use CodeIgniter\Controller;
// Ganti dengan BaseController agar session stabil
use App\Models\UserModel;

class Auth extends BaseController // <--- UBAH INI JADI BaseController
{
    public function login()
    {
        // HAPUS baris ini: $session = session(); 
        // Kita gunakan $this->session milik BaseController agar konsisten.

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // Validasi input sederhana
        if (!$username || !$password) {
            // Gunakan $this->session
            $this->session->setFlashdata('msg', 'Username dan Password wajib diisi.');
            return redirect()->to('/');
        }

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $sessData = [
                    'id_user'   => $user['id_user'], // Pastikan INT
                    'username'  => $user['username'],
                    'nama'      => $user['nama'],
                    'role'      => $user['akses'],
                    'logged_in' => true
                ];

                // Gunakan $this->session agar sesi tersimpan stabil
                $this->session->set($sessData);

                // Debugging: Pastikan session ID tersimpan
                // log_message('error', 'Login Sukses. ID Tersimpan: ' . $this->session->get('id_user'));

                switch ($user['akses']) {
                    case 'admin':
                        return redirect()->to('/admin/dashboard');
                    case 'teknisi':
                        return redirect()->to('/teknisi/dashboard');
                    case 'pelapor':
                        return redirect()->to('/pelapor/dashboard');
                    default:
                        $this->session->destroy();
                        return redirect()->to('/')->with('msg', 'Role pengguna tidak valid.');
                }
            } else {
                $this->session->setFlashdata('msg', 'Password salah.');
                return redirect()->to('/');
            }
        } else {
            $this->session->setFlashdata('msg', 'Username tidak ditemukan.');
            return redirect()->to('/');
        }
    }

    public function logout()
    {
        // Gunakan $this->session
        $this->session->destroy();
        return redirect()->to('/');
    }
}
