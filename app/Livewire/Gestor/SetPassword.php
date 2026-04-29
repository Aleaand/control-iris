<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class SetPassword extends Component
{
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $done = false;

    protected function rules(): array
    {
        return [
            'new_password'              => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'new_password.required'  => 'Debes introducir una contraseña.',
            'new_password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $userId = auth()->id();
        
        if ($userId) {
            // Usamos DB::table para forzar la actualización directa en la base de datos
            // y evitar cualquier problema de caché de instancia o de Eloquent.
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $userId)
                ->update([
                    'password' => \Illuminate\Support\Facades\Hash::make($this->new_password),
                    'must_change_password' => null,
                    'updated_at' => now(),
                ]);

            $this->done = true;
            session()->flash('message', '¡Contraseña establecida correctamente! Bienvenido/a al panel.');
            
            // Redirigir al dashboard
            $this->redirect(route('gestor.dashboard'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.gestor.set-password')
            ->layout('layouts.gestor-auth');
    }
}
