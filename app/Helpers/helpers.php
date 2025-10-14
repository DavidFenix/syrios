<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('prefix')) {
    function prefix(string $basename = ''): string
    {
        $prefix = config('prefix.tabelas', 'syrios_');
        return $prefix . $basename;
    }
}

if (!function_exists('dashboard_route')) {
    function dashboard_route()
    {
        $user = auth()->user();

        if (!$user) {
            return route('login');
        }

        if (!session('current_role')) {
            return route('login');
        }

        $role = session('current_role');
        $schoolId = session('current_school_id');

        if ($role && $schoolId) {
            switch ($role) {
                case 'master':
                    return route('master.dashboard');
                case 'secretaria':
                    return route('secretaria.dashboard');
                case 'escola':
                    return route('escola.dashboard');
                case 'professor':
                    return route('professor.dashboard');
                default:
                    return '/';
            }
        }

        if ($user->hasRole('master')) {
            return route('master.dashboard');
        }

        if ($user->hasRole('secretaria')) {
            return route('secretaria.dashboard');
        }

        if ($user->hasRole('escola')) {
            return route('escola.dashboard');
        }

        if ($user->hasRole('professor')) {
            return route('professor.dashboard');
        }

        return route('choose.school');
    }
}
