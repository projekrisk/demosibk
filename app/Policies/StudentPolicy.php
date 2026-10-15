<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Student $student): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin_sekolah']);
    }

 
    public function update(User $user, Student $student): bool
    {
        return in_array($user->role, ['super_admin', 'admin_sekolah']);
    }

    public function delete(User $user, Student $student): bool
    {
        return in_array($user->role, ['super_admin', 'admin_sekolah']);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin_sekolah']);
    }
}