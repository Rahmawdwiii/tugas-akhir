<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\BaseHandler;
use CodeIgniter\Session\Handlers\DatabaseHandler; // Pastikan ini aktif

class Session extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Session Driver
     * --------------------------------------------------------------------------
     */
    public string $driver = DatabaseHandler::class; // BENAR (Database)

    /**
     * --------------------------------------------------------------------------
     * Session Cookie Name
     * --------------------------------------------------------------------------
     */
    public string $cookieName = 'ci_session';

    /**
     * --------------------------------------------------------------------------
     * Session Expiration
     * --------------------------------------------------------------------------
     */
    public int $expiration = 7200;

    /**
     * --------------------------------------------------------------------------
     * Session Save Path
     * --------------------------------------------------------------------------
     * * KOREKSI:
     * Karena driver = DatabaseHandler, maka savePath = 'nama_tabel'
     * JANGAN gunakan WRITEPATH . 'session'
     */
    public string $savePath = 'ci_sessions'; // <--- UBAH BAGIAN INI

    /**
     * --------------------------------------------------------------------------
     * Session Match IP
     * --------------------------------------------------------------------------
     */
    public bool $matchIP = false; // BENAR (False agar tidak logout sendiri)

    /**
     * --------------------------------------------------------------------------
     * Session Time to Update
     * --------------------------------------------------------------------------
     */
    public int $timeToUpdate = 300;

    /**
     * --------------------------------------------------------------------------
     * Session Regenerate Destroy
     * --------------------------------------------------------------------------
     */
    public bool $regenerateDestroy = false;

    /**
     * --------------------------------------------------------------------------
     * Session Database Group
     * --------------------------------------------------------------------------
     */
    public ?string $DBGroup = null; // Biarkan null untuk pakai koneksi default

    // ... sisa konfigurasi Redis di bawah biarkan saja default ...
    public int $lockRetryInterval = 100_000;
    public int $lockMaxRetries = 300;
}