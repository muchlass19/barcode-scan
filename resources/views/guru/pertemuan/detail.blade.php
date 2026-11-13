@extends('layouts.layout')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Dashboard Pertemuan</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Guru</a></li>
                            <li class="breadcrumb-item"><a href="#">Absen</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail</a></li>
                            <li class="breadcrumb-item"><a href="#">{{ $infoJadwal->kelas->nama_kelas }}</a></li>
                            <li class="breadcrumb-item active">{{ $infoJadwal->mataPelajaran->nama_mata_pelajaran }}</li>
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
                                <h4 class="card-title">Pertemuan {{ $infoJadwal->kelas->nama_kelas }} - {{ $infoJadwal->mataPelajaran->nama_mata_pelajaran }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal Pertemuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwalPertemuan as $pertemuan)
                                    <tr>
                                        <td>{{ $pertemuan->tanggal_wib }}</td>
                                        <td class="d-flex gap-2">
                                            <a href="{{ route('guru.absen.manual', $pertemuan->id) }}" class="btn btn-primary btn-sm">Detail Absen</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Hari ini tidak jadwal mengajar.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection