@extends('layouts.layout')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Tambah Data Siswa Kelas {{ $kelas->nama_kelas }}</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.kelas.index') }}">Manajemen Kelas</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.kelas.detail.index', $kelas->id) }}">{{ $kelas->tahun_ajaran }}</a></li>
                            <li class="breadcrumb-item active">Tambah Data Siswa</li>
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
                                <h4 class="card-title">Tambah Data Siswa Kelas {{ $kelas->nama_kelas }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form action="{{ route('admin.kelas.detail.store', $kelas->id) }}" method="POST">
                            @csrf
                            <div class="mb-3 position-relative">
                                <label for="siswa_nisn" class="form-label">NISN Siswa</label>
                                <input type="text" class="form-control @error('siswa_nisn') is-invalid @enderror" id="search_siswa" placeholder="Cari nama atau NISN siswa...">
                                <input type="hidden" name="siswa_nisn" id="siswa_nisn">
                                <div id="siswaList" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                                @error('siswa_nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tahunAjaran" class="form-label">Tahun Ajaran</label>
                                <input type="text" class="form-control @error('tahun_ajaran') is-invalid @enderror" name="tahun_ajaran" id="namaKelas" aria-describedby="namaKelas" placeholder="Contoh: 2025/2026" value="{{ old('tahun_ajaran') }}">
                                @error('tahun_ajaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline-danger">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search_siswa');
    const siswaList = document.getElementById('siswaList');
    const siswaNisnInput = document.getElementById('siswa_nisn');

    searchInput.addEventListener('keyup', function () {
        const query = this.value.trim();
        siswaList.innerHTML = '';

        if (query.length < 1) return;

        fetch(`{{ route('search.siswa') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                siswaList.innerHTML = '';

                if (data.empty === true) {
                    const msg = document.createElement('div');
                    msg.classList.add('list-group-item');
                    msg.textContent = data.message;
                    siswaList.appendChild(msg);
                    return;
                }

                data.forEach(siswa => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.textContent = `${siswa.nama_lengkap} (${siswa.nisn})`;
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        searchInput.value = `${siswa.nama_lengkap} (${siswa.nisn})`;
                        siswaNisnInput.value = siswa.nisn;
                        siswaList.innerHTML = '';
                    });
                    siswaList.appendChild(item);
                });
            });
    });

    // Klik di luar dropdown = sembunyikan daftar
    document.addEventListener('click', function (e) {
        if (!siswaList.contains(e.target) && e.target !== searchInput) {
            siswaList.innerHTML = '';
        }
    });
});
</script>
@endpush