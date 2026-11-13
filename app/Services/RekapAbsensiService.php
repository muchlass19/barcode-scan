<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\KelasSiswa;
use App\Models\Pertemuan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Menyusun data rekap absensi (hadir & tidak hadir) untuk seorang guru.
 *
 * Siswa yang tidak hadir tidak punya baris di tabel `absensi`, jadi rekap
 * dibangun dari daftar pertemuan x daftar siswa di kelas tersebut, bukan dari
 * tabel absensi saja.
 */
class RekapAbsensiService
{
    public const KETERANGAN_BELUM_ABSEN = 'Belum absen';

    /**
     * Semua pertemuan dari jadwal milik guru, urut tanggal.
     */
    public function pertemuanByGuru(string $guruNip): Collection
    {
        return Pertemuan::with(['jadwalPelajaran.kelas', 'jadwalPelajaran.mataPelajaran'])
            ->whereHas('jadwalPelajaran', function ($query) use ($guruNip) {
                $query->where('guru_nip', $guruNip);
            })
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();
    }

    /**
     * Daftar siswa per kelas (unik per nisn, urut nama).
     *
     * @param  array<int, int|string>  $kelasIds
     * @return Collection<int, Collection<int, \App\Models\Siswa>>
     */
    public function siswaPerKelas(array $kelasIds): Collection
    {
        if (empty($kelasIds)) {
            return collect();
        }

        return KelasSiswa::with('siswa')
            ->whereIn('kelas_id', $kelasIds)
            ->get()
            ->groupBy('kelas_id')
            ->map(function (Collection $rows) {
                return $rows->pluck('siswa')
                    ->filter()
                    ->unique('nisn')
                    ->sortBy('nama_lengkap')
                    ->values();
            });
    }

    /**
     * Daftar siswa yang direkap per pertemuan: siswa di kelas tersebut
     * ditambah siswa di luar daftar kelas yang tetap punya baris absensi
     * (scan tidak memvalidasi keanggotaan kelas, jangan sampai datanya hilang).
     *
     * @param  Collection<int, Pertemuan>  $pertemuanList
     * @param  Collection<int, Collection<int, \App\Models\Siswa>>  $siswaPerKelas
     * @param  Collection<string, Absensi>  $absensi
     * @return Collection<int, Collection<int, \App\Models\Siswa>>
     */
    protected function siswaPerPertemuan(
        Collection $pertemuanList,
        Collection $siswaPerKelas,
        Collection $absensi
    ): Collection {
        $perPertemuan = collect();

        foreach ($pertemuanList as $pertemuan) {
            $kelasId = $pertemuan->jadwalPelajaran->kelas_id ?? null;
            $daftarKelas = $siswaPerKelas->get($kelasId, collect());

            $diLuarKelas = $absensi
                ->filter(function (Absensi $absen) use ($pertemuan) {
                    return (int) $absen->pertemuan_id === (int) $pertemuan->id;
                })
                ->map(function (Absensi $absen) {
                    return $absen->siswa;
                })
                ->filter()
                ->reject(function ($siswa) use ($daftarKelas) {
                    return $daftarKelas->contains('nisn', $siswa->nisn);
                });

            $perPertemuan->put(
                $pertemuan->id,
                $daftarKelas->concat($diLuarKelas)->sortBy('nama_lengkap')->values()
            );
        }

        return $perPertemuan;
    }

    /**
     * Baris detail: satu baris per pertemuan per siswa (hadir maupun tidak hadir).
     *
     * Urutan kolom: No, Tanggal, Kelas, Mata Pelajaran, NISN, Nama Siswa,
     * Status, Jam Scan, Keterangan.
     */
    public function detailRows(string $guruNip): array
    {
        $pertemuanList = $this->pertemuanByGuru($guruNip);

        if ($pertemuanList->isEmpty()) {
            return [];
        }

        $siswaPerKelas = $this->siswaPerKelas($this->kelasIds($pertemuanList));
        $absensi = $this->absensiKeyed($pertemuanList->pluck('id'));
        $siswaPerPertemuan = $this->siswaPerPertemuan($pertemuanList, $siswaPerKelas, $absensi);

        $rows = [];
        $nomor = 0;

        foreach ($pertemuanList as $pertemuan) {
            $jadwal = $pertemuan->jadwalPelajaran;

            foreach ($siswaPerPertemuan->get($pertemuan->id, collect()) as $siswa) {
                $absen = $absensi->get($this->key($pertemuan->id, $siswa->nisn));

                $nomor++;

                $rows[] = [
                    $nomor,
                    $this->tanggal($pertemuan->tanggal),
                    $jadwal->kelas->nama_kelas ?? '-',
                    $jadwal->mataPelajaran->nama_mata_pelajaran ?? '-',
                    $siswa->nisn,
                    $siswa->nama_lengkap,
                    $this->status($absen),
                    $absen && $absen->waktu_scan ? substr((string) $absen->waktu_scan, 0, 5) : '-',
                    $absen ? ($absen->keterangan ?: '-') : self::KETERANGAN_BELUM_ABSEN,
                ];
            }
        }

        return $rows;
    }

