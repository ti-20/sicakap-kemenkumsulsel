<?php
// app/views/pages/tambah-tamu.php
// Auto-detect BASE_URL jika belum tersedia
if (!isset($BASE)) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    $httpHost = $_SERVER['HTTP_HOST'] ?? '';
    
    $isLocalhost = (
        strpos($serverName, 'localhost') !== false ||
        strpos($serverName, '127.0.0.1') !== false ||
        strpos($httpHost, 'localhost') !== false ||
        strpos($requestUri, '/rekap-konten/public') !== false ||
        strpos($scriptName, '/rekap-konten/public') !== false
    );
    
    $BASE = $isLocalhost ? 
        (defined('BASE_URL') ? BASE_URL : '/rekap-konten/public') : 
        '';
}
?>

<div class="overview">
    <div class="title">
        <i class="fas fa-user-plus"></i>
        <span class="text">Tambah Tamu</span>
    </div>

    <!-- Form Tambah Pengguna -->
    <div class="form-container">
        <form id="formTambahTamu" action="index.php?page=store-tamu" method="POST" class="input-berita-form" autocomplete="off" enctype="multipart/form-data">

            <!-- Upload Foto -->
            <div class="upload-container">
                    <img id="previewImage" src="<?= $BASE ?>/Images/user.jpg" alt="Preview Foto">
                <br>
                <label for="foto"><i class="fas fa-image"></i> Upload Foto</label>
                <input type="file" id="foto" name="foto" accept="image/*">
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" 
                       value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="telp">Telepon/WA</label>
                <input type="text" id="telp" name="telp" placeholder="08123" 
                       value="<?= htmlspecialchars($_POST['telp'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="abc@gmail.com" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" placeholder="Alamat" 
                       value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="tujuan">Maksud/Tujuan Bertamu</label>
                <input type="text" id="tujuan" name="tujuan" placeholder="Maksud/Tujuan Bertamu" 
                       value="<?= htmlspecialchars($_POST['tujuan'] ?? '') ?>" required>
            </div>

            <div style="text-align:center; margin-top:20px;">
                <button type="submit" class="btn-simpan">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" class="btn-batal" onclick="window.location.href='index.php?page=tamu'">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
    <!-- End Form -->
</div>

<!-- Script preview foto dan validasi -->
<script src="<?= $BASE ?>/js/tambah-tamu.js"></script>
