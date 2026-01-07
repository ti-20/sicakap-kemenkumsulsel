<?php
require_once __DIR__ . '/../models/TamuModel.php';

class TamuController
{
    private $model;

    public function __construct()
    {
        $this->model = new TamuModel();
    }

    // Halaman daftar Tamu (tamu.php)
    public function daftarTamu()
    {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/pages/tamu.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    // Halaman tambah Tamu (tambah-tamu.php)
    public function tambahTamu()
    {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/pages/tambah-tamu.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    // Proses tambah tamu
    public function storeTamu()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $nama   = trim($_POST['nama'] ?? '');
        $telp   = trim($_POST['telp'] ?? '');
        $email  = $_POST['email'] ?? '';
        $alamat = $_POST['alamat'] ?? '';
        $tujuan = $_POST['tujuan'] ?? '';
        // $fotoBase64 = $_POST['foto'] ?? '';
        $ttdBase64 = $_POST['ttd'] ?? '';

        // VALIDASI
        $errors = [];
        if (!$nama)   $errors[] = 'Nama harus diisi';
        if (!$telp)   $errors[] = 'No Telepon harus diisi';
        if (!$email)  $errors[] = 'Email harus diisi';
        if (!$alamat) $errors[] = 'Alamat harus diisi';
        if (!$tujuan) $errors[] = 'Tujuan harus diisi';
        // if (!$fotoBase64) $errors[] = 'Foto harus diisi';
        if (!$ttdBase64) $errors[] = 'Tanda tangan harus diisi';

        if ($errors) {
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }

        // Proses kamera
        $fotoBase64 = $_POST['foto'] ?? '';

        $fotoFilename = null;

        if ($fotoBase64) {
            $uploadDir = __DIR__ . '/../../public/Images/uploads/foto/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Hapus prefix base64
            $fotoBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
            $fotoBinary = base64_decode($fotoBase64);

            if ($fotoBinary === false) {
                echo json_encode(['success' => false, 'message' => 'Format foto tidak valid']);
                exit;
            }

            // NAMA FILE SESUAI CONTOH DB KAMU
            $fotoFilename = 'B' . time() . rand(1000, 9999) . '.jpg';
            $fotoPath = $uploadDir . $fotoFilename;

            file_put_contents($fotoPath, $fotoBinary);
        }

        // PROSES TTD (BASE64 → PNG)
        $uploadDir = __DIR__ . '/../../public/Images/uploads/ttd/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Hapus prefix base64
        $ttdBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $ttdBase64);
        $ttdBinary = base64_decode($ttdBase64);

        if ($ttdBinary === false) {
            echo json_encode(['success' => false, 'message' => 'Format tanda tangan tidak valid']);
            exit;
        }

        // Nama file sesuai contoh database kamu
        $ttdFilename = 'A' . time() . rand(1000, 9999) . '.png';
        $ttdPath = $uploadDir . $ttdFilename;

        file_put_contents($ttdPath, $ttdBinary);

        // SIMPAN KE DATABASE
        $data = [
            'nama'   => $nama,
            'telp'   => $telp,
            'email'  => $email,
            'alamat' => $alamat,
            'tujuan' => $tujuan,
            // 'foto'   => $fotoFilename,
            'ttd'    => $ttdFilename
        ];

        if ($this->model->tambahTamu($data)) {
            echo json_encode(['success' => true, 'message' => 'Tamu berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan tamu']);
        }
        exit;
    }

