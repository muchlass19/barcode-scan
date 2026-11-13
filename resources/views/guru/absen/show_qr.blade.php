@extends('layouts.layout')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Dashboard Admin</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Guru</a></li>
                            <li class="breadcrumb-item"><a href="#">QR Code</a></li>
                            <li class="breadcrumb-item active">{{ $pertemuan->jadwalPelajaran->mataPelajaran->nama_mata_pelajaran }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Absen Mata Pelajaran {{ $pertemuan->jadwalPelajaran->mataPelajaran->nama_mata_pelajaran }} - {{ $pertemuan->tanggal_wib }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-12">
                                {{ $qrCode }}
                            </div>
                            <div class="col-12 mt-4">
                                <p class="text-sm">Barcode Expired pada <span class="fw-bold">{{ $pertemuan->expired_at_wib }}</span></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection