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
                                <h4 class="card-title">Jadwal {{ $guru->nama_lengkap }}</h4>
                            </div>
                            <div class="col-2">
                                <div class="row justify-content-end">
                                    <a href="{{ route('guru.rekap.export') }}" class="btn btn-success btn-sm">
                                        Rekap Absen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwalGuru as $jadwal)
                                    <tr>
                                        <td>{{ $jadwal->kelas->nama_kelas }}</td>
                                        <td class="d-flex gap-2 text-end">
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('guru.jadwal.detail') }}">Detail</a>
                                            <!-- <form action="{{ route('guru.absen.generate') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="jadwal_pelajaran_id" value="{{ $jadwal->id }}">
                                                <input type="hidden" name="kelas_id" value="{{ $jadwal->kelas->id }}">
                                                <button class="btn btn-primary btn-sm" type="submit">Generate QR</button>
                                            </form> -->
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