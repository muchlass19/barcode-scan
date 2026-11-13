<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\MataPelajaran;
use App\Models\Pertemuan;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Semua jam disimpan & ditampilkan sebagai WIB (GMT+7 / Asia/Jakarta).
 */
class TimezoneJakartaTest extends TestCase
{
    use RefreshDatabase;

    private Guru $guru;
    private Siswa $siswa;
    private JadwalPelajaran $jadwal;
    private Pertemuan $pertemuan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = Guru::create([
            'nip' => '4444444444444444',
            'nama_lengkap' => 'Guru WIB',
            'password' => Hash::make('rahasia'),
        ]);

        $kelas = Kelas::create(['nama_kelas' => 'XII IPA 3', 'guru_nip' => $this->guru->nip]);
        $mapel = MataPelajaran::create(['nama_mata_pelajaran' => 'Fisika']);

        // 15 Januari 2026 = kamis
        $this->jadwal = JadwalPelajaran::create([
            'kelas_id' => $kelas->id,
            'guru_nip' => $this->guru->nip,
            'mata_pelajaran_id' => $mapel->id,
            'hari' => 'kamis',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '08:00:00',
        ]);

        $this->siswa = Siswa::create([
            'nisn' => '0007000000000001',
            'nama_lengkap' => 'Siswa WIB',
            'password' => Hash::make('rahasia'),
        ]);

        KelasSiswa::create([
            'siswa_nisn' => $this->siswa->nisn,
            'kelas_id' => $kelas->id,
            'tahun_ajaran' => '2025/2026',
        ]);

        $this->pertemuan = Pertemuan::create([
            'jadwal_pelajaran_id' => $this->jadwal->id,
            'tanggal' => '2026-01-15',
            'token_qr' => 'TOKEN-WIB',
            'expired_at' => '2026-01-15 08:00:00',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_zona_waktu_aplikasi_adalah_wib(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', date_default_timezone_get());
        $this->assertSame('Asia/Jakarta', Carbon::now()->getTimezone()->getName());
        $this->assertSame(25200, Carbon::now()->getOffset(), 'Offset harus GMT+7 (25200 detik).');
        $this->assertSame('+07:00', Carbon::now()->format('P'));
    }

    public function test_absen_manual_menyimpan_jam_dan_tanggal_wib(): void
    {
        // 00:30 WIB = 17:30 UTC (hari sebelumnya) — kalau salah tz, tanggalnya jadi 14 Jan.
        Carbon::setTestNow(Carbon::create(2026, 1, 15, 0, 30, 0, 'Asia/Jakarta'));

        $this->actingAs($this->guru, 'guru')
            ->post(route('guru.processManual', $this->siswa->nisn), [
                'is_absen' => '1',
                'pertemuan_id' => $this->pertemuan->id,
            ])
            ->assertRedirect();

        $absensi = Absensi::firstOrFail();

        $this->assertSame('00:30:00', $absensi->waktu_scan);
        $this->assertSame('2026-01-15', $absensi->tanggal);
        $this->assertSame(1, (int) $absensi->status_kehadiran);
    }

    public function test_scan_qr_menyimpan_jam_dan_tanggal_wib(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 15, 7, 59, 0, 'Asia/Jakarta'));

        $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.absen.scan', [
                'pertemuan_id' => $this->pertemuan->id,
                'token' => $this->pertemuan->token_qr,
            ]))
            ->assertRedirect(route('siswa.index'));

        $absensi = Absensi::firstOrFail();

        $this->assertSame('07:59:00', $absensi->waktu_scan);
        $this->assertSame('2026-01-15', $absensi->tanggal);
        $this->assertSame(1, (int) $absensi->status_kehadiran);
    }

    public function test_kadaluarsa_qr_dibandingkan_dengan_jam_wib(): void
    {
        // 07:59 WIB masih boleh, 08:01 WIB sudah lewat (expired_at = 08:00:00 WIB).
        Carbon::setTestNow(Carbon::create(2026, 1, 15, 8, 1, 0, 'Asia/Jakarta'));

        $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.absen.scan', [
                'pertemuan_id' => $this->pertemuan->id,
                'token' => $this->pertemuan->token_qr,
            ]))
            ->assertSessionHas('error');

        $this->assertSame(0, Absensi::count(), 'Absensi kedaluwarsa tidak boleh tersimpan.');
    }

    public function test_generate_qr_memakai_tanggal_dan_jam_wib(): void
    {
        // 00:30 WIB hari kamis = 17:30 UTC hari rabu: jangan sampai salah hari/tanggal.
        Carbon::setTestNow(Carbon::create(2026, 1, 15, 0, 30, 0, 'Asia/Jakarta'));

        $this->pertemuan->delete();

        $this->actingAs($this->guru, 'guru')
            ->post(route('guru.absen.generate'), [
                'jadwal_ids' => [$this->jadwal->id],
                'kelas_id' => $this->jadwal->kelas_id,
            ])
            ->assertRedirect();

        $pertemuan = Pertemuan::firstOrFail();

        $this->assertSame('2026-01-15', $pertemuan->tanggal);
        // expired_at dibaca dari DB sebagai Carbon zona WIB, bukan UTC.
        $this->assertSame('2026-01-15 08:00:00', $pertemuan->expired_at->format('Y-m-d H:i:s'));
        $this->assertSame('Asia/Jakarta', $pertemuan->expired_at->getTimezone()->getName());

        $this->assertDatabaseHas('pertemuan', [
            'id' => $pertemuan->id,
            'tanggal' => '2026-01-15',
        ]);

        $this->assertSame(
            '2026-01-15 08:00:00',
            DB::table('pertemuan')->where('id', $pertemuan->id)->value('expired_at'),
            'Jam expired harus tersimpan apa adanya (waktu WIB) di kolom datetime.'
        );
    }

    public function test_halaman_qr_menampilkan_jam_expired_wib(): void
    {
        // 00:30 WIB = 17:30 UTC hari sebelumnya. Kalau tampilannya ikut UTC,
        // jam ini akan muncul sebagai 17:30.
        $this->pertemuan->update(['expired_at' => '2026-01-15 00:30:00']);

        $this->actingAs($this->guru, 'guru')
            ->get(route('guru.absen.qr', $this->pertemuan->id))
            ->assertOk()
            ->assertSee('15 Januari 2026', false)
            ->assertSee('00:30 WIB', false)
            ->assertDontSee('17:30', false);
    }

    public function test_halaman_daftar_pertemuan_menampilkan_tanggal_wib(): void
    {
        $this->actingAs($this->guru, 'guru')
            ->withSession([
                'jadwal_ids' => [$this->jadwal->id],
                'kelas_id' => $this->jadwal->kelas_id,
            ])
            ->get(route('guru.pertemuan.detail'))
            ->assertOk()
            ->assertSee('15 Januari 2026', false)
            ->assertDontSee('2026-01-15 00:00:00', false);
    }

    public function test_dashboard_guru_menampilkan_jam_mulai_dan_selesai_wib(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 15, 0, 30, 0, 'Asia/Jakarta'));

        $this->actingAs($this->guru, 'guru')
            ->get(route('guru.index'))
            ->assertOk()
            ->assertSee('Jam Mulai (WIB)', false)
            ->assertSee('Jam Selesai (WIB)', false)
            ->assertSee('07:00', false)
            ->assertSee('08:00', false);
    }

    public function test_jadwal_siswa_menampilkan_jam_wib(): void
    {
        $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.jadwal.index'))
            ->assertOk()
            ->assertSee('07:00 - 08:00 WIB', false);
    }
}
