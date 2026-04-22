<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\User;
use App\Models\Passport;
use App\Models\MedicalCertificate;
use App\Models\Reservation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ManageUsers extends Component
{
    public $roleFilter;

    #[Url(as: 'manager')]
    public $filterManagerId = null;
    public $filterManagerName = null;

    // Generales
    public $name, $email, $phone, $birth_date;
    public $assigned_manager_id = null;

    // Pasaporte (Only for clients)
    public $has_passport = false;
    public $passport_number, $passport_expiration;
    public $passport_id;

    // Certificado (Only for clients)
    public $has_certificate = false;
    public $medical_issue_date;
    public $certificate_id;

    // Control Clientes de Gestor (Only Gestores)
    public $clientSearch = '';
    public $clientSearchResults = [];
    public $assignedClients = [];
    public $pendingClientOverride = null;
    public $showOverrideModal = false;

    // Borrado Diferenciado
    public $showMigrationModal = false;
    public $migrationTargetGestorId = null;
    public $deleteImpactInfo = [];
    public $showPasswordModal = false;
    public $tempPassword = '';

    // Control CRUD
    public $isEditing = false;
    public $userId = null;
    public $search = '';
    public $sortDir = 'asc';
    
    // Tab State Modal
    public $activeTab = 'general';

    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    public function mount($role = 'cliente')
    {
        $this->roleFilter = $role;
        abort_unless(in_array($this->roleFilter, ['cliente', 'gestor']), 404);
        $this->resetInputFields();
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->ignore($this->userId)
            ],
            // Strict regex for valid phone numbers
            'phone' => ['nullable', 'string', 'min:9', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            // Strict validation for birth date (min 18 years old for safety)
            'birth_date' => 'nullable|date|before_or_equal:-18 years|after:1900-01-01',
        ];

        if ($this->roleFilter === 'cliente') {
            $rules['assigned_manager_id'] = 'nullable|exists:users,id';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'birth_date.before_or_equal' => 'El titular debe ser mayor de 18 años por motivos de seguridad.',
        ];
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->birth_date = '';
        $this->assigned_manager_id = null;

        $this->has_passport = false;
        $this->passport_number = '';
        $this->passport_expiration = '';
        $this->passport_id = null;

        $this->has_certificate = false;
        $this->medical_issue_date = '';
        $this->certificate_id = null;

        $this->clientSearch = '';
        $this->clientSearchResults = [];
        $this->assignedClients = [];
        $this->pendingClientOverride = null;
        $this->showOverrideModal = false;

        $this->isEditing = false;
        $this->userId = null;
        $this->activeTab = 'general';
        
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->showMigrationModal = false;
        $this->migrationTargetGestorId = null;
        $this->deleteImpactInfo = [];

        // Note: we don't reset showPasswordModal here so it persists after creation
        // but we ensure it's false when starting other flows.
    }

    public function clearPasswordModal()
    {
        $this->showPasswordModal = false;
        $this->tempPassword = '';
    }

    public function updatedClientSearch()
    {
        if (strlen($this->clientSearch) > 1) {
            $this->clientSearchResults = User::query()
                ->where('role', 'cliente')
                ->where(function ($q) {
                    $q->where('email', 'like', '%' . $this->clientSearch . '%')
                      ->orWhere('id', 'like', $this->clientSearch . '%');
                })
                ->with('manager')
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->clientSearchResults = [];
        }
    }

    public function requestAddClient($clientId)
    {
        if (collect($this->assignedClients)->contains('id', $clientId)) {
            $this->clientSearch = '';
            $this->clientSearchResults = [];
            return;
        }
        
        $client = User::with('manager')->find($clientId);
        if ($client) {
            if ($client->manager && $client->manager->id !== $this->userId) {
                $this->pendingClientOverride = $client;
                $this->showOverrideModal = true;
            } else {
                $this->confirmAddClient($client);
            }
        }
    }

    public function confirmOverrideClient()
    {
        if ($this->pendingClientOverride) {
            $this->confirmAddClient($this->pendingClientOverride);
            $this->showOverrideModal = false;
            $this->pendingClientOverride = null;
        }
    }

    public function cancelOverrideClient()
    {
        $this->showOverrideModal = false;
        $this->pendingClientOverride = null;
    }

    public function confirmAddClient($client)
    {
        $mngrName = 'N/A';
        if (is_array($client)) {
            $clientModel = User::with('manager')->find($client['id']);
            $mngrName = $clientModel->manager ? $clientModel->manager->name : 'N/A';
            $this->assignedClients[] = [
                'id' => $client['id'],
                'name' => $client['name'],
                'email' => $client['email'],
                'old_manager' => $mngrName
            ];
        } else {
            $this->assignedClients[] = [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'old_manager' => $client->manager ? $client->manager->name : 'N/A'
            ];
        }

        $this->clientSearch = '';
        $this->clientSearchResults = [];
    }

    public function removeClient($clientId)
    {
        $this->assignedClients = collect($this->assignedClients)->filter(fn($c) => $c['id'] !== $clientId)->values()->toArray();
    }

    public function render()
    {
        $query = User::query()
            ->where('role', $this->roleFilter)
            ->with(['passengers', 'manager', 'clients']);

        if ($this->filterManagerId && $this->roleFilter === 'cliente') {
            $query->where('assigned_manager_id', $this->filterManagerId);
            $managerUser = User::find($this->filterManagerId);
            if ($managerUser) {
                $this->filterManagerName = $managerUser->name;
            }
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('name', $this->sortDir)->get();
        // Load available managers so we can assign clients (Only useful when editing a client)
        $managers = [];
        $availableGestors = [];
        if ($this->roleFilter === 'cliente') {
            $managers = User::where('role', 'gestor')->orderBy('name')->get();
        }
        if ($this->roleFilter === 'gestor') {
            $availableGestors = User::where('role', 'gestor')->orderBy('name')->get();
        }

        return view('livewire.admin.manage-users', [
            'users' => $users,
            'managers' => $managers,
            'availableGestors' => $availableGestors,
        ])->layout('layouts.app');
    }

    public function toggleSort()
    {
        $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
    }

    public function setCreateMode()
    {
        $this->resetInputFields();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $this->isEditing = true;
        
        $user = User::with(['clients'])->find($id);

        if ($user) {
            $this->userId = $id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->birth_date = $user->birth_date ? $user->birth_date->format('Y-m-d') : null;
            
            if ($this->roleFilter === 'cliente') {
                $this->assigned_manager_id = $user->assigned_manager_id;
            } else if ($this->roleFilter === 'gestor') {
                $this->assignedClients = $user->clients->map(function($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'email' => $c->email,
                        'old_manager' => 'Este Gestor'
                    ];
                })->toArray();
            }
        }
    }

    public function updatedName($value)
    {
        if ($this->roleFilter === 'gestor' && !$this->isEditing) {
            $this->generateUniqueEmail($value);
        }
    }

    private function generateUniqueEmail($name)
    {
        if (empty(trim($name))) {
            $this->email = '';
            return;
        }

        // Extraer primera palabra (nombre), minúsculas y quitar acentos (usando Str::slug)
        $baseStr = \Illuminate\Support\Str::slug(explode(' ', trim($name))[0], '');
        if (empty($baseStr)) {
            $baseStr = 'user';
        }

        // Buscar un correo único
        $email = $baseStr . '@iris.com';
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $baseStr . $counter . '@iris.com';
            $counter++;
        }

        $this->email = $email;
    }

    public function confirmSave()
    {
        $this->validate();
        $this->showSaveModal = true;
    }

    public function executeSave()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date ?: null,
        ];

        // El email es inmutable para los gestores una vez creados
        if (!($this->isEditing && $this->roleFilter === 'gestor')) {
            $data['email'] = $this->email;
        }

        if (!$this->isEditing && $this->roleFilter === 'gestor') {
            if (empty($this->email)) {
                $this->generateUniqueEmail($this->name);
                $this->validateOnly('email');
            }
            $data['email'] = $this->email;
        }

        if ($this->roleFilter === 'cliente') {
            $data['assigned_manager_id'] = $this->assigned_manager_id ?: null;
        }

        if ($this->isEditing && $this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $user->update($data);

                if ($this->roleFilter === 'gestor') {
                    $this->syncClients($user);
                }
                
                $t = ucfirst($this->roleFilter);
                session()->flash('message', "{$t} actualizado correctamente.");
            }
        } else {
            $rawPassword = \Illuminate\Support\Str::random(12);
            $this->tempPassword = $rawPassword;
            
            $data['password'] = \Illuminate\Support\Facades\Hash::make($rawPassword);
            $data['role'] = $this->roleFilter;
            $user = User::create($data);

            if ($this->roleFilter === 'gestor') {
                $this->syncClients($user);
            }

            // Send Welcome Email
            try {
                Mail::to($user->email)->send(new \App\Mail\WelcomeUserMail($user, $rawPassword));
            } catch (\Exception $e) {
                Log::error("Error enviando email de bienvenida: " . $e->getMessage());
                session()->flash('error', 'El usuario fue creado pero no se pudo enviar el correo de bienvenida. Revisa la configuración de SMTP.');
            }

            $this->showPasswordModal = true;
            $t = ucfirst($this->roleFilter);
            session()->flash('message', "{$t} registrado con éxito. Contraseña generada y enviada por email.");
        }

        $this->resetInputFields();
    }

    private function syncClients(User $gestor)
    {
        // 1. Unassign removed clients
        $keptIds = collect($this->assignedClients)->pluck('id')->toArray();
        if ($this->isEditing) {
            // Find existing clients bound to gestor that are NOT in keptIds anymore
            User::where('assigned_manager_id', $gestor->id)
                ->whereNotIn('id', $keptIds)
                ->update(['assigned_manager_id' => null]);
        }
        
        // 2. Assign selected clients
        if (!empty($keptIds)) {
            User::whereIn('id', $keptIds)->update(['assigned_manager_id' => $gestor->id]);
        }
    }

    public function resetPassword()
    {
        if (!$this->isEditing || !$this->userId) return;

        $user = User::find($this->userId);
        if ($user) {
            $rawPassword = \Illuminate\Support\Str::random(12);
            $this->tempPassword = $rawPassword;
            $user->password = \Illuminate\Support\Facades\Hash::make($rawPassword);
            $user->save();

            $this->showPasswordModal = true;
            session()->flash('message', "Clave de acceso de emergencia regenerada con éxito.");
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->deleteImpactInfo = [];

        $user = User::withCount(['passengers', 'reservations', 'clients'])->find($id);
        if (!$user) return;

        if ($this->roleFilter === 'gestor' && $user->clients_count > 0) {
            // Gestor con clientes → forzar migración de cartera
            $this->deleteImpactInfo = [
                'type' => 'gestor_migration',
                'name' => $user->name,
                'client_count' => $user->clients_count,
            ];
            $this->showMigrationModal = true;
            return;
        }

        if ($this->roleFilter === 'cliente') {
            $hasPaidReservations = Reservation::where('user_id', $id)
                ->where('payment_status', 'paid')
                ->exists();

            $this->deleteImpactInfo = [
                'type' => $hasPaidReservations ? 'client_soft' : 'client_hard',
                'name' => $user->name,
                'passenger_count' => $user->passengers_count,
                'reservation_count' => $user->reservations_count,
                'has_paid' => $hasPaidReservations,
            ];
        }

        $this->showDeleteModal = true;
    }

    public function executeMigrationAndDelete()
    {
        if (!$this->deleteId || !$this->migrationTargetGestorId) return;

        $gestor = User::find($this->deleteId);
        $targetGestor = User::find($this->migrationTargetGestorId);

        if (!$gestor || !$targetGestor || $targetGestor->role !== 'gestor') {
            session()->flash('message', 'Error: Gestor destino no válido.');
            return;
        }

        // Migrar todos los clientes al nuevo gestor
        User::where('assigned_manager_id', $gestor->id)
            ->update(['assigned_manager_id' => $targetGestor->id]);

        $gestor->delete();

        session()->flash('message', "Gestor eliminado. Todos los clientes han sido migrados a {$targetGestor->name}.");
        $this->resetInputFields();
    }

    public function executeDelete()
    {
        if (!$this->deleteId) return;

        $user = User::find($this->deleteId);
        if (!$user || $user->role === 'super_admin') {
            $this->resetInputFields();
            return;
        }

        if ($this->roleFilter === 'cliente') {
            $hasPaidReservations = Reservation::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->exists();

            if ($hasPaidReservations) {
                // Soft Delete: cancelar reservas activas, mantener registro
                Reservation::where('user_id', $user->id)
                    ->where('status', '!=', 'Cancelada')
                    ->update(['status' => 'Cancelada', 'seat_number' => null]);

                $user->delete(); // SoftDeletes
                session()->flash('message', 'Cliente desactivado (Soft Delete). Reservas canceladas. Registro fiscal preservado.');
            } else {
                // Hard Delete: sin historial financiero
                Reservation::where('user_id', $user->id)->forceDelete();
                $user->passengers()->forceDelete();
                $user->forceDelete();
                session()->flash('message', 'Cliente eliminado permanentemente.');
            }
        } else {
            $user->delete();
            session()->flash('message', 'Eliminado de forma permanente.');
        }

        $this->resetInputFields();
    }
}
