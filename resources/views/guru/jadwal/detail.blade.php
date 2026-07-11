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
                            <li class="breadcrumb-item"><a href="#">Absen</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail</a></li>
                            <li class="breadcrumb-item"><a href="#">XI IPA 2</a></li>
                            <li class="breadcrumb-item active">Bahasa Indonesia</li>
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
                                <h4 class="card-title">Jadwal XI IPA 2 - Bahasa Indonesia</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Hari Mengajar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwalGuru as $key => $group)

                                    @php
                                        $order = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

                                        $first = $group->first();
                                        $hariList = $group->pluck('hari')
                                            ->sortBy(function ($day) use ($order) {
                                                return array_search(strtolower($day), $order);
                                            })
                                            ->implode(', ');
                                        $jadwalIds = $group->pluck('id')->toArray();
                                    @endphp
                                    <tr>
                                        <td>{{ $first->mataPelajaran->nama_mata_pelajaran }}</td>
                                        <td>{{ $first->kelas->nama_kelas }}</td>
                                        <td>{{ $hariList }}</td>
                                        <td class="d-flex gap-2">
                                            <form action="{{ route('guru.pertemuan.set') }}" method="POST">
                                                @csrf
                                                @foreach($jadwalIds as $jid)
                                                <input type="hidden" name="jadwal_ids[]" value="{{ $jid }}">
                                                @endforeach
                                                <input type="hidden" name="kelas_id" value="{{ $first->kelas->id }}">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Detail Pertemuan</button>
                                            </form>

                                            <form action="{{ route('guru.absen.generate') }}" method="POST">
                                                @csrf
                                                @foreach($jadwalIds as $jid)
                                                <input type="hidden" name="jadwal_ids[]" value="{{ $jid }}">
                                                @endforeach
                                                <input type="hidden" name="kelas_id" value="{{ $first->kelas->id }}">
                                                <button class="btn btn-primary btn-sm" type="submit">Generate QR</button>
                                            </form>
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