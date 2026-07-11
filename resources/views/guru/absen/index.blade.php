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
                            <li class="breadcrumb-item"><a href="#">Absen Manual</a></li>
                            <li class="breadcrumb-item active"></li>
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
                                <h4 class="card-title">Absen Mata Pelajaran</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kelasSiswa as $kelas)
                                    <tr>
                                        <td>{{ $kelas->siswa->nama_lengkap }}</td>
                                        <td>
                                            <form action="{{ route('guru.processManual', $kelas->siswa->nisn) }}" method="POST">
                                                @csrf
                                                <div class="form-check form-switch form-switch-success">
                                                    <input type="hidden" name="is_absen" value="0">
                                                    <input name="is_absen" class="form-check-input" type="checkbox" value="1" onChange="this.form.submit()" {{ $statusKehadiran[$kelas->siswa->nisn] == 1 ? 'checked' : '' }}>
                                                    <input type="hidden" name="pertemuan_id" value="{{ $pertemuan_id }}">
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            Belum ada siswa di kelas ini.
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