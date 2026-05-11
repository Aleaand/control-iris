<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\User;
use App\Models\ContactLog;

class GestorCommunication extends Component
{
    public ?int $selectedClientId = null;
    public string $search = '';

    // — Nuevo log
    public string $log_type      = 'email';
    public string $log_notes     = '';
    public string $log_zoom_link = '';
    public string $log_date      = '';
    public bool   $showLogForm   = false;

    // — Reuniones y Tareas
    public ?int $resolveTaskId = null;
    public string $meetingDate = '';
    public string $meetingType = 'videollamada';
    public string $meetingDescription = '';
    public bool $showMeetingForm = false;

    // — Calendario
    public int $currentMonth;
    public int $currentYear;
    public ?string $selectedDate = null;

    public function mount()
    {
        $this->currentMonth = (int) date('n');
        $this->currentYear = (int) date('Y');
    }

    protected function rules(): array
    {
        return [
            'log_type'      => 'required|in:llamada,email,videollamada,otro',
            'log_notes'     => 'required|string|max:1000',
            'log_zoom_link' => 'nullable|url|max:500',
            'log_date'      => 'nullable|date',
        ];
    }

    public function selectClient(int $id): void
    {
        $this->selectedClientId = $id;
        $this->resetLogForm();
    }

    public function openLogForm(): void
    {
        $this->resetLogForm();
        $this->showLogForm = true;
    }

    public function saveLog(): void
    {
        $this->validate();

        if ($this->log_type === 'videollamada' && empty($this->log_zoom_link)) {
            $randomRoom = 'IrisAerospace_' . \Illuminate\Support\Str::random(12);
            $this->log_zoom_link = "https://meet.jit.si/{$randomRoom}";
        }

        ContactLog::create([
            'client_id'  => $this->selectedClientId,
            'gestor_id'  => auth()->id(),
            'type'       => $this->log_type,
            'zoom_link'  => $this->log_zoom_link ?: null,
            'notes'      => ($this->log_date ? "FECHA PROGRAMADA: {$this->log_date}\n\n" : "") . $this->log_notes,
        ]);

        $client = User::find($this->selectedClientId);
        $content = '';

        if ($this->log_type === 'llamada') {
            $content = "<p>Estimado/a <strong>{$client->name}</strong>,</p><p>Nos pondremos en contacto con usted vía telefónica el día y hora acordados: <strong>{$this->log_date}</strong>.</p><p><strong>Mensaje adicional:</strong><br>{$this->log_notes}</p>";
        } elseif ($this->log_type === 'videollamada') {
            $content = "<p>Estimado/a <strong>{$client->name}</strong>,</p><p>Hemos programado una videollamada para el día y hora: <strong>{$this->log_date}</strong>.</p><p>Puede acceder a la sala de reunión a través del siguiente enlace:</p><p><a href='{$this->log_zoom_link}' style='color: #10b981; font-weight: bold;'>{$this->log_zoom_link}</a></p><p><strong>Mensaje adicional:</strong><br>{$this->log_notes}</p>";
        } else {
            $content = "<p>Estimado/a <strong>{$client->name}</strong>,</p><p>{$this->log_notes}</p>";
        }

        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #10b981; padding: 20px; border-radius: 10px; background-color: #ffffff; color: #333;'>
                <h2 style='color: #10b981; text-align: center; text-transform: uppercase; letter-spacing: 2px;'>Iris Aerospace</h2>
                <h3 style='color: #666; text-align: center; font-size: 14px;'>Comunicación Oficial de su Gestor</h3>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <div style='line-height: 1.6;'>
                    {$content}
                </div>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 12px; color: #999; text-align: center;'>Gestión de Clientes - Iris Aerospace<br>Este es un correo automático, por favor no responda directamente a esta dirección.</p>
            </div>
        ";

        \Illuminate\Support\Facades\Mail::html($html, function ($message) use ($client) {
            $message->to($client->email)
                    ->subject('Iris Aerospace - Actualización de su expediente');
        });

        session()->flash('message', 'Interacción registrada y correo corporativo enviado al cliente.');
        $this->resetLogForm();
    }

    private function resetLogForm(): void
    {
        $this->showLogForm   = false;
        $this->log_type      = 'email';
        $this->log_notes     = '';
        $this->log_zoom_link = '';
        $this->log_date      = '';
        $this->resetValidation();
    }

