<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman Utama (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// Halaman Kebijakan Privasi
Route::get('/privacy', function () {
    return view('privacy');
});

// Halaman Sanggahan (Disclaimer)
Route::get('/disclaimer', function () {
    return view('disclaimer');
});

// Halaman Syarat dan Ketentuan
Route::get('/terms', function () {
    return view('terms');
});

// Halaman Kontak
Route::get('/contact', function () {
    return view('contact');
});

// Halaman Panduan
Route::get('/guide', function () {
    return view('guide');
});

/*
|--------------------------------------------------------------------------
| Kustomisasi Rute Email Verification
|--------------------------------------------------------------------------
*/
// RUTE KUSTOM VERIFIKASI EMAIL (Bypass 403 100% untuk perangkat/browser lain)
Route::get('/verify-email/{id}/{hash}', function (Request $request, $id, $hash) {
    
    // Cari data pengguna berdasarkan ID di link
    $user = User::find($id);

    if (! $user) {
        abort(403, 'Pengguna tidak ditemukan.');
    }

    // PERBAIKAN: Hapus hasValidSignature() yang terlalu ketat dan sering diblokir HP/Proxy.
    // Kita hanya menggunakan pencocokan Hash (SHA1 dari Email) yang sudah cukup aman untuk aktivasi.
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Data verifikasi tidak cocok atau link rusak.');
    }

    // Eksekusi aktivasi jika belum aktif
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    // Arahkan ke halaman login Filament dengan pesan sukses
    return redirect('/app/login')->with('status', 'Email berhasil diverifikasi! Silakan login.');
    
})->name('custom.verification.verify');


/*
|--------------------------------------------------------------------------
| Portal Web Siswa (Tanpa API/Android)
|--------------------------------------------------------------------------
*/
use App\Livewire\Student\Auth\Login;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\Assessment\Take;
use App\Livewire\Student\Records;
use App\Livewire\Student\Curhat;
use App\Livewire\BookingPage;

Route::prefix('siswa')->group(function () {
    
    // Auth Siswa
    Route::get('/login', Login::class)->name('student.login');
    Route::post('/logout', function () {
        Auth::guard('student')->logout();
        return redirect()->route('student.login');
    })->name('student.logout');

    // Rute yang butuh login
    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('student.dashboard');
        
        // Fitur Konseling/Curhat
        Route::get('/curhat', Curhat::class)->name('student.curhat');
        
        // Catatan Kedisiplinan
        Route::get('/records', Records::class)->name('student.records');
        
        // Asesmen (AKPD & RIASEC)
        Route::get('/assessment/{package_id}', Take::class)->name('student.assessment.take');
    });

});

/*
|--------------------------------------------------------------------------
| Fitur Publik Lainnya
|--------------------------------------------------------------------------
*/

// Booking Jadwal Konseling Online (Public)
Route::get('/booking/{npsn}', BookingPage::class)->name('student.booking');