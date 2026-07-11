@extends('layouts.layout')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Jadwal {{ $namaKelas }} - {{ $mapel->nama_mata_pelajaran }}</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Siswa</a></li>
                            <li class="breadcrumb-item"><a href="#">Absen</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail</a></li>
                            <li class="breadcrumb-item"><a href="#">{{ $namaKelas }}</a></li>
                            <li class="breadcrumb-item active">{{ $mapel->nama_mata_pelajaran }}</li>
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
                                <h4 class="card-title">Jadwal {{ $namaKelas }} - {{ $mapel->nama_mata_pelajaran }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal Pertemuan</th>
                                        <th>Status Hadir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapPertemuan as $pertemuan)
                                    <tr>
                                        <td>{{ $pertemuan['tanggal'] }}</td>
                                        <td><span style="font-size: 14px" class="badge fw-medium px-2 {{ $pertemuan['hadir'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $pertemuan['hadir'] ? 'Hadir' : 'Tidak Hadir' }}</span></td>
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