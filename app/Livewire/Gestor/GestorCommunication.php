<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\User;
use App\Models\ContactLog;

class GestorCommunication extends Component
{
    public ?int $selectedClientId = null;
    public string $search = '';
    public string $log_type = 'email';
    public string $log_notes = '';
    public string $log_zoom_link = '';
    public string $log_date = '';
    public bool $showLogForm = false;
    public ?int $resolveTaskId = null;
    public string $meetingDate = '';
    public string $meetingType = 'videollamada';
    public string $meetingDescription = '';
    public bool $showMeetingForm = false;
    public bool $showClientTasks = false;
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
            'log_type' => 'required|in:llamada,email,videollamada,otro',
            'log_notes' => 'required|string|max:1000',
            'log_zoom_link' => 'nullable|url|max:500',
            'log_date' => 'nullable|date',
        ];
    }

    public function selectClient(int $id): void
    {
        $this->selectedClientId = $id;
        $this->showClientTasks = false;
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
            'client_id' => $this->selectedClientId,
            'gestor_id' => auth()->id(),
            'type' => $this->log_type,
            'zoom_link' => $this->log_zoom_link ?: null,
            'notes' => ($this->log_date ? "FECHA PROGRAMADA: {$this->log_date}\n\n" : "") . $this->log_notes,
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

        \Illuminate\Support\Facades\Mail::send('emails.gestor-notification', [
            'content' => $content,
            'zoom_link' => ($this->log_type === 'videollamada') ? $this->log_zoom_link : null,
        ], function ($message) use ($client) {
            $message->to($client->email)
                ->subject('Iris Aerospace - Actualización de su expediente');
        });

        session()->flash('message', 'Interacción registrada y correo corporativo enviado al cliente.');
        $this->resetLogForm();
    }

    private function resetLogForm(): void
    {
        $this->showLogForm = false;
        $this->log_type = 'email';
        $this->log_notes = '';
        $this->log_zoom_link = '';
        $this->log_date = '';
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
            $randomRoom = 'IrisAerospace_' . \Illuminate\Support\Str::random(12);
            $link = "https://meet.jit.si/{$randomRoom}";
        }

        ContactLog::create([
            'client_id' => $this->selectedClientId,
            'gestor_id' => auth()->id(),
            'type' => $this->meetingType,
            'zoom_link' => $link,
            'notes' => "REUNIÓN PROGRAMADA: " . $this->meetingDate . "\n" . $this->meetingDescription,
        ]);

        $client = User::find($this->selectedClientId);
        $content = "<p>Estimado/a <strong>{$client->name}</strong>,</p><p>Se ha programado una nueva reunión (" . ucfirst($this->meetingType) . ") para el día: <strong>{$this->meetingDate}</strong>.</p><p><strong>Asunto:</strong><br>{$this->meetingDescription}</p>";

        \Illuminate\Support\Facades\Mail::send('emails.gestor-notification', [
            'content' => $content,
            'zoom_link' => $link,
        ], function ($message) use ($client) {
            $message->to($client->email)
                ->subject('Iris Aerospace - Nueva Reunión Programada');
        });

        if ($this->resolveTaskId) {
            $task = \App\Models\Task::find($this->resolveTaskId);
            if ($task) {
                $task->update(['status' => 'Completada']);
            }
        }

        session()->flash('message', 'Reunión programada con éxito y correo de confirmación enviado.');
        $this->showMeetingForm = false;
        $this->resolveTaskId = null;
    }

    public function render()
    {
        $clients = User::where('assigned_manager_id', auth()->id())
            ->where('role', 'cliente')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
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
            ->where(function ($q) {
                $q->where('type', 'like', '%contacto%')
                    ->orWhere('type', 'like', '%reunion%')
                    ->orWhere('title', 'like', '%contacto%');
            })
            ->where('status', 'Pendiente')
            ->orderBy('priority', 'asc')
            ->get();

        $calendarEvents = [];
        $scheduledLogs = ContactLog::where('gestor_id', auth()->id())
            ->where(function ($q) {
                $q->where('notes', 'like', '%FECHA PROGRAMADA:%')
                    ->orWhere('notes', 'like', '%REUNIÓN PROGRAMADA:%');
            })
            ->with('client')
            ->get();

        foreach ($scheduledLogs as $log) {
            preg_match('/(?:FECHA|REUNIÓN) PROGRAMADA: (\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})/', $log->notes, $matches);
            if (!empty($matches)) {
                $date = $matches[1];
                $time = $matches[2];
                if (!isset($calendarEvents[$date])) {
                    $calendarEvents[$date] = [];
                }
                $calendarEvents[$date][] = [
                    'id' => $log->id,
                    'time' => $time,
                    'type' => $log->type,
                    'client' => $log->client?->name ?? 'Cliente',
                    'client_id' => $log->client_id,
                    'link' => $log->zoom_link,
                    'description' => $log->notes,
                    'is_meeting' => true
                ];
            }
        }

        foreach ($calendarEvents as $date => &$events) {
            usort($events, function ($a, $b) {
                return strcmp($a['time'], $b['time']);
            });
        }
        unset($events);

        $clientTasks = collect();
        if ($this->selectedClientId) {
            $clientTasks = \App\Models\Task::where('assigned_gestor_id', auth()->id())
                ->where('created_by', $this->selectedClientId)
                ->where('status', 'Pendiente')
                ->get();
        }

        $globalRequests = collect();

        foreach ($contactTasks as $task) {
            $globalRequests->push((object) [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'priority' => $task->priority,
                'priorityColor' => $task->priorityColor(),
                'type' => 'task',
                'client_id' => null,
            ]);
        }

        $now = date('Y-m-d');
        foreach ($calendarEvents as $date => $events) {
            if ($date >= $now) {
                foreach ($events as $ev) {
                    $globalRequests->push((object) [
                        'id' => $ev['id'],
                        'title' => "Reunión: " . $ev['client'],
                        'description' => $ev['time'] . " - " . $ev['type'] . "\n" . strip_tags($ev['description']),
                        'priority' => 'media',
                        'priorityColor' => $ev['type'] === 'videollamada' ? 'blue' : 'amber',
                        'type' => 'meeting',
                        'client_id' => $ev['client_id'] ?? null,
                        'link' => $ev['link'] ?? null,
                    ]);
                }
            }
        }

        return view('livewire.gestor.communication', [
            'clients' => $clients,
            'logs' => $logs,
            'selectedClient' => $selectedClient,
            'contactTasks' => $contactTasks,
            'calendarEvents' => $calendarEvents,
            'clientTasks' => $clientTasks,
            'globalRequests' => $globalRequests->sortBy('title'),
        ])->layout('layouts.gestor');
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

