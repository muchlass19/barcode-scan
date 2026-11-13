<?php

namespace Tests\Feature;

use App\Exports\RekapAbsenExport;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\MataPelajaran;
use App\Models\Pertemuan;
use App\Models\Siswa;
use App\Services\RekapAbsensiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class RekapAbsenExportTest extends TestCase
{
    use RefreshDatabase;

    private Guru $guru;
    private Siswa $siswaHadir;
    private Siswa $siswaTidakHadir;
    private Pertemuan $pertemuan1;
    private Pertemuan $pertemuan2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRekap();
    }

    private function seedRekap(): void
    {
        $this->guru = Guru::create([
            'nip' => '1111111111111111',
            'nama_lengkap' => 'Guru Pengampu',
            'password' => Hash::make('rahasia'),
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'X IPA 1',
            'guru_nip' => $this->guru->nip,
        ]);

        $mapel = MataPelajaran::create(['nama_mata_pelajaran' => 'Matematika']);

        $jadwal = JadwalPelajaran::create([
            'kelas_id' => $kelas->id,
            'guru_nip' => $this->guru->nip,
            'mata_pelajaran_id' => $mapel->id,
            'hari' => 'senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
        ]);

        $this->siswaHadir = Siswa::create([
            'nisn' => '0001000000000001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'password' => Hash::make('rahasia'),
        ]);

        $this->siswaTidakHadir = Siswa::create([
            'nisn' => '0001000000000002',
            'nama_lengkap' => 'Budi Santoso',
            'password' => Hash::make('rahasia'),
        ]);

        foreach ([$this->siswaHadir, $this->siswaTidakHadir] as $siswa) {
            KelasSiswa::create([
                'siswa_nisn' => $siswa->nisn,
                'kelas_id' => $kelas->id,
                'tahun_ajaran' => '2025/2026',
            ]);
        }

        $this->pertemuan1 = Pertemuan::create([
            'jadwal_pelajaran_id' => $jadwal->id,
            'tanggal' => '2025-01-06',
            'token_qr' => 'TOKEN-PERTEMUAN-1',
            'expired_at' => '2025-01-06 09:00:00',
        ]);

        $this->pertemuan2 = Pertemuan::create([
            'jadwal_pelajaran_id' => $jadwal->id,
            'tanggal' => '2025-01-13',
            'token_qr' => 'TOKEN-PERTEMUAN-2',
            'expired_at' => '2025-01-13 09:00:00',
        ]);

        // Pertemuan 1: Ahmad hadir scan QR, Budi ditandai tidak hadir manual.
        Absensi::create([
            'siswa_nisn' => $this->siswaHadir->nisn,
            'pertemuan_id' => $this->pertemuan1->id,
            'tanggal' => '2025-01-06',
            'waktu_scan' => '08:05:00',
            'status_kehadiran' => 1,
            'keterangan' => 'Absen QR',
        ]);

        Absensi::create([
            'siswa_nisn' => $this->siswaTidakHadir->nisn,
            'pertemuan_id' => $this->pertemuan1->id,
            'tanggal' => '2025-01-06',
            'waktu_scan' => '08:30:00',
            'status_kehadiran' => 0,
            'keterangan' => 'Absen manual',
        ]);

        // Pertemuan 2: hanya Ahmad yang absen, Budi tidak punya baris absensi sama sekali.
        Absensi::create([
            'siswa_nisn' => $this->siswaHadir->nisn,
            'pertemuan_id' => $this->pertemuan2->id,
            'tanggal' => '2025-01-13',
            'waktu_scan' => '08:02:00',
            'status_kehadiran' => 1,
            'keterangan' => 'Absen QR',
        ]);
    }

    public function test_detail_rows_memuat_siswa_hadir_dan_tidak_hadir(): void
    {
        $rows = app(RekapAbsensiService::class)->detailRows($this->guru->nip);

        $this->assertCount(4, $rows, 'Harus ada 2 siswa x 2 pertemuan baris.');

        $this->assertSame(
            ['06/01/2025', 'X IPA 1', 'Matematika', $this->siswaHadir->nisn, 'Ahmad Fauzi', 'Hadir', '08:05', 'Absen QR'],
            array_slice($rows[0], 1)
        );

        $this->assertSame(
            ['06/01/2025', 'X IPA 1', 'Matematika', $this->siswaTidakHadir->nisn, 'Budi Santoso', 'Tidak Hadir', '08:30', 'Absen manual'],
            array_slice($rows[1], 1)
        );

        $this->assertSame('Hadir', $rows[2][6]);
        $this->assertSame(
            ['13/01/2025', 'X IPA 1', 'Matematika', $this->siswaTidakHadir->nisn, 'Budi Santoso', 'Tidak Hadir', '-', 'Belum absen'],
            array_slice($rows[3], 1)
        );
    }

    public function test_summary_rows_menghitung_hadir_dan_tidak_hadir(): void
    {
        $rows = app(RekapAbsensiService::class)->summaryRows($this->guru->nip);

        $this->assertCount(2, $rows);

        $this->assertSame(
            ['X IPA 1', 'Matematika', $this->siswaHadir->nisn, 'Ahmad Fauzi', 2, 0, 2, 100.0],
            array_slice($rows[0], 1)
        );

        $this->assertSame(
            ['X IPA 1', 'Matematika', $this->siswaTidakHadir->nisn, 'Budi Santoso', 0, 2, 2, 0.0],
            array_slice($rows[1], 1)
        );
    }

    public function test_rekap_hanya_memuat_data_guru_yang_login(): void
    {
        $guruLain = Guru::create([
            'nip' => '2222222222222222',
            'nama_lengkap' => 'Guru Lain',
            'password' => Hash::make('rahasia'),
        ]);

        $kelasLain = Kelas::create(['nama_kelas' => 'XI IPS 1', 'guru_nip' => $guruLain->nip]);
        $mapelLain = MataPelajaran::create(['nama_mata_pelajaran' => 'Sejarah']);

        $jadwalLain = JadwalPelajaran::create([
            'kelas_id' => $kelasLain->id,
            'guru_nip' => $guruLain->nip,
            'mata_pelajaran_id' => $mapelLain->id,
            'hari' => 'selasa',
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
        ]);

        $siswaLain = Siswa::create([
            'nisn' => '0001000000000003',
            'nama_lengkap' => 'Citra Lestari',
            'password' => Hash::make('rahasia'),
        ]);

        KelasSiswa::create([
            'siswa_nisn' => $siswaLain->nisn,
            'kelas_id' => $kelasLain->id,
            'tahun_ajaran' => '2025/2026',
        ]);

        Pertemuan::create([
            'jadwal_pelajaran_id' => $jadwalLain->id,
            'tanggal' => '2025-01-07',
            'token_qr' => 'TOKEN-GURU-LAIN',
            'expired_at' => '2025-01-07 11:00:00',
        ]);

        $rows = collect(app(RekapAbsensiService::class)->detailRows($this->guru->nip));
        $this->assertCount(4, $rows);
        $this->assertFalse($rows->contains(fn (array $row) => $row[5] === 'Citra Lestari'));

        $rowsGuruLain = app(RekapAbsensiService::class)->detailRows($guruLain->nip);
        $this->assertCount(1, $rowsGuruLain);
        $this->assertSame('Citra Lestari', $rowsGuruLain[0][5]);
        $this->assertSame('Tidak Hadir', $rowsGuruLain[0][6]);
    }

    public function test_siswa_di_luar_daftar_kelas_yang_scan_tetap_muncul(): void
    {
        $siswaLuarKelas = Siswa::create([
            'nisn' => '0001000000000009',
            'nama_lengkap' => 'Zaki Tanpa Kelas',
            'password' => Hash::make('rahasia'),
        ]);

        Absensi::create([
            'siswa_nisn' => $siswaLuarKelas->nisn,
            'pertemuan_id' => $this->pertemuan1->id,
            'tanggal' => '2025-01-06',
            'waktu_scan' => '08:10:00',
            'status_kehadiran' => 1,
            'keterangan' => 'Absen QR',
        ]);

        $rowsPertemuan1 = collect(app(RekapAbsensiService::class)->detailRows($this->guru->nip))
            ->filter(fn (array $row) => $row[1] === '06/01/2025')
            ->values();

        $this->assertCount(3, $rowsPertemuan1, 'Pertemuan 1 harus memuat 2 siswa kelas + 1 siswa di luar daftar kelas.');
        $this->assertSame('Zaki Tanpa Kelas', $rowsPertemuan1[2][5]);
        $this->assertSame('Hadir', $rowsPertemuan1[2][6]);

        $barisZaki = collect(app(RekapAbsensiService::class)->summaryRows($this->guru->nip))
            ->first(fn (array $row) => $row[4] === 'Zaki Tanpa Kelas');

        $this->assertNotNull($barisZaki, 'Siswa di luar daftar kelas harus ikut masuk sheet ringkasan.');
        $this->assertSame([1, 1, 2, 50.0], array_slice($barisZaki, 5));
    }

    public function test_workbook_punya_dua_sheet_dan_isi_sesuai(): void
    {
        $workbook = new RekapAbsenExport($this->guru->nip);
        $sheets = $workbook->sheets();

        $this->assertCount(2, $sheets);
        $this->assertSame('Rekap Absensi', $sheets[0]->title());
        $this->assertSame('Rekap Kehadiran', $sheets[1]->title());

        $file = 'rekap-absen-test-' . uniqid() . '.xlsx';
        Excel::store($workbook, $file, 'local');

        try {
            $path = Storage::disk('local')->path($file);
            $this->assertFileExists($path);

            $spreadsheet = IOFactory::load($path);

            $detail = $spreadsheet->getSheet(0)->toArray();
            $this->assertSame(
                ['No', 'Tanggal', 'Kelas', 'Mata Pelajaran', 'NISN', 'Nama Siswa', 'Status', 'Jam Scan', 'Keterangan'],
                $detail[0]
            );
            $this->assertCount(5, $detail, '1 baris heading + 4 baris siswa/pertemuan.');
            $this->assertSame('Tidak Hadir', $detail[4][6]);
            $this->assertSame('Belum absen', $detail[4][8]);

            $ringkasan = $spreadsheet->getSheet(1)->toArray();
            $this->assertSame('Nama Siswa', $ringkasan[0][4]);
            $this->assertSame('Budi Santoso', $ringkasan[2][4]);
            $this->assertSame(0, (int) $ringkasan[2][5], 'Jumlah hadir 0 harus tertulis di sel, bukan sel kosong.');
            $this->assertSame(2, (int) $ringkasan[2][6]);
        } finally {
            Storage::disk('local')->delete($file);
        }
    }

    public function test_route_rekap_absen_mengirim_file_excel(): void
    {
        $this->actingAs($this->guru, 'guru')
            ->get(route('guru.rekap.export'))
            ->assertOk()
            ->assertDownload('rekap-absensi.xlsx');
    }

    public function test_route_rekap_absen_menolak_guru_tanpa_pertemuan(): void
    {
        $guruKosong = Guru::create([
            'nip' => '3333333333333333',
            'nama_lengkap' => 'Guru Tanpa Jadwal',
            'password' => Hash::make('rahasia'),
        ]);

        $this->actingAs($guruKosong, 'guru')
            ->from(route('guru.jadwal.index'))
            ->get(route('guru.rekap.export'))
            ->assertRedirect(route('guru.jadwal.index'))
            ->assertSessionHas('error');
    }
}
