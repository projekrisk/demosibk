<?php

namespace App\Filament\App\Resources\StudentReportResource\Pages;

use App\Filament\App\Resources\StudentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use App\Models\StudentReport;
use App\Models\StudentAssessment;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ListStudentReports extends ListRecords
{
    protected static string $resource = StudentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_mass_reports_auto')
                ->label('Generate')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Generate Rapor')
                ->modalDescription('Sistem akan generate deskripsi rapor untuk seluruh siswa.')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('academic_year')
                                ->label('Tahun Ajaran')
                                ->default(fn() => Filament::getTenant()->current_academic_year ?? now()->year . '/' . (now()->year + 1))
                                ->required(),
                            Forms\Components\Select::make('semester')
                                ->options(['Ganjil' => 'Ganjil', 'Genap' => 'Genap'])
                                ->default(Filament::getTenant()->current_semester ?? 'Ganjil')
                                ->required(),
                            
                            Forms\Components\Toggle::make('overwrite')
                                ->label('Regenerate Ulang (Timpa Rapor Lama)')
                                ->helperText('Aktifkan jika ada nilai asesmen yang berubah.')
                                ->columnSpanFull(),
                        ]),
                ])
                ->action(function (array $data) {
                    set_time_limit(0);

                    $user = Auth::user();
                    
                    $existingReportStudentIds = StudentReport::where('academic_year', $data['academic_year'])
                        ->where('semester', $data['semester'])
                        ->pluck('student_id')
                        ->toArray();

                    $query = StudentReportResource::getEloquentQuery();

                    if (empty($data['overwrite'])) {
                        // Hanya proses yang belum punya rapor
                        $query->whereNotIn('id', $existingReportStudentIds);
                    }

                    // Ambil semua siswa sekaligus beserta relasi kelas & konselornya
                    $students = $query->with(['classroom.counselors'])->get();

                    if ($students->isEmpty()) {
                        Notification::make()
                            ->title('Semua Tuntas!')
                            ->body('Tidak ada siswa yang perlu diproses. Rapor seluruh siswa sudah lengkap/terbaru.')
                            ->success()
                            ->send();
                        return;
                    }

                    $getNarration = function ($score, $domain) {
                        $banks = [
                            'pribadi' => [
                                'sangat_baik' => ['Siswa menunjukkan kematangan emosi yang sangat baik.', 'Memiliki tingkat kepercayaan diri yang tinggi dan pemahaman diri yang sangat positif.', 'Kondisi psikologis siswa terpantau prima dan adaptif dalam menghadapi keseharian.'],
                                'baik' => ['Kondisi emosional siswa tergolong stabil secara umum.', 'Siswa cukup mampu mengelola perasaannya sehari-hari dengan baik.', 'Memiliki konsep diri yang wajar dan mampu mengatasi masalah ringan.'],
                                'sedang' => ['Siswa terkadang mengalami keraguan pada dirinya sendiri namun masih bisa diatasi.', 'Terdapat fluktuasi emosi pada momen tertentu yang butuh sedikit pendampingan.', 'Siswa sedang dalam tahap penyesuaian emosi menuju kemandirian yang lebih baik.'],
                                'kurang' => ['Siswa cukup sering menunjukkan kecemasan atau bersikap kurang percaya diri.', 'Diperlukan dukungan penuh untuk mengelola emosi agar lebih stabil.', 'Cenderung merasa terbebani oleh pikiran yang menghambat aktivitasnya.'],
                                'sangat_kurang' => ['Siswa sangat membutuhkan dukungan psikologis intensif terkait masalah pribadi.', 'Kondisi emosional siswa saat ini sangat memerlukan perhatian khusus dan kehangatan di rumah.', 'Beban emosional siswa tergolong tinggi dan butuh intervensi bimbingan secara rutin.']
                            ],
                            'sosial' => [
                                'sangat_baik' => ['Sangat mudah bergaul dan memiliki empati yang luar biasa terhadap sesama.', 'Interaksi sosialnya sangat baik dan sering menjadi penggerak hal positif di kelompoknya.', 'Memiliki kecerdasan sosial yang tinggi dan disukai teman-temannya.'],
                                'baik' => ['Mampu berinteraksi dengan teman sebaya maupun guru dengan sangat baik.', 'Tidak memiliki kendala berarti dalam bersosialisasi di lingkungan sekolah.', 'Dapat memosisikan diri dan bersikap sopan dalam pergaulan.'],
                                'sedang' => ['Cenderung memilih-milih teman bermain, namun masih bisa diajak bekerja sama secara kelompok.', 'Interaksi sosialnya cukup wajar meski terkadang tampak lebih suka menyendiri.', 'Kadang membutuhkan waktu lebih lama untuk membaur di lingkungan baru.'],
                                'kurang' => ['Tampak kesulitan beradaptasi atau sering menarik diri dari keramaian teman.', 'Kurang nyaman bersosialisasi dan kadang merasa terkucil dari kelompok.', 'Terkadang terjadi miskomunikasi atau gesekan ringan dengan teman sebayanya.'],
                                'sangat_kurang' => ['Sangat menutup diri dan terindikasi terisolasi dari pergaulan sekolah.', 'Mengalami kendala sosialisasi yang berat dan membutuhkan penanganan intensif.', 'Sangat memerlukan dukungan agar berani kembali berinteraksi tanpa hambatan emosional.']
                            ],
                            'belajar' => [
                                'sangat_baik' => ['Memiliki kemandirian dan antusiasme belajar yang sangat tinggi.', 'Disiplin belajarnya luar biasa dan terarah dalam menyelesaikan tugas-tugas sekolah.', 'Motivasi akademik sangat memuaskan, sering menjadi contoh bagi rekan-rekannya.'],
                                'baik' => ['Motivasi belajarnya stabil dan mampu memenuhi tanggung jawab akademik dengan baik.', 'Memiliki rutinitas belajar yang teratur dan kooperatif di kelas.', 'Mampu menyerap instruksi pelajaran dan membagi waktunya dengan bijak.'],
                                'sedang' => ['Terkadang mengalami penurunan fokus, namun masih mampu menyelesaikan tugas sekolahnya.', 'Motivasi belajarnya perlu sedikit didorong agar lebih persisten.', 'Kapasitas belajarnya cukup baik meski manajemen waktunya sesekali kurang optimal.'],
                                'kurang' => ['Sering kehilangan fokus belajar dan cenderung menunda-nunda tugas.', 'Mengalami hambatan dalam memanajemen waktu akademik yang berdampak pada hasil belajarnya.', 'Memerlukan bimbingan untuk menemukan pola belajar yang lebih efektif baginya.'],
                                'sangat_kurang' => ['Menunjukkan kesulitan besar dalam mengikuti ritme pelajaran dan rentan kehilangan motivasi.', 'Membutuhkan intervensi akademik mendesak karena atensi belajar yang minim.', 'Kerap mengabaikan tanggung jawab belajar secara signifikan sehingga butuh pemantauan ekstra.']
                            ],
                            'karir' => [
                                'sangat_baik' => ['Telah memiliki orientasi studi lanjut atau proyeksi masa depan yang sangat matang.', 'Pemahaman terhadap minat serta bakat dirinya sangat luar biasa dan terarah.', 'Sangat proaktif menggali informasi terkait persiapan dan jalur cita-citanya.'],
                                'baik' => ['Sudah memiliki gambaran umum mengenai opsi studi kelanjutan atau karirnya kelak.', 'Mampu mengaitkan kesukaan belajarnya dengan potensi jurusan di masa depan.', 'Eksplorasi wawasan karirnya tergolong realistis dan baik.'],
                                'sedang' => ['Masih dalam tahap menimbang beberapa alternatif untuk masa depan.', 'Memiliki minat yang beragam namun belum mengerucut pada satu prioritas utama.', 'Butuh diskusi berkelanjutan untuk semakin memantapkan pilihan kelanjutan studinya.'],
                                'kurang' => ['Terlihat masih sangat kebingungan dalam menentukan arah spesifik masa depannya.', 'Belum sepenuhnya menyadari potensi dan kekuatan utama yang dimilikinya.', 'Membutuhkan tes peminatan atau konseling karir intensif untuk memetakan arah tujuannya.'],
                                'sangat_kurang' => ['Sama sekali belum memiliki wawasan arah hidup dan cenderung belum peduli pada masa depan.', 'Sangat membutuhkan bimbingan karir terstruktur dari dasar untuk membangkitkan kesadarannya.', 'Tidak memiliki proyeksi karir, memerlukan dukungan menyeluruh terkait orientasi masa depannya.']
                            ],
                        ];

                        $level = '';
                        if ($score <= 20) $level = 'sangat_baik';
                        elseif ($score <= 40) $level = 'baik';
                        elseif ($score <= 60) $level = 'sedang';
                        elseif ($score <= 80) $level = 'kurang';
                        else $level = 'sangat_kurang';

                        $sentences = $banks[$domain][$level];
                        return $sentences[array_rand($sentences)];
                    };

                    $count = 0;

                    foreach ($students as $student) {
                        $assessment = StudentAssessment::where('student_id', $student->id)
                            ->where('status', 'completed')
                            ->whereHas('package', function ($q) {
                                $q->where('usage_type', 'evaluation');
                            })
                            ->latest('filled_at')
                            ->first()
                            ?? StudentAssessment::where('student_id', $student->id)->where('status', 'completed')->latest('filled_at')->first();

                        $achievements = $student->records()->where('type', 'achievement')->count();
                        $violations = $student->records()->where('type', 'violation')->count();
                        $counselings = $student->counselingServices()->count();

                        $pribadi = $assessment ? $assessment->percentage_pribadi : 0;
                        $sosial = $assessment ? $assessment->percentage_sosial : 0;
                        $belajar = $assessment ? $assessment->percentage_belajar : 0;
                        $karir = $assessment ? $assessment->percentage_karir : 0;

                        $pribadiText = $getNarration($pribadi, 'pribadi');
                        $sosialText = $getNarration($sosial, 'sosial');
                        $belajarText = $getNarration($belajar, 'belajar');
                        $karirText = $getNarration($karir, 'karir');
                        
                        $kesimpulan = "";
                        if ($violations > 0 && $achievements > 0) {
                            $kesimpulan .= "Di samping prestasi yang telah dicapai, komitmen pada tata tertib sekolah perlu lebih ditingkatkan. ";
                        } elseif ($violations > 0) {
                            $kesimpulan .= "Siswa diimbau untuk lebih memperhatikan tata tertib dan mengendalikan pelanggaran kedisiplinan. ";
                        } elseif ($achievements > 0) {
                            $kesimpulan .= "Prestasi luar biasa yang diraih patut diapresiasi dan terus difasilitasi potensinya. ";
                        } else {
                            $kesimpulan .= "Secara administratif, catatan perilaku siswa sejauh ini terpantau mematuhi kelaziman aturan sekolah. ";
                        }

                        $scoresArr = [
                            'pribadi' => $pribadi, 'sosial' => $sosial, 'belajar' => $belajar, 'karir' => $karir
                        ];
                        $maxScore = max($scoresArr);
                        $priorityDomain = array_search($maxScore, $scoresArr);

                        if ($maxScore > 60) {
                            $kesimpulan .= "Saat ini, titik fokus pendampingan difokuskan pada pemulihan/penguatan aspek " . ucfirst($priorityDomain) . ". Kami sangat mengharapkan sinergi yang erat dari pihak keluarga/orang tua untuk memberikan kehangatan dan dukungan moril Ananda di rumah.";
                        } elseif ($maxScore <= 20) {
                            $kesimpulan .= "Secara keseluruhan, perkembangannya berjalan sangat memuaskan dan positif. Terus pertahankan komunikasi suportif antara sekolah dan keluarga demi keberhasilan masa depan Ananda.";
                        } else {
                            $kesimpulan .= "Perkembangan kapasitas dirinya tergolong normal dan aman sesuai fasenya. Saran kami, berikanlah ruang berekspresi dan apresiasi ringan secara berkala di rumah agar potensi adaptasinya semakin maksimal.";
                        }

                        // 4. Tentukan Konselor (Jika Admin yang klik, cari guru BK kelasnya)
                        $counselorId = $user->id;
                        if ($user->role !== 'konselor') {
                            $assignedCounselor = $student->classroom?->counselors()->first();
                            if ($assignedCounselor) {
                                $counselorId = $assignedCounselor->id;
                            }
                        }

                        // 5. Simpan ke Database
                        StudentReport::updateOrCreate(
                            [
                                'tenant_id' => $student->tenant_id,
                                'student_id' => $student->id,
                                'academic_year' => $data['academic_year'],
                                'semester' => $data['semester'],
                            ],
                            [
                                'counselor_id' => $counselorId,
                                'personality_development' => $pribadiText,
                                'social_development' => $sosialText,
                                'academic_development' => trim($belajarText . ' ' . $karirText),
                                'general_conclusion' => $kesimpulan,
                                'total_achievements' => $achievements,
                                'total_violations' => $violations,
                                'total_counseling' => $counselings,
                                'assessment_pribadi' => $pribadi,
                                'assessment_sosial' => $sosial,
                                'assessment_belajar' => $belajar,
                                'assessment_karir' => $karir,
                            ]
                        );
                        
                        $count++;
                    }

                    Notification::make()
                        ->title('Proses Selesai')
                        ->body("Berhasil generate rapor otomatis untuk {$count} siswa.")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('print_all')
                ->label('Cetak (.pdf)')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn () => Filament::getTenant()->canPrintReport())
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('academic_year')
                                ->label('Tahun Ajaran')
                                ->default(fn() => Filament::getTenant()->current_academic_year ?? now()->year . '/' . (now()->year + 1))
                                ->required(),
                            Forms\Components\Select::make('semester')
                                ->options(['Ganjil' => 'Ganjil', 'Genap' => 'Genap'])
                                ->default(Filament::getTenant()->current_semester ?? 'Ganjil')
                                ->required(),
                            Forms\Components\TextInput::make('signing_place')
                                ->label('Tempat (Kota)')
                                ->default(function() {
                                    $address = Filament::getTenant()->address;
                                    if($address) {
                                        $parts = explode(',', $address);
                                        $city = trim(end($parts));
                                        return preg_replace('/[0-9]+/', '', $city);
                                    }
                                    return '';
                                }),
                            Forms\Components\DatePicker::make('signing_date')
                                ->label('Tanggal Tanda Tangan')
                                ->default(now())
                                ->required(),
                        ])
                ])
                ->action(function (array $data) {
                    set_time_limit(0);

                    $studentIds = StudentReportResource::getEloquentQuery()->pluck('id')->toArray();

                    $reports = StudentReport::whereIn('student_id', $studentIds)
                        ->where('academic_year', $data['academic_year'])
                        ->where('semester', $data['semester'])
                        ->with(['student', 'counselor'])
                        ->get();

                    if ($reports->isEmpty()) {
                        Notification::make()->title('Data tidak ditemukan')->warning()->send();
                        return;
                    }

                    $tenant = Filament::getTenant();
                    
                    $pdf = Pdf::loadView('pdf.rapor-bk-bulk', [
                        'reports' => $reports,
                        'tenant' => $tenant,
                        'signing_place' => $data['signing_place'],
                        'signing_date' => $data['signing_date'],
                    ])
                    ->setPaper([0, 0, 609.45, 935.43], 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Rapor-Lengkap-' . $data['semester'] . '.pdf');
                }),
        ];
    }
}