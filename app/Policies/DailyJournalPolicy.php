<?php

namespace App\Policies;

use App\Models\DailyJournal;
use App\Models\User;

class DailyJournalPolicy
{
    public function viewAny(User $user): bool
    {
        return true; 
    }

    public function view(User $user, DailyJournal $dailyJournal): bool
    {
        if ($user->role === 'super_admin' || $user->role === 'admin_sekolah') {
            return true;
        }
        return $user->id === $dailyJournal->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, DailyJournal $dailyJournal): bool
    {
        if ($user->role === 'super_admin' || $user->role === 'admin_sekolah') {
            return true;
        }
        return $user->id === $dailyJournal->user_id;
    }

    public function delete(User $user, DailyJournal $dailyJournal): bool
    {
        if ($user->role === 'super_admin' || $user->role === 'admin_sekolah') {
            return true;
        }
        return $user->id === $dailyJournal->user_id;
    }
}