    // Proses hapus TAMU (AJAX)
    public function hapusTamu()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID tamu tidak valid']);
            exit;
        }

        // Ambil data tamu sebelum dihapus
        $tamu = $this->model->getTamuById($id);
        if (!$tamu) {
            echo json_encode(['success' => false, 'message' => 'Data tamu tidak ditemukan']);
            exit;
        }

        // Hapus file FOTO
        if (!empty($tamu['foto'])) {
            $fotoPath = __DIR__ . '/../../public/Images/uploads/foto/' . $tamu['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        // Hapus file TTD
        if (!empty($tamu['ttd'])) {
            $ttdPath = __DIR__ . '/../../public/Images/uploads/ttd/' . $tamu['ttd'];
            if (file_exists($ttdPath)) {
                unlink($ttdPath);
            }
        }

        // Hapus dari database
        if ($this->model->hapusTamu($id)) {
            echo json_encode(['success' => true, 'message' => 'Data tamu berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data tamu']);
        }

        exit;
    }

    // Halaman edit pengguna (edit-pengguna.php)
    public function editPengguna()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?page=pengguna');
            exit;
        }

        // if (!$pengguna) {
        //     $_SESSION['errors'] = ['Pengguna tidak ditemukan'];
        //     header('Location: index.php?page=pengguna');
        //     exit;
        // }

        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/pages/edit-pengguna.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    // Proses update pengguna
    public function updatePengguna()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID pengguna tidak valid']);
            exit;
        }

        $nama = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'Operator';

        // Validasi
        $errors = [];
        if (empty($nama)) $errors[] = 'Nama harus diisi';
        if (empty($username)) $errors[] = 'Username harus diisi';
        if (!in_array($role, ['Admin', 'Operator', 'p3h'])) $errors[] = 'Role tidak valid';

        // Validasi password jika diisi
        if (!empty($password)) {
            if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
            if ($password !== $confirmPassword) $errors[] = 'Password dan konfirmasi password tidak sama';
        }

        if (!empty($errors)) {
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }

        // Handle foto upload
        $foto = 'user.jpg'; // Default foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/Images/users/';
            $fileExtension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fileName = 'user_' . $id . '_' . time() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                $foto = $fileName;
            }
        } else {
            // Jika tidak ada foto baru, ambil foto lama dari database
            $penggunaLama = $this->model->getPenggunaById($id);
            $foto = $penggunaLama['foto'] ?? 'user.jpg';
        }

        // Simpan data
        $data = [
            'nama' => $nama,
            'username' => $username,
            'role' => $role,
            'foto' => $foto
        ];

        // Jika password diisi, update password
        if (!empty($password)) {
            $data['password'] = $password;
        }
        exit;
    }

    // Halaman edit profil (edit-profil.php)
    public function editProfilPengguna()
    {
        // nanti bisa pakai data user yang sedang login
        // $profil = $this->model->getProfilByUserId($userId);

        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/pages/edit-profil.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    // Proses update profil pengguna
    public function updateProfilPengguna()
    {
        // Suppress semua output sebelum JSON
        ob_clean();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Ambil ID user yang sedang login
        $id = $_SESSION['user']['id'] ?? null;
        if (!$id) {
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User tidak valid']);
            exit;
        }

        $nama = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $konfirmasi = $_POST['konfirmasi'] ?? '';

        // Validasi
        $errors = [];
        if (empty($nama)) $errors[] = 'Nama harus diisi';
        if (empty($username)) $errors[] = 'Username harus diisi';

        // Validasi password jika diisi
        if (!empty($password)) {
            if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
            if ($password !== $konfirmasi) $errors[] = 'Password dan konfirmasi password tidak sama';
        }

        // Cek username sudah ada (kecuali untuk user yang sama)
        if ($this->model->isUsernameExists($username, $id)) {
            $errors[] = 'Username sudah digunakan';
        }

        if (!empty($errors)) {
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }

        // Handle foto upload dengan security
        $foto = $_SESSION['user']['foto'] ?? 'user.jpg'; // Default foto dari session
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../helpers/SecureFileUpload.php';
            $uploadHandler = new SecureFileUpload('users');

            $uploadResult = $uploadHandler->uploadFile('foto', 'user');

            if ($uploadResult['success']) {
                // Hapus foto lama jika bukan foto default
                $fotoLama = $_SESSION['user']['foto'] ?? 'user.jpg';
                if ($fotoLama !== 'user.jpg') {
                    $uploadHandler->deleteFile($fotoLama);
                }
                $foto = $uploadResult['filename'];
            } else {
                // Handle upload error
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Upload foto gagal: ' . $uploadResult['message']]);
                exit;
            }
        }

        // Simpan data
        $data = [
            'nama' => $nama,
            'username' => $username,
            'foto' => $foto,
            'role' => $_SESSION['user']['role'] // Pertahankan role yang sudah ada
        ];

        // Jika password diisi, update password
        if (!empty($password)) {
            $data['password'] = $password;
        }

        if ($this->model->updatePengguna($id, $data)) {
            // Update session dengan data baru (pertahankan role)
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['foto'] = $foto;
            // Role tidak perlu diupdate karena sudah ada di session

            // Clear output buffer dan set header untuk JSON response
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui']);
        } else {
            // Clear output buffer dan set header untuk JSON response
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui profil']);
        }
        exit;
    }
}
