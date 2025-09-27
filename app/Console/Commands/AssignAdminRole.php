<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class AssignAdminRole extends Command
{
    protected $signature = 'users:assign-admin {email}';
    protected $description = 'Asigna el rol de administrador a un usuario específico';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("No se encontró ningún usuario con el email: {$email}");
            return 1;
        }

        $adminRole = Role::where('slug', 'admin')->first();
        
        if (!$adminRole) {
            $this->error('El rol de administrador no existe en la base de datos');
            return 1;
        }

        $user->assignRole($adminRole);
        
        $this->info("Se ha asignado el rol de administrador al usuario: {$email}");
        return 0;
    }
}