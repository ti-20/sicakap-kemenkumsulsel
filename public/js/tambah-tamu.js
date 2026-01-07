// Tambah Pengguna Form Management
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formTambahTamu');
    const fotoInput = document.getElementById('foto');
    const preview = document.getElementById('previewImage');
    
    // Preview foto
    // if (fotoInput) {
    //     fotoInput.addEventListener('change', function() {
    //         const file = this.files[0];
    //         if (file) {
    //             const reader = new FileReader();
    //             reader.onload = function(e) {
    //                 preview.setAttribute('src', e.target.result);
    //             }
    //             reader.readAsDataURL(file);
    //         }
    //     });
    // }
    
    // Validasi form
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nama = document.getElementById('nama').value.trim();
        const telp = document.getElementById('telp').value.trim();
        const email = document.getElementById('email').value;
        const alamat = document.getElementById('alamat').value;
        const tujuan = document.getElementById('tujuan').value;
        
        // Validasi client-side
        if (!nama) {
            Swal.fire('Error!', 'Nama harus diisi', 'error');
            return;
        }
        
        if (!telp) {
            Swal.fire('Error!', 'No Telpon/WA harus diisi', 'error');
            return;
        }
        
        if (!email) {
            Swal.fire('Error!', 'Email harus diisi', 'error');
            return;
        }

        if (!alamat) {
            Swal.fire('Error!', 'Alamat harus diisi', 'error');
            return;
        }

        if (!tujuan) {
            Swal.fire('Error!', 'Maksud/Tujuan bertamu harus diisi', 'error');
            return;
        }
        
        // Submit form
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menambahkan tamu baru?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tambahkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX submission
                const formData = new FormData(form);
                
                fetch('index.php?page=store-tamu', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'index.php?page=tamu';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message,
                            showConfirmButton: true
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menambahkan tamu',
                        showConfirmButton: true
                    });
                });
            }
        });
    });
});