<?php

namespace App\Controllers;

class EmailTest extends BaseController
{
    public function index()
    {
        $email = service('email');

        $email->setFrom(
            env('email.fromEmail'),
            env('email.fromName')
        );

        $email->setTo('rahmawatidwilestari2@gmail.com');

        $email->setSubject('Testing Email UPA PP');

        $email->setMessage("
            <h2>Email Berhasil 🎉</h2>

            <p>Selamat!</p>

            <p>Sistem UPA PP berhasil terhubung ke Gmail.</p>

            <hr>

            <b>CodeIgniter 4.7</b><br>
            <b>SMTP Gmail</b><br>
            <b>Status :</b> BERHASIL

            <hr>

            <small>Generated automatically by UPA PP</small>
        ");

        if ($email->send()) {

            echo "EMAIL BERHASIL DIKIRIM";

        } else {

            echo "<pre>";
            print_r($email->printDebugger(['headers']));
            echo "</pre>";

        }
    }
}