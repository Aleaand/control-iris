<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasResponsivePagination;
use Livewire\Attributes\Url;
use App\Models\User;
use App\Models\Passport;
use App\Models\MedicalCertificate;
use App\Models\Reservation;
use App\Models\Task;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ManageUsers extends Component
{
    use WithPagination, HasResponsivePagination;

    public $roleFilter;

    #[Url(as: 'manager')]
    public $filterManagerId = null;
    public $filterManagerName = null;

    // Generales
    public $name, $primarylastname, $secondarylastname, $email, $phone, $birth_date;
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
            'primarylastname' => 'required|string|max:255',
            'secondarylastname' => 'nullable|string|max:255',
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
            $rules['assigned_manager_id'] = 'required|exists:users,id';
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
        $this->primarylastname = '';
        $this->secondarylastname = '';
        $this->email = '';
        $this->phone = '';
        $this->birth_date = '';
        if ($this->roleFilter === 'cliente') {
            // Default to the gestor with the fewest clients for balanced distribution
            $bestGestor = User::where('role', 'gestor')
                ->withCount('clients')
                ->orderBy('clients_count', 'asc')
                ->first();
            $this->assigned_manager_id = $bestGestor ? $bestGestor->id : null;
        } else {
            $this->assigned_manager_id = null;
        }

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
    }

    public function updatedClientSearch()
    {
        $query = User::query()->where('role', 'cliente');

        if (strlen($this->clientSearch) > 1) {
            $searchTerm = '%' . strtolower($this->clientSearch) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(email) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('id::text LIKE ?', [$searchTerm]);
            });
        } else {
            // Sugerencias: Clientes que no tienen gestor asignado
            $query->whereNull('assigned_manager_id');
        }

        $this->clientSearchResults = $query->with('manager')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function requestAddClient($clientId)
    {
        if (collect($this->assignedClients)->contains('id', $clientId)) {
            $this->clientSearch = '';
            $this->clientSearchResults = [];
            return;
        }
        
        $client = User::with('manager')->where('role', 'cliente')->find($clientId);
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
            $clientModel = User::with('manager')->where('role', 'cliente')->find($client['id']);
            if (!$clientModel) return; // Role check safety
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
            ->with(['passengers', 'manager', 'clients'])
            ->withCount('clients');

        if ($this->filterManagerId && $this->roleFilter === 'cliente') {
            $query->where('assigned_manager_id', $this->filterManagerId);
            $managerUser = User::find($this->filterManagerId);
            if ($managerUser) {
                $this->filterManagerName = $managerUser->name;
            }
        }

        if ($this->search) {
            $searchTerm = '%' . strtolower($this->search) . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$searchTerm])
                  ->orWhereRaw('id::text LIKE ?', [$searchTerm]);
            });
        }

        $users = $query->orderBy('name', $this->sortDir)->paginate($this->getPerPage());
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
            $this->primarylastname = $user->primarylastname;
            $this->secondarylastname = $user->secondarylastname;
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

        $this->updatedClientSearch();
    }

    public function updatedName($value)
    {
        if ($this->roleFilter === 'gestor' && !$this->isEditing) {
            $this->generateUniqueEmail($value, $this->primarylastname);
        }
    }

    public function updatedPrimarylastname($value)
    {
        if ($this->roleFilter === 'gestor' && !$this->isEditing) {
            $this->generateUniqueEmail($this->name, $value);
        }
    }

    private function generateUniqueEmail($name, $lastname = '')
    {
        if (empty(trim($name))) {
            $this->email = '';
            return;
        }

        // Extraer primera palabra del nombre y del apellido
        $firstName = \Illuminate\Support\Str::slug(explode(' ', trim($name))[0], '');
        $firstLast = \Illuminate\Support\Str::slug(explode(' ', trim($lastname))[0], '');

        $baseStr = $firstName;
        if (!empty($firstLast)) {
            $baseStr .= '.' . $firstLast;
        }

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
            'primarylastname' => $this->primarylastname,
            'secondarylastname' => $this->secondarylastname ?: null,
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
            // Create user with a random unusable password — they will set it themselves via reset link
            $data['password'] = \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32));
            $data['role'] = $this->roleFilter;
            $user = User::create($data);

            if ($this->roleFilter === 'gestor') {
                $this->syncClients($user);
            }

            // Send password setup link (welcome email for new gestors)
            try {
                $token = app('auth.password.broker')->createToken($user);
                $user->notify(new \App\Notifications\GestorWelcomeNotification($token));
            } catch (\Exception $e) {
                Log::error("Error enviando email de bienvenida al gestor: " . $e->getMessage());
                session()->flash('error', 'El usuario fue creado pero no se pudo enviar el correo de configuración. Revisa la configuración de SMTP.');
            }

            $t = ucfirst($this->roleFilter);
            session()->flash('message', "{$t} registrado con éxito. Se ha enviado un enlace de configuración de contraseña a {$user->email}.");
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
        
        // 2. Assign selected clients (Strictly only 'cliente' role)
        if (!empty($keptIds)) {
            User::whereIn('id', $keptIds)
                ->where('role', 'cliente')
                ->update(['assigned_manager_id' => $gestor->id]);
        }
    }

    public function resetPassword()
    {
        if (!$this->isEditing || !$this->userId) return;
        $this->regenerateUserPassword($this->userId);
    }

    public function regenerateUserPassword($id)
    {
        $user = User::find($id);
        if ($user) {
            // Send a fresh password reset link instead of generating a visible temp password
            try {
                $token = app('auth.password.broker')->createToken($user);
                $user->sendPasswordResetNotification($token);
                session()->flash('message', "Enlace de restablecimiento de contraseña enviado a {$user->email}.");
            } catch (\Exception $e) {
                Log::error("Error enviando email de reseteo: " . $e->getMessage());
                session()->flash('error', 'No se pudo enviar el correo. Revisa la configuración de SMTP.');
            }
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

        // ── Tarea de bienvenida de cartera al gestor destino ──────────
        $clientNames = User::where('assigned_manager_id', $targetGestor->id)
            ->where('role', 'cliente')
            ->pluck('name')
            ->implode(', ');
        Task::create([
            'assigned_gestor_id' => $targetGestor->id,
            'created_by'         => auth()->id(),
            'title'              => "Cartera Recibida — Clientes de {$gestor->name}",
            'description'        => "Has recibido la cartera de clientes del gestor {$gestor->name} que ha sido eliminado del sistema.\n\nClientes asignados: {$clientNames}\n\nRevisa sus expedientes y retoma la gestión de sus reservas activas.",
            'type'               => 'general',
            'status'             => 'Pendiente',
            'priority'           => 'media',
            'payload'            => ['migrated_from_gestor' => $gestor->name, 'migrated_from_id' => $gestor->id],
        ]);
        // ─────────────────────────────────────────────────────────────

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

                // ── Tarea al gestor del cliente sobre la baja ─────────────
                if ($user->assigned_manager_id) {
                    $cancelledCount = Reservation::where('user_id', $user->id)
                        ->where('status', 'Cancelada')
                        ->count();
                    Task::create([
                        'assigned_gestor_id' => $user->assigned_manager_id,
                        'created_by'         => auth()->id(),
                        'title'              => "Baja de Cliente — {$user->name}",
                        'description'        => "El cliente {$user->name} ha sido dado de baja del sistema (cuenta desactivada).\n\nSe han cancelado {$cancelledCount} reserva(s) activa(s). Comunica la baja al cliente y gestiona cualquier incidencia pendiente.",
                        'type'               => 'passenger_issue',
                        'status'             => 'Pendiente',
                        'priority'           => 'media',
                        'payload'            => ['client_id' => $user->id, 'client_name' => $user->name, 'cancelled_reservations' => $cancelledCount],
                    ]);
                }
                // ─────────────────────────────────────────────────────────

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
