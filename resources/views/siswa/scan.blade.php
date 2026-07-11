@extends('layouts.layout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <h4 class="mb-3">Scan QR Code Kehadiran</h4>
    
        <div id="reader" style="width: 100%"></div>
    
        <div class="mt-3">
            <small>
                Arahkan kamera ke QR Code. Sistem akan otomatis membaca dan memproses absensi.
            </small>
        </div>
    
    </div>

</div>

{{-- Library QR Code Scanner --}}
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const html5QrCode = new Html5Qrcode("reader");

    // Ambil list kamera
    Html5Qrcode.getCameras().then(cameras => {

        if (cameras.length > 0) {
            console.log(cameras);
            // Start kamera belakang (jika tersedia)
            // let cameraId = cameras.length > 1 ? cameras[1].id : cameras[0].id;
            let cameraId = cameras[0].id;

            html5QrCode.start(
                cameraId,
                { fps: 10, qrbox: 450 },

                // Ketika QR berhasil terbaca
                qrMessage => {

                    try {
                        const data = JSON.parse(qrMessage);

                        // Redirect ke proses scan
                        window.location.href =
                            "/siswa/absen/scan?pertemuan_id=" + data.pertemuan_id +
                            "&token=" + encodeURIComponent(data.token);

                    } catch (err) {
                        alert("QR Code tidak valid!");
                    }
                },

                // Error callback (boleh dikosongkan)
                errorMessage => {
                    // console.log("Scan error: ", errorMessage);
                }
            );
        }

    }).catch(err => {
        alert("Kamera tidak ditemukan!");
    });

});
</script>
@endsection
