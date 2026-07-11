@extends('layouts.layout')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Manajemen Kelas</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item active">Manajemen Kelas</li>
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
                                <h4 class="card-title">Manajemen Kelas</h4>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.kelas.tambah') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Tambah Data Kelas</a>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fa-solid fa-file-excel me-1"></i>Import Excel
                                </button>
                                <a href="{{ route('admin.kelas.download') }}" class="btn btn-outline-info">
                                    <i class="fa-solid fa-download me-1"></i>Download Template Excel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Wali Kelas</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kelasList as $kelas)
                                    <tr>
                                        <td>{{ $kelas->nama_kelas }}</td>
                                        <td>{{ $kelas->guru->nama_lengkap }} ({{ $kelas->guru_nip }})</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.kelas.detail.index', $kelas->id) }}" class="btn btn-primary btn-sm">Detail</a>
                                            <a href="{{ route('admin.kelas.ubah', $kelas->id) }}" class="btn btn-warning btn-sm">Ubah</a>
                                            <form action="{{ route('admin.kelas.hapus', $kelas->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-nama="{{ $kelas->nama_kelas }}" data-swal-template="#deleteData">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            Belum ada data kelas.
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

        <!-- Modal Import -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.kelas.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Data Kelas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File Excel</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format yang didukung: .xlsx dan .xls</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection