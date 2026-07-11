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
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.jadwal-pelajaran.index') }}">Manajemen Jadwal Kelas</a></li>
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
                            <a href="#" 
                               class="text-primary text-decoration-none edit-jadwal-btn"
                               data-id="{{ $jadwal['jadwal_id'] }}"
                               data-hari="{{ $hari }}"
                               data-mapel="{{ $jadwal['nama_mata_pelajaran'] }}"
                               data-mapel_id="{{ $jadwal['mata_pelajaran_id'] ?? '' }}"
                               data-guru="{{ $jadwal['guru'] }}"
                               data-guru_nip="{{ $jadwal['guru_nip'] ?? '' }}"
                               data-jam_mulai="{{ $jadwal['jam_mulai'] }}"
                               data-jam_selesai="{{ $jadwal['jam_selesai'] }}">
                                {{ $jadwal['nama_mata_pelajaran'] }}
                            </a>
                        </h6>
                        <p class="text-muted mb-0">{{ $jadwal['guru'] }}</p>
                        <small class="text-muted">{{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }}</small>
                    </div>
                    @empty
                    <div class="card-body pt-2">
                        <p class="text-muted mb-0">Belum ada jadwal.</p>
                    </div>
                    @endforelse

                    {{-- Tombol Tambah Jadwal --}}
                    <div class="p-2">
                        <button 
                            type="button" 
                            class="btn btn-primary w-100 open-jadwal-btn"
                            data-bs-toggle="modal" 
                            data-bs-target="#tambahJadwalModal"
                            data-hari="{{ $hari }}"
                            data-hari-label="{{ $label }}">
                            Tambah Jadwal {{ $label }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ================= MODAL TAMBAH ================= --}}
        <div class="modal fade" id="tambahJadwalModal" tabindex="-1" aria-labelledby="tambahJadwalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Jadwal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Kelas --}}
                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" class="form-control" value="{{ $data['nama_kelas'] }}" readonly>
                                <input type="hidden" name="kelas_id" value="{{ $data['kelas_id'] }}">
                            </div>

                            {{-- Hari --}}
                            <div class="mb-3">
                                <label class="form-label">Hari</label>
                                <input type="text" id="hari_text" class="form-control" readonly>
                                <input type="hidden" id="hari" name="hari">
                            </div>

                            {{-- Mapel --}}
                            <div class="mb-3 position-relative">
                                <label class="form-label">Mata Pelajaran</label>
                                <input type="text" id="search_mapel" class="form-control" placeholder="Cari mata pelajaran..." autocomplete="off">
                                <input type="hidden" id="mata_pelajaran_id" name="mata_pelajaran_id">
                            </div>

                            {{-- Guru --}}
                            <div class="mb-3 position-relative">
                                <label class="form-label">Guru</label>
                                <input type="text" id="search_guru" class="form-control" placeholder="Cari guru..." autocomplete="off">
                                <input type="hidden" id="guru_nip" name="guru_nip">
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label class="form-label">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL EDIT ================= --}}
        <div class="modal fade" id="editJadwalModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="editJadwalForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Jadwal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_jadwal_id" name="jadwal_id">

                            <div class="mb-3 position-relative">
                                <label class="form-label">Mata Pelajaran</label>
                                <input type="text" id="edit_search_mapel" class="form-control" placeholder="Cari Mata Pelajaran...">
                                <input type="hidden" id="edit_mata_pelajaran_id" name="mata_pelajaran_id">
                            </div>

                            <div class="mb-3 position-relative">
                                <label class="form-label">Guru</label>
                                <input type="text" id="edit_search_guru" class="form-control" placeholder="Cari Guru...">
                                <input type="hidden" id="edit_guru_nip" name="guru_nip">
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label class="form-label">Jam Mulai</label>
                                    <input type="time" id="edit_jam_mulai" name="jam_mulai" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Jam Selesai</label>
                                    <input type="time" id="edit_jam_selesai" name="jam_selesai" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Hapus</button>
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-success" type="submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= KONFIRMASI HAPUS ================= --}}
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="deleteJadwalForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Yakin ingin menghapus jadwal ini?</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-danger" type="submit">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =============== SETUP SEARCH (reusable) ===============
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
                    if (!data.length) {
                        list.innerHTML = '<div class="list-group-item text-muted">Tidak ada hasil.</div>';
                        return;
                    }
                    data.forEach(item => {
                        const a = document.createElement('a');
                        a.href = '#';
                        a.classList.add('list-group-item', 'list-group-item-action');
                        a.textContent = label(item);
                        a.onclick = e => {
                            e.preventDefault();
                            input.value = label(item);
                            hidden.value = value(item);
                            list.innerHTML = '';
                        };
                        list.appendChild(a);
                    });
                })
        });

        document.addEventListener('click', e => {
            if (!list.contains(e.target) && e.target !== input) list.innerHTML = '';
        });
    };

    // Tambah jadwal: setup pencarian
    setupSearch({
        inputId: 'search_mapel',
        hiddenId: 'mata_pelajaran_id',
        listId: 'mapelList',
        route: '{{ route('search.mapel') }}',
        label: mapel => mapel.nama_mata_pelajaran,
        value: mapel => mapel.id
    });

    setupSearch({
        inputId: 'search_guru',
        hiddenId: 'guru_nip',
        listId: 'guruList',
        route: '{{ route('search.guru') }}',
        label: guru => `${guru.nama_lengkap} (${guru.nip})`,
        value: guru => guru.nip
    });

    // =============== TAMBAH ===============
    document.querySelectorAll('.open-jadwal-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('hari').value = btn.dataset.hari;
            document.getElementById('hari_text').value = btn.dataset.hariLabel;
        });
    });

    // =============== EDIT ===============
    document.querySelectorAll('.edit-jadwal-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();

            const modal = new bootstrap.Modal(document.getElementById('editJadwalModal'));
            const id = btn.dataset.id;

            // Set nilai ke form
            document.getElementById('edit_jadwal_id').value = id;
            document.getElementById('edit_search_mapel').value = btn.dataset.mapel;
            document.getElementById('edit_mata_pelajaran_id').value = btn.dataset.mapel_id;
            document.getElementById('edit_search_guru').value = `${btn.dataset.guru} (${btn.dataset.guru_nip})`;
            document.getElementById('edit_guru_nip').value = btn.dataset.guru_nip;
            document.getElementById('edit_jam_mulai').value = btn.dataset.jam_mulai;
            document.getElementById('edit_jam_selesai').value = btn.dataset.jam_selesai;

            // Set action form
            document.getElementById('editJadwalForm').action = `/admin/jadwal-pelajaran/${id}`;
            document.getElementById('deleteJadwalForm').action = `/admin/jadwal-pelajaran/${id}`;

            modal.show();
        });
    });

    // Edit: setup pencarian dinamis juga
    setupSearch({
        inputId: 'edit_search_mapel',
        hiddenId: 'edit_mata_pelajaran_id',
        listId: 'editMapelList',
        route: '{{ route('search.mapel') }}',
        label: m => m.nama_mata_pelajaran,
        value: m => m.id
    });

    setupSearch({
        inputId: 'edit_search_guru',
        hiddenId: 'edit_guru_nip',
        listId: 'editGuruList',
        route: '{{ route('search.guru') }}',
        label: g => `${g.nama_lengkap} (${g.nip})`,
        value: g => g.nip
    });
});
</script>
@endpush

@endsection
