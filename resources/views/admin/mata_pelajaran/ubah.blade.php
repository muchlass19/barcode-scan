@extends('layouts.layout')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Ubah Mata Pelajaran</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.mata-pelajaran.index') }}">Manajemen Mata Pelajaran</a></li>
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
                                <h4 class="card-title">Ubah Mata Pelajaran</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form action="{{ route('admin.mata-pelajaran.update', $mapel->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="namaMataPelajaran" class="form-label">Nama Mata Pelajaran</label>
                                <input type="text" class="form-control @error('nama_mata_pelajaran') is-invalid @enderror" name="nama_mata_pelajaran" id="namaMataPelajaran" aria-describedby="namaMataPelajaran" placeholder="Masukkan nama mata pelajaran" value="{{ old('nama_mata_pelajaran', $mapel->nama_mata_pelajaran) }}">
                                @error('nama_mata_pelajaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-outline-danger">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection