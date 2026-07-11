@extends('layouts.layout')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Ubah Data Kelas</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.kelas.index') }}">Manajemen Kelas</a></li>
                            <li class="breadcrumb-item active">Ubah Data</li>
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
                                <h4 class="card-title">Ubah Data Kelas</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form action="{{ route('admin.kelas.update', $kelas->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="namaKelas" class="form-label">Nama Kelas</label>
                                <input
                                    type="text"
                                    class="form-control @error('nama_kelas') is-invalid @enderror"
                                    name="nama_kelas"
                                    id="namaKelas"
                                    placeholder="Contoh: XII IPA 1"
                                    value="{{ old('nama_kelas', $kelas->nama_kelas) }}">
                                @error('nama_kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="guru_nip" class="form-label">Wali Kelas (Guru)</label>
                                <input
                                    type="text"
                                    class="form-control @error('guru_nip') is-invalid @enderror"
                                    id="search_guru"
                                    placeholder="Cari nama atau NIP guru..."
                                    value="{{ old('guru_nip') ? $guru_nama : ($kelas->guru ? $kelas->guru->nama_lengkap . ' (' . $kelas->guru->nip . ')' : '') }}">
                                
                                <input type="hidden" name="guru_nip" id="guru_nip" value="{{ old('guru_nip', $kelas->guru_nip) }}">
                                <div id="guruList" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                                
                                @error('guru_nip')
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
    const searchInput = document.getElementById('search_guru');
    const guruList = document.getElementById('guruList');
    const guruNipInput = document.getElementById('guru_nip');

    searchInput.addEventListener('keyup', function () {
        const query = this.value.trim();
        guruList.innerHTML = '';

        if (query.length < 2) return;

        fetch(`{{ route('search.guru') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                guruList.innerHTML = '';

                data.forEach(guru => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.textContent = `${guru.nama_lengkap} (${guru.nip})`;
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        searchInput.value = `${guru.nama_lengkap} (${guru.nip})`;
                        guruNipInput.value = guru.nip;
                        guruList.innerHTML = '';
                    });
                    guruList.appendChild(item);
                });
            });
    });

    // Klik di luar dropdown = sembunyikan daftar
    document.addEventListener('click', function (e) {
        if (!guruList.contains(e.target) && e.target !== searchInput) {
            guruList.innerHTML = '';
        }
    });
});
</script>
@endpush
