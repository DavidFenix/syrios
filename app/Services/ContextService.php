<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\Role;

class ContextService
{
    public static function decideAfterLogin($user)
    {
        $roles = $user->roles()->withPivot('school_id')->get();

        if ($roles->isEmpty()) {
            return redirect()->route('no.access');
        }

        // agrupa roles por escola
        $porEscola = $roles->groupBy('pivot.school_id');

        if ($porEscola->count() === 1) {
            $schoolId = $porEscola->keys()->first();
            $rolesDaEscola = $porEscola[$schoolId];

            if ($rolesDaEscola->count() === 1) {
                return self::setContextAndRedirect($schoolId, $rolesDaEscola->first()->id);
            }

            return redirect()->route('choose.role', ['school' => $schoolId]);
        }

        // mais de uma escola
        return redirect()->route('choose.school');
    }

    public static function setContext($schoolId, $roleId)
    {
        $role = Role::findOrFail($roleId);

        Session::put('current_school_id', $schoolId);
        Session::put('current_role_id', $roleId);
        Session::put('current_role', $role->role_name);
    }

    public static function clearContext()
    {
        Session::forget(['current_school_id', 'current_role_id', 'current_role']);
    }

    public static function setContextAndRedirect($schoolId, $roleId)
    {
        self::setContext($schoolId, $roleId);
        return redirect(dashboard_route());
    }
}
