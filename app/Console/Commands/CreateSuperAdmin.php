<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'uhtv:create-superadmin {--name=} {--email=}';
    protected $description = 'Crear la cuenta inicial de Superadministrador UHTV';

    public function handle(): int
    {
        if (User::where('role', 'superadmin')->exists()) {
            $this->error('Ya existe una cuenta Superadministrador.');
            return self::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Nombre del Superadministrador');
        $email = $this->option('email') ?: $this->ask('Correo del Superadministrador');
        $password = $this->secret('Contraseña segura');
        $confirmation = $this->secret('Repite la contraseña');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'confirmation' => $confirmation],
            ['name' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users,email', 'password' => 'required|string|min:12', 'confirmation' => 'same:password']
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) $this->error($error);
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => mb_strtolower($email),
            'password' => Hash::make($password),
            'role' => 'superadmin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->info('Superadministrador creado. Guarda las credenciales en un lugar seguro.');
        return self::SUCCESS;
    }
}
