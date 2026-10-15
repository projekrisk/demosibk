<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class StudentCurhatController extends Controller
{
    public function getCounselors(Request $request)
    {
        $student = $request->user();

        $counselors = User::where('role', 'konselor')
            ->whereHas('tenants', function ($query) use ($student) {
                $query->where('tenants.id', $student->tenant_id);
            })
            ->whereHas('classrooms', function (Builder $query) use ($student) {
                $query->where('classrooms.id', $student->classroom_id);
            })
            ->select('id', 'name', 'email')
            ->get();
        
        if ($counselors->isEmpty()) {
             $counselors = User::where('role', 'konselor')
                 ->whereHas('tenants', function($q) use ($student){
                     $q->where('tenants.id', $student->tenant_id);
                 })
                 ->select('id', 'name', 'email')
                 ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $counselors
        ]);
    }

    public function index(Request $request)
    {
        $messages = StudentMessage::where('student_id', $request->user()->id)
            ->with('targetCounselor')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'subject' => $msg->subject,
                    'preview' => substr($msg->message, 0, 50) . '...',
                    'message' => $msg->message,
                    'is_anonymous' => (bool) $msg->is_anonymous,
                    
                    'status' => $msg->replied_at ? 'Dibalas' : 'Menunggu',
                    'response' => $msg->response,
                    'replied_at' => $msg->replied_at ? $msg->replied_at->format('d M Y, H:i') : null,
                    'replied_by' => $msg->replier ? $msg->replier->name : null,
                    
                    'target_counselor' => $msg->targetCounselor ? $msg->targetCounselor->name : 'Umum',
                    'created_at' => $msg->created_at->format('d M Y, H:i'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'is_anonymous' => 'boolean',
            'counselor_id' => 'nullable|exists:users,id'
        ]);

        $user = $request->user();

        $curhat = StudentMessage::create([
            'tenant_id' => $user->tenant_id,
            'student_id' => $user->id,
            'counselor_id' => $request->counselor_id,
            'is_anonymous' => $request->is_anonymous ?? false,
            'sender_name' => $request->is_anonymous ? 'Siswa (Anonim)' : $user->name,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan berhasil dikirim. Guru BK akan segera merespons.',
            'data' => $curhat,
        ]);
    }
}