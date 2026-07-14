<?php

namespace App\Models;

use CodeIgniter\Model;

class RuleModel extends Model
{
    protected $table = 'tb_hasil_fp_growth_alat';
    protected $primaryKey = 'id_hasil';

    /*
    |--------------------------------------------------------------------------
    | FP-Growth Alat
    |--------------------------------------------------------------------------
    */
    public function getRekomendasiAlat($namaAlat)
    {
        return $this->db->table('tb_hasil_fp_growth_alat')
            ->groupStart()
            ->like('antecedent', $namaAlat)
            ->orLike('consequent', $namaAlat)
            ->groupEnd()
            ->orderBy('confidence', 'DESC')
            ->orderBy('lift', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();
    }

    /*
    |--------------------------------------------------------------------------
    | FP-Growth Ruangan
    |--------------------------------------------------------------------------
    */
    public function getRekomendasiLokasi($namaAlat, $unit)
    {
        return $this->db->table('tb_hasil_fp_growth')

            ->groupStart()

            ->groupStart()
            ->like('antecedent', $namaAlat)
            ->orLike('consequent', $namaAlat)
            ->groupEnd()

            ->groupStart()
            ->like('antecedent', $unit)
            ->orLike('consequent', $unit)
            ->groupEnd()

            ->groupEnd()

            ->orderBy('confidence', 'DESC')
            ->orderBy('lift', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Rule Manual
    |--------------------------------------------------------------------------
    */
    public function getRuleManual()
    {
        return $this->db->table('tb_rule_manual')
            ->orderBy('id_rule', 'ASC')
            ->get()
            ->getResultArray();
    }
}