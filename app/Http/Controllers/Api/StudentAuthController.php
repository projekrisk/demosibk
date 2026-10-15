<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'npsn' => 'required',
            'nis' => 'required',
            'nisn' => 'required',
        ]);

        $tenant = Tenant::where('npsn', $request->npsn)->first();
        if (!$tenant) {
            return response()->json(['status' => 'error', 'message' => 'Sekolah tidak terdaftar.'], 404);
        }

        $student = Student::with(['classroom', 'tenant'])
            ->where('tenant_id', $tenant->id)
            ->where('nis', $request->nis)
            ->where('nisn', $request->nisn)
            ->first();

        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $studentData = [
            'id' => $student->id,
            'name' => $student->name,
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'class_name' => $student->class_name,
            'school_name' => $student->tenant->name,
            'avatar_url' => $student->avatar_url ? url('uploads/' . $student->avatar_url) : null,
        ];

        if (empty($student->password)) {
            return response()->json([
                'status' => 'inactive',
                'message' => 'Akun belum aktif. Silakan lakukan aktivasi.',
                'data' => $studentData
            ], 200);
        }

        if (!$request->filled('password')) {
            return response()->json([
                'status' => 'need_password',
                'message' => 'Silakan masukkan kata sandi.',
                'data' => $studentData
            ], 200);
        }

        if (!Hash::check($request->password, $student->password)) {
            return response()->json(['status' => 'error', 'message' => 'Kata sandi salah.'], 401);
        }

        if ($request->fcm_token) {
            $student->forceFill(['fcm_token' => $request->fcm_token])->save();
        }

        $token = $student->createToken('student_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'data' => $studentData
        ]);
    }

    public function activate(Request $request)
    {
        $request->validate([
            'npsn' => 'required',
            'nis' => 'required',
            'nisn' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $tenant = Tenant::where('npsn', $request->npsn)->first();
        if (!$tenant) return response()->json(['message' => 'Sekolah tidak ditemukan.'], 404);

        $student = Student::with(['tenant'])->where('tenant_id', $tenant->id)
            ->where('nis', $request->nis)
            ->where('nisn', $request->nisn)
            ->first();

        if (!$student) return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        
        if (!empty($student->password)) {
            return response()->json(['message' => 'Akun ini sudah aktif. Silakan login.'], 422);
        }

        $student->forceFill([
            'password' => $request->password 
        ])->save();
        
        $token = $student->createToken('student_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'data' => [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'class_name' => $student->class_name,
                'school_name' => $tenant->name,
                'avatar_url' => $student->avatar_url ? url('uploads/' . $student->avatar_url) : null
            ]
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:2048']);
        $user = $request->user();
        if ($user->avatar_url && Storage::disk('public_uploads')->exists($user->avatar_url)) {
            Storage::disk('public_uploads')->delete($user->avatar_url);
        }
        $path = $request->file('avatar')->store('student-avatars', 'public_uploads');
        $user->forceFill(['avatar_url' => $path])->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Foto profil diperbarui.',
            'avatar_full_url' => url('uploads/' . $path),
            'data' => $user
        ]);
    }

    public function records(Request $request)
    {
        $user = $request->user();
        $records = $user->records()
            ->orderBy('recorded_at', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'type' => $record->type, 
                    'title' => $record->title,
                    'description' => $record->description,
                    'recorded_at' => $record->recorded_at->format('d M Y'),
                ];
            });
        return response()->json(['status' => 'success', 'data' => $records]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out.']);
    }
}