document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('BSimpan');
    const form = document.getElementById('FormTambah');

    if (!btn || !form) return;

    btn.addEventListener('click', function () {

        if (typeof capturePublicFoto === 'function') {
            capturePublicFoto();
        }

        const formData = new FormData(form);

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah data sudah benar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch('index.php?page=store-tamu', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terima kasih 🙏',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            form.reset();
                            document.getElementById('foto').value = '';
                            document.getElementById('ttd').value = '';

                            // 🔥 RESET CANVAS TTD
                            if (typeof clearTTD === 'function') {
                                clearTTD();
                            }
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                });
        });
    });
});


// Fungsi ttd
// === TTD PUBLIC ===
document.addEventListener("DOMContentLoaded", function () {
    const sigBox = document.getElementById("sig");
    const inputTTD = document.getElementById("ttd");
    const clearBtn = document.getElementById("clear");

    if (!sigBox) return;

    // GLOBAL CANVAS & CTX
    window.ttdCanvas = document.createElement("canvas");
    window.ttdCanvas.width = sigBox.offsetWidth;
    window.ttdCanvas.height = 200;
    sigBox.appendChild(window.ttdCanvas);

    window.ttdCtx = window.ttdCanvas.getContext("2d");
    window.ttdCtx.strokeStyle = "#000";
    window.ttdCtx.lineWidth = 2;
    window.ttdCtx.lineCap = "round";

    let drawing = false;

    window.ttdCanvas.addEventListener("mousedown", () => drawing = true);
    window.ttdCanvas.addEventListener("mouseup", () => {
        drawing = false;
        window.ttdCtx.beginPath();
        inputTTD.value = window.ttdCanvas.toDataURL("image/png");
    });
    window.ttdCanvas.addEventListener("mousemove", draw);

    function draw(e) {
        if (!drawing) return;
        window.ttdCtx.lineTo(e.offsetX, e.offsetY);
        window.ttdCtx.stroke();
        window.ttdCtx.beginPath();
        window.ttdCtx.moveTo(e.offsetX, e.offsetY);
    }

    // 🔥 FUNGSI GLOBAL CLEAR (INI YANG KURANG)
    window.clearTTD = function () {
        window.ttdCtx.clearRect(
            0,
            0,
            window.ttdCanvas.width,
            window.ttdCanvas.height
        );
        inputTTD.value = "";
    };

    clearBtn.addEventListener("click", window.clearTTD);
});


// === KAMERA PUBLIC (TANPA TOMBOL, AUTO CAPTURE) ===
document.addEventListener('DOMContentLoaded', function () {
    const cameraBox = document.getElementById('my_camera');
    const fotoInput = document.getElementById('foto');

    if (!cameraBox || !fotoInput) return;

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            const video = document.createElement('video');
            video.autoplay = true;
            video.playsInline = true;
            video.srcObject = stream;
            video.style.width = '100%';

            cameraBox.appendChild(video);

            // fungsi global → dipanggil saat klik simpan
            window.capturePublicFoto = function () {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);

                fotoInput.value = canvas.toDataURL('image/jpeg');
            };
        })
        .catch(() => {
            Swal.fire(
                'Kamera Tidak Aktif',
                'Izin kamera ditolak atau browser tidak mendukung',
                'error'
            );
        });
});
