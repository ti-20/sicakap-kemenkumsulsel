<?php
// app/models/TamuModel.php
require_once __DIR__ . '/../../config/database.php';

class TamuModel
{
    private $db;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Ambil satu data tamu
    public function getTamuById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tb_tamu WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Simpan data tamu baru
    public function tambahTamu(array $data)
    {
        try {
            $sql = "INSERT INTO tb_tamu 
                    (nama, telp, email, alamat, tujuan, ttd, tgl, jam) 
                    VALUES 
                    (:nama, :telp, :email, :alamat, :tujuan, :ttd, CURDATE(), CURTIME())";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nama'   => $data['nama'],
                ':telp'   => $data['telp'],
                ':email'  => $data['email'],
                ':alamat' => $data['alamat'],
                ':tujuan' => $data['tujuan'],
                ':ttd'    => $data['ttd'],
            ]);
        } catch (PDOException $e) {
            // Untuk debugging (hapus di production)
            // error_log($e->getMessage());
            return false;
        }
    }

    // Hapus tamu
    public function hapusTamu($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tb_tamu WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
