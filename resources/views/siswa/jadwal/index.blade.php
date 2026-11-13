@extends('layouts.layout')
@section('content')

@php
$hariList = [
    'senin' => 'Senin',
    'selasa' => 'Selasa',
    'rabu' => 'Rabu',
    'kamis' => 'Kamis',
    'jumat' => 'Jumat',
    'sabtu' => 'Sabtu'
];
@endphp

<div class="page-content">
    <div class="container-fluid">

        {{-- ================= HEADER ================= --}}
        <div class="row mb-3">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Jadwal Kelas {{ $data['nama_kelas'] }}</h4>
                    <div>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Siswa</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.jadwal-pelajaran.index') }}">Jadwal Kelas</a></li>
                            <li class="breadcrumb-item active">{{ $data['nama_kelas'] }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= LIST JADWAL ================= --}}
        <div class="row">
            @foreach($hariList as $hari => $label)
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $label }}</h5>
                    </div>

                    {{-- Daftar Jadwal --}}
                    @forelse($data['hari'][$hari] ?? [] as $jadwal)
                    <div class="card-body pt-0">
                        <h6>
                            <form action="{{ route('siswa.jadwal.setDetail') }}" method="POST" class="text-primary text-decoration-none edit-jadwal-btn">
                                @csrf
                                <input type="hidden" name="mata_pelajaran_id" value="{{ $jadwal['mata_pelajaran_id'] }}">
                                <input type="hidden" name="jadwal_pelajaran_id" value="{{ $jadwal['jadwal_id'] }}">
                                <button type="submit" style="background: none; border: none; padding: 0" class="text-primary fw-bold">{{ $jadwal['nama_mata_pelajaran'] }}</button>
                            </form>
                        </h6>
                        <p class="text-muted mb-0">{{ $jadwal['guru'] }}</p>
                        <small class="text-muted">{{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }} WIB</small>
                    </div>
                    @empty
                    <div class="card-body pt-2">
                        <p class="text-muted mb-0">Belum ada jadwal.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
