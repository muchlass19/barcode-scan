@extends('layouts.layout')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Tambah Jadwal Pelajaran</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.jadwal-pelajaran.index') }}">Manajemen Jadwal Pelajaran</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
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
                                <h4 class="card-title">Tambah Jadwal Pelajaran</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST">
                            @csrf
                            <div class="mb-3 position-relative">
                                <label for="kelas_id" class="form-label">Kelas</label>
                                <input type="text" class="form-control @error('kelas_id') is-invalid @enderror" id="search_kelas" placeholder="Cari nama kelas...">
                                <input type="hidden" name="kelas_id" id="kelas_id">
                                <div id="guruList" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                                @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 position-relative">
                                <label for="guru_nip" class="form-label">Guru Pengampu</label>
                                <input type="text" class="form-control @error('guru_nip') is-invalid @enderror" id="search_guru" placeholder="Cari nama atau NIP guru...">
                                <input type="hidden" name="guru_nip" id="guru_nip">
                                <div id="guruList" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                                @error('guru_nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 position-relative">
                                <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran</label>
                                <input type="text" class="form-control @error('mata_pelajaran_id') is-invalid @enderror" id="search_mapel" placeholder="Cari nama mata pelajaran...">
                                <input type="hidden" name="mata_pelajaran_id" id="mata_pelajaran_id">
                                <div id="guruList" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                                @error('mata_pelajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="hari" class="form-label">Pilih Hari</label>
                                <div>
                                    <select name="hari" id="hari" class="form-select" aria-label="Pilih Hari">
                                        <option selected>Pilih Hari</option>
                                        <option value="senin">Senin</option>
                                        <option value="selasa">Selasa</option>
                                        <option value="rabu">Rabu</option>
                                        <option value="kamis">Kamis</option>
                                        <option value="jumat">Jumat</option>
                                        <option value="sabtu">Sabtu</option>
                                        <option value="minggu">Minggu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="jam_mulai">Jam Mulai</label>
                                <div>
                                    <input class="form-control" name="jam_mulai" type="time" id="jam_mulai">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="jam_selesai">Jam Selesai</label>
                                <div>
                                    <input class="form-control" name="jam_selesai" type="time" id="jam_selesai">
                                </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const setupSearch = ({ inputId, hiddenId, listId, route, label, value }) => {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const list = document.createElement('div');
        list.id = listId;
        list.classList.add('list-group', 'position-absolute', 'w-100');
        list.style.zIndex = '1000';
        input.parentNode.appendChild(list);

        input.addEventListener('keyup', function () {
            const query = this.value.trim();
            list.innerHTML = '';

            if (query.length < 2) return;

            fetch(`${route}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.length === 0) {
                        const empty = document.createElement('div');
                        empty.classList.add('list-group-item', 'text-muted');
                        empty.textContent = 'Tidak ada hasil.';
                        list.appendChild(empty);
                        return;
                    }

                    data.forEach(item => {
                        const a = document.createElement('a');
                        a.href = '#';
                        a.classList.add('list-group-item', 'list-group-item-action');
                        a.textContent = label(item);
                        a.addEventListener('click', function (e) {
                            e.preventDefault();
                            input.value = label(item);
                            hidden.value = value(item);
                            list.innerHTML = '';
                        });
                        list.appendChild(a);
                    });
                })
                .catch(err => console.error('Error:', err));
        });

        document.addEventListener('click', function (e) {
            if (!list.contains(e.target) && e.target !== input) {
                list.innerHTML = '';
            }
        });
    }

    // ---------- 🔍 PENCARIAN GURU ----------
    setupSearch({
        inputId: 'search_guru',
        hiddenId: 'guru_nip',
        listId: 'guruList',
        route: '{{ route('search.guru') }}',
        label: (guru) => `${guru.nama_lengkap} (${guru.nip})`,
        value: (guru) => guru.nip
    });

    // ---------- 🔍 PENCARIAN KELAS ----------
    setupSearch({
        inputId: 'search_kelas',
        hiddenId: 'kelas_id',
        listId: 'kelasList',
        route: '{{ route('search.kelas') }}',
        label: (kelas) => `${kelas.nama_kelas}`,
        value: (kelas) => kelas.id
    });

    // ---------- 🔍 PENCARIAN MATA PELAJARAN ----------
    setupSearch({
        inputId: 'search_mapel',
        hiddenId: 'mata_pelajaran_id',
        listId: 'mapelList',
        route: '{{ route('search.mapel') }}',
        label: (mapel) => `${mapel.nama_mata_pelajaran}`,
        value: (mapel) => mapel.id
    });
});
</script>
@endpush