    public function openMeetingForm(?int $taskId = null): void
    {
        $this->resolveTaskId = $taskId;
        $this->meetingDate = '';
        $this->meetingType = 'videollamada';
        $this->meetingDescription = '';
        $this->showMeetingForm = true;
    }

    public function scheduleMeeting(): void
    {
        $this->validate([
            'meetingDate' => 'required|date',
            'meetingType' => 'required|in:videollamada,telefono',
            'meetingDescription' => 'required|string|max:500',
        ]);

        $link = null;
        if ($this->meetingType === 'videollamada') {
            // Generate Jitsi Meet link
            $randomRoom = 'IrisAerospace_' . \Illuminate\Support\Str::random(12);
            $link = "https://meet.jit.si/{$randomRoom}";
        }

        ContactLog::create([
            'client_id'  => $this->selectedClientId,
            'gestor_id'  => auth()->id(),
            'type'       => $this->meetingType,
            'zoom_link'  => $link,
            'notes'      => "REUNIÓN PROGRAMADA: " . $this->meetingDate . "\n" . $this->meetingDescription,
        ]);

        if ($this->resolveTaskId) {
            $task = \App\Models\Task::find($this->resolveTaskId);
            if ($task) {
                $task->update(['status' => 'Completada']);
            }
        }

        session()->flash('message', 'Reunión programada con éxito.');
        $this->showMeetingForm = false;
        $this->resolveTaskId = null;
    }

    public function render()
    {
        $clients = User::where('assigned_manager_id', auth()->id())
            ->where('role', 'cliente')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->orderBy('name')
            ->get();

        $logs = $this->selectedClientId
            ? ContactLog::where('client_id', $this->selectedClientId)
                ->where('gestor_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $selectedClient = $this->selectedClientId
            ? User::where('assigned_manager_id', auth()->id())->find($this->selectedClientId)
            : null;

        $contactTasks = \App\Models\Task::where('assigned_gestor_id', auth()->id())
            ->where(function($q) {
                $q->where('type', 'like', '%contacto%')
                  ->orWhere('type', 'like', '%reunion%')
                  ->orWhere('title', 'like', '%contacto%');
            })
            ->where('status', 'Pendiente')
            ->orderBy('priority', 'asc')
            ->get();

        // Extraer eventos del calendario (buscando FECHA PROGRAMADA en las notas)
        $calendarEvents = [];
        $scheduledLogs = ContactLog::where('gestor_id', auth()->id())
            ->where('notes', 'like', '%FECHA PROGRAMADA:%')
            ->with('client')
            ->get();

        foreach ($scheduledLogs as $log) {
            preg_match('/FECHA PROGRAMADA: (\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})/', $log->notes, $matches);
            if (!empty($matches)) {
                $date = $matches[1];
                $time = $matches[2];
                if (!isset($calendarEvents[$date])) {
                    $calendarEvents[$date] = [];
                }
                $calendarEvents[$date][] = [
                    'time' => $time,
                    'type' => $log->type,
                    'client' => $log->client?->name ?? 'Cliente',
                    'link' => $log->zoom_link,
                    'description' => $log->notes
                ];
            }
        }

        // Ordenar los eventos cronológicamente dentro de cada día
        foreach ($calendarEvents as $date => &$events) {
            usort($events, function($a, $b) {
                return strcmp($a['time'], $b['time']);
            });
        }
        unset($events);

        // Tareas específicas del cliente seleccionado
        $clientTasks = collect();
        if ($this->selectedClientId) {
            // Buscamos tareas que en su payload tengan client_id o que el creador sea el cliente
            $clientTasks = \App\Models\Task::where('assigned_gestor_id', auth()->id())
                ->where('created_by', $this->selectedClientId)
                ->where('status', 'Pendiente')
                ->get();
        }

        return view('livewire.gestor.communication', compact('clients', 'logs', 'selectedClient', 'contactTasks', 'calendarEvents', 'clientTasks'))
            ->layout('layouts.gestor');
    }

    public function prevMonth()
    {
        $this->currentMonth--;
        if ($this->currentMonth < 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        }
    }

    public function nextMonth()
    {
        $this->currentMonth++;
        if ($this->currentMonth > 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        }
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
    }

    public function closeDateModal(): void
    {
        $this->selectedDate = null;
    }
}

