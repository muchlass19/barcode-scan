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
                            <li class="breadcrumb-item active">Dashboard</li>
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
                                <h4 class="card-title">Jadwal Terdekat</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="row fw-bold mb-2">
                            <div class="col">Nama Kelas</div>
                            <div class="col">Nama Mata Pelajaran</div>
                            <div class="col">Jam Mulai</div>
                            <div class="col">Jam Selesai</div>
                        </div>
                        @foreach($jadwalTerdekat as $jadwal)
                        <div class="row mb-2">
                            <div class="col">
                                {{ $jadwal->kelas->nama_kelas }}
                            </div>
                            <div class="col">
                                {{ $jadwal->mataPelajaran->nama_mata_pelajaran }}
                            </div>
                            <div class="col">
                                {{ $jadwal->jam_mulai }}
                            </div>
                            <div class="col">
                                {{ $jadwal->jam_selesai }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection