<?php

namespace App\Policies;

use App\Models\CounselingService;
use App\Models\User;

class CounselingServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CounselingService $counselingService): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CounselingService $counselingService): bool
    {
        if ($user->role === 'super_admin' || $user->role === 'admin_sekolah') {
            return true;
        }
        return $user->id === $counselingService->counselor_id;
    }

    public function delete(User $user, CounselingService $counselingService): bool
    {
        if ($user->role === 'super_admin' || $user->role === 'admin_sekolah') {
            return true;
        }
        return $user->id === $counselingService->counselor_id;
    }
}