    /**
     * Baris ringkasan: satu baris per kelas + mata pelajaran per siswa.
     *
     * Urutan kolom: No, Kelas, Mata Pelajaran, NISN, Nama Siswa, Hadir,
     * Tidak Hadir, Total Pertemuan, Persentase Kehadiran.
     */
    public function summaryRows(string $guruNip): array
    {
        $pertemuanList = $this->pertemuanByGuru($guruNip);

        if ($pertemuanList->isEmpty()) {
            return [];
        }

        $siswaPerKelas = $this->siswaPerKelas($this->kelasIds($pertemuanList));
        $absensi = $this->absensiKeyed($pertemuanList->pluck('id'));
        $siswaPerPertemuan = $this->siswaPerPertemuan($pertemuanList, $siswaPerKelas, $absensi);

        $groups = $pertemuanList->groupBy(function (Pertemuan $pertemuan) {
            return $pertemuan->jadwalPelajaran->kelas_id . '-' . $pertemuan->jadwalPelajaran->mata_pelajaran_id;
        });

        $rows = [];
        $nomor = 0;

        foreach ($groups as $group) {
            $first = $group->first();
            $jadwal = $first->jadwalPelajaran;
            $totalPertemuan = $group->count();

            $daftarSiswa = collect();

            foreach ($group as $pertemuan) {
                $daftarSiswa = $daftarSiswa->concat($siswaPerPertemuan->get($pertemuan->id, collect()));
            }

            $daftarSiswa = $daftarSiswa->unique('nisn')->sortBy('nama_lengkap')->values();

            foreach ($daftarSiswa as $siswa) {
                $hadir = 0;

                foreach ($group as $pertemuan) {
                    $absen = $absensi->get($this->key($pertemuan->id, $siswa->nisn));

                    if ($absen && (bool) $absen->status_kehadiran) {
                        $hadir++;
                    }
                }

                $nomor++;

                $rows[] = [
                    $nomor,
                    $jadwal->kelas->nama_kelas ?? '-',
                    $jadwal->mataPelajaran->nama_mata_pelajaran ?? '-',
                    $siswa->nisn,
                    $siswa->nama_lengkap,
                    $hadir,
                    $totalPertemuan - $hadir,
                    $totalPertemuan,
                    $totalPertemuan > 0 ? round($hadir / $totalPertemuan * 100, 2) : 0,
                ];
            }
        }

        return $rows;
    }

    /**
     * @param  Collection<int, Pertemuan>  $pertemuanList
     * @return array<int, int>
     */
    protected function kelasIds(Collection $pertemuanList): array
    {
        return $pertemuanList
            ->map(function (Pertemuan $pertemuan) {
                return $pertemuan->jadwalPelajaran->kelas_id ?? null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, int>  $pertemuanIds
     * @return Collection<string, Absensi>
     */
    protected function absensiKeyed(Collection $pertemuanIds): Collection
    {
        return Absensi::with('siswa')
            ->whereIn('pertemuan_id', $pertemuanIds)
            ->get()
            ->keyBy(function (Absensi $absensi) {
                return $this->key($absensi->pertemuan_id, $absensi->siswa_nisn);
            });
    }

    protected function key($pertemuanId, $nisn): string
    {
        return $pertemuanId . '-' . $nisn;
    }

    protected function status(?Absensi $absensi): string
    {
        return $absensi && (bool) $absensi->status_kehadiran ? 'Hadir' : 'Tidak Hadir';
    }

    protected function tanggal($tanggal): string
    {
        return $tanggal ? Carbon::parse($tanggal)->format('d/m/Y') : '-';
    }
}
