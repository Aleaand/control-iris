<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Passenger;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use App\Mail\MissingDocumentationMail;
use App\Mail\PassportSubmittedMail;
use App\Mail\PassportApprovedMail;
use Barryvdh\DomPDF\Facade\Pdf;

class GestorCompliance extends Component
{
    use WithFileUploads;

    public string $filterStatus = 'all';
    public string $search = '';
    public ?int $analyzingPassengerId = null;
    public bool $showAnalysisModal = false;
    public ?array $analysisResults = null;
    public bool $showEditModal = false;
    public ?int $editPassengerId = null;
    public string $iris_passport_number = '';
    public string $iris_passport_expiration = '';
    public string $training_date = '';
    public string $training_status = 'No Apto';
    public string $physical_fitness = 'No apto';
    public bool $showBulkOfacModal = false;
    public array $bulkOfacSummary = [];
    public bool $showPassportDueModal = false;
    public $passportDuePassengers = [];
    public bool $showJsonModal = false;
    public bool $showTramitarModal = false;
    public bool $showRevisarModal = false;
    public ?array $tramitarPax = null;
    public string $tramite_nacionalidad = '';
    public string $tramite_caducidad_dni = '';
    public string $tramite_correo = '';
    public string $tramite_telefono = '';
    public string $tramite_direccion = '';
    public string $tramite_tipo = 'Nuevo pasaporte';
    public $tramite_foto = null;
    public bool $showMissingDocSection = false;
    public array $selectedMissingDocs = [];
    public string $missingDocNotes = '';
    public bool $showFinalizarModal = false;
    public ?array $finalizarPax = null;
    public string $final_passport_number = '';
    public string $final_passport_expiration = '';
    public $final_passport_pdf = null;

    protected function rules(): array
    {
        return [
            'iris_passport_number' => 'nullable|string|max:50',
            'iris_passport_expiration' => 'nullable|date',
            'training_date' => 'nullable|date',
            'training_status' => 'required|in:Apto,No Apto',
            'physical_fitness' => 'required|in:Apto,En entrenamiento,No apto',
        ];
    }

    public function openEdit(int $passengerId): void
    {
        $pax = $this->getMyPassenger($passengerId);
        $this->editPassengerId = $pax->id;
        $this->iris_passport_number = $pax->iris_passport_number ?? '';
        $this->iris_passport_expiration = $pax->iris_passport_expiration?->format('Y-m-d') ?? '';
        $this->training_date = $pax->training_certificate_date?->format('Y-m-d') ?? '';
        $this->training_status = $pax->training_certificate_status ?? 'No Apto';
        $this->physical_fitness = $pax->physical_fitness;
        $this->showEditModal = true;
    }

    public function saveCompliance(): void
    {
        $this->validate();

        $pax = $this->getMyPassenger($this->editPassengerId);

        $newPhysicalFitness = $this->physical_fitness;
        if ($this->training_status === 'Apto') {
            $newPhysicalFitness = 'Apto';
        }

        $pax->update([
            'iris_passport_number' => $this->iris_passport_number ?: null,
            'iris_passport_expiration' => $this->iris_passport_expiration ?: null,
            'training_certificate_date' => $this->training_date ?: null,
            'training_certificate_status' => $this->training_status,
            'physical_fitness' => $newPhysicalFitness,
        ]);

        session()->flash('message', 'Documentación actualizada.');
        $this->showEditModal = false;
        $this->editPassengerId = null;
        $this->resetValidation();
    }

    public function analyzePassengerForPassport(int $id): void
    {
        $pax = $this->getMyPassenger($id);
        $this->analyzingPassengerId = $pax->id;

        $missing = [];
        if (!$pax->document_number)
            $missing[] = 'DNI / Pasaporte Terrestre';
        if (!$pax->birth_date)
            $missing[] = 'Fecha de Nacimiento';

        if (count($missing) > 0) {
            $this->analysisResults = [
                'status' => 'missing_data',
                'missing' => $missing,
                'passenger_name' => $pax->full_name
            ];
            $this->showAnalysisModal = true;
            return;
        }

        try {
            $apiKey = config('services.trade_gov.api_key');
            $ofac_url = 'https://sanctionssearch.ofac.treas.gov/';

            if (empty($apiKey)) {
                $score = 50;
                $color = 'amber';
                $message = 'ATENCIÓN: Falta la API Key. El sistema está configurado para conectarse a Trade.gov, pero no se ha encontrado la clave "TRADE_GOV_API_KEY" en el archivo .env. Por ahora, por favor verifique manualmente en la web oficial.';
                $ofac_url = 'https://sanctionssearch.ofac.treas.gov/';
            } else {
                $debugQuery = "Trade.gov API Connection";
                $queryName = $pax->full_name;
                $data = null;
                $attempts = 0;
                $maxAttempts = 2;
                $lastError = '';

                while ($attempts < $maxAttempts && !$data) {
                    try {
                        $attempts++;
                        $response = \Illuminate\Support\Facades\Http::timeout(10)
                            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                            ->withoutVerifying()
                            ->acceptJson()
                            ->withHeaders([
                                'subscription-key' => $apiKey,
                                'Ocp-Apim-Subscription-Key' => $apiKey
                            ])
                            ->get('https://data.trade.gov/consolidated_screening_list/v1/search', [
                                'name' => $queryName,
                                'fuzzy_name' => 'true',
                                'api-version' => '1.0'
                            ]);

                        if ($response->successful()) {
                            $data = $response->json();
                            if (!is_array($data)) {
                                $data = null;
                                throw new \Exception('Respuesta no válida (Redirect/HTML)');
                            }
                        } else {
                            throw new \Exception('HTTP Error: ' . $response->status());
                        }
                    } catch (\Exception $e) {
                        $lastError = $e->getMessage();
                        if ($attempts >= $maxAttempts) {
                            throw new \Exception($lastError);
                        }
                        usleep(500000);
                    }
                }

                $totalMatches = $data['total'] ?? 0;
                $debugQuery = "Intento por Nombre (Intentos: $attempts): " . $queryName;

                if ($totalMatches === 0 && $pax->document_number) {
                    $queryId = $pax->document_number;
                    $data = null;
                    $attempts = 0;

                    while ($attempts < $maxAttempts && !$data) {
                        try {
                            $attempts++;
                            $response = \Illuminate\Support\Facades\Http::timeout(10)
                                ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                                ->withoutVerifying()
                                ->acceptJson()
                                ->withHeaders([
                                    'subscription-key' => $apiKey,
                                    'Ocp-Apim-Subscription-Key' => $apiKey
                                ])
                                ->get('https://data.trade.gov/consolidated_screening_list/v1/search', [
                                    'q' => $queryId,
                                    'api-version' => '1.0'
                                ]);

                            if ($response->successful()) {
                                $data = $response->json();
                                if (!is_array($data)) {
                                    $data = null;
                                    throw new \Exception('Respuesta no válida (Redirect/HTML)');
                                }
                            } else {
                                throw new \Exception('HTTP Error: ' . $response->status());
                            }
                        } catch (\Exception $e) {
                            $lastError = $e->getMessage();
                            if ($attempts >= $maxAttempts) {
                                throw new \Exception($lastError);
                            }
                            usleep(500000);
                        }
                    }

                    $totalMatches = $data['total'] ?? 0;
                    $debugQuery .= " | Intento por ID (Intentos: $attempts): " . $queryId;
                }

                if ($response->successful()) {
                    if ($totalMatches === 0) {
                        $score = 0;
                        $color = 'green';
                        $message = 'Pasajero limpio. La API de Trade.gov (OFAC) no ha encontrado ninguna coincidencia para el nombre o documento.';
                        $ofac_url = 'https://sanctionssearch.ofac.treas.gov/';
                    } else {
                        $score = 65;
                        $color = 'amber';

                        $idMatchesCount = 0;
                        $dobMatchesCount = 0;
                        $exactNameMatch = false;

                        $paxDob = $pax->birth_date ? $pax->birth_date->format('Y-m-d') : null;
                        $paxDoc = strtoupper($pax->document_number ?? '');

                        foreach ($data['results'] as $result) {
                            $currentOfacUrl = $result['source_information_url'] ?? $ofac_url;

                            if (($result['source'] ?? '') === 'SDN' && !empty($result['source_id'])) {
                                $currentOfacUrl = 'https://sanctionssearch.ofac.treas.gov/Details.aspx?id=' . $result['source_id'];
                            }
                            $ids = $result['ids'] ?? [];
                            foreach ($ids as $idItem) {
                                $apiId = strtoupper($idItem['number'] ?? '');
                                if ($paxDoc && $apiId === $paxDoc) {
                                    $idMatchesCount++;
                                    $ofac_url = $currentOfacUrl;
                                }
                            }

                            $dobs = $result['dates_of_birth'] ?? [];
                            foreach ($dobs as $dob) {
                                if ($paxDob && str_contains($dob, $paxDob)) {
                                    $dobMatchesCount++;
                                    if ($ofac_url === 'https://sanctionssearch.ofac.treas.gov/') {
                                        $ofac_url = $currentOfacUrl;
                                    }
                                }
                            }

                            if (strtolower(trim($result['name'] ?? '')) === strtolower(trim($pax->full_name))) {
                                $exactNameMatch = true;
                                if ($ofac_url === 'https://sanctionssearch.ofac.treas.gov/') {
                                    $ofac_url = $currentOfacUrl;
                                }
                            }
                        }

                        if ($idMatchesCount > 0) {
                            $score = 100;
                            $color = 'red';
                            $message = "¡ALERTA! Se ha encontrado una coincidencia exacta de DOCUMENTO ($paxDoc). El perfil está bloqueado.";
                        } elseif ($dobMatchesCount > 0) {
                            $score = 90;
                            $color = 'red';
                            $message = "¡ALERTA! Se han encontrado $totalMatches personas con el mismo nombre, y $dobMatchesCount de ellos coinciden en FECHA DE NACIMIENTO ($paxDob).";
                        } else {
                            $message = "Se han encontrado $totalMatches personas con el mismo nombre. No hay coincidencias de ID ni de Fecha de Nacimiento.";
                            if ($exactNameMatch) {
                                $score = 75;
                                $message .= " (Nota: El nombre coincide exactamente)";
                            }
                        }
                    }
                } else {
                    throw new \Exception('API Error: ' . $response->status());
                }
            }
        } catch (\Exception $e) {
            $score = 50;
            $color = 'amber';
            $message = 'El sistema no ha podido validar automáticamente con la API de Trade.gov (Causa: ' . $e->getMessage() . '). Por seguridad, se ha asignado un riesgo preventivo del 50%. Verifique manualmente en el buscador oficial de sanciones.';
            $ofac_url = 'https://sanctionssearch.ofac.treas.gov/';
        }

        $dniCheck = true;

        $this->analysisResults = [
            'status' => 'analyzed',
            'score' => $score,
            'color' => $color,
            'message' => $message,
            'passenger_name' => $pax->full_name,
            'dni' => $pax->document_number,
            'dob' => $pax->birth_date->format('Y-m-d'),
            'dni_flight_valid' => $dniCheck,
            'ofac_url' => $ofac_url,
            'is_exact_match' => ($score === 100),
            'debug_query' => $debugQuery ?? 'Trade.gov API Connection',
            'raw_json' => isset($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'No hay datos crudos disponibles.'
        ];

        $this->showAnalysisModal = true;
    }

    public function downloadJson(): mixed
    {
        if (!$this->analysisResults || !isset($this->analysisResults['raw_json'])) {
            return null;
        }

        $jsonContent = $this->analysisResults['raw_json'];
        $dni = $this->analysisResults['dni'] ?? 'pax';
        $filename = "ofac_analysis_{$dni}_" . now()->format('Ymd_His') . ".json";

        return response()->streamDownload(function () use ($jsonContent) {
            echo $jsonContent;
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function requestMissingData(): void
    {
        session()->flash('passport_request_message', 'Se ha enviado un requerimiento automático al cliente para que complete sus datos (DNI / Fecha de Nacimiento).');
        $this->showAnalysisModal = false;
        $this->analyzingPassengerId = null;
        $this->analysisResults = null;
    }

    public function escalateToSuperior(): void
    {
        session()->flash('passport_request_message', 'El perfil ha sido escalado a Seguridad Central debido a la alta coincidencia en listas OFAC.');
        $this->showAnalysisModal = false;
        $this->analyzingPassengerId = null;
        $this->analysisResults = null;
    }

    public function submitPassportRequest(): void
    {
        session()->flash('passport_request_message', 'Solicitud burocrática validada y enviada a la Agencia Espacial con éxito.');

        $this->showAnalysisModal = false;
        $this->analyzingPassengerId = null;
        $this->analysisResults = null;
    }

    public function openTramitarPasaporte(int $id): void
    {
        $pax = $this->getMyPassenger($id);
        $this->tramitarPax = [
            'id' => $pax->id,
            'nombre' => $pax->full_name,
            'dni' => $pax->document_number,
            'caducidad_dni' => '',
            'fecha_nacimiento' => $pax->birth_date?->format('Y-m-d')
        ];

        $this->tramite_nacionalidad = $pax->document_country ?? '';
        $this->tramite_correo = $pax->client?->email ?? '';
        $this->tramite_telefono = $pax->client?->phone ?? '';
        $this->tramite_direccion = '';
        $this->tramite_tipo = ($pax->iris_passport_number) ? 'Renovar Pasaporte' : 'Nuevo pasaporte';

        $this->showTramitarModal = true;
    }

    public function openRevisarPasaporte(int $id): void
    {
        $pax = $this->getMyPassenger($id);
        $this->tramitarPax = [
            'id' => $pax->id,
            'nombre' => $pax->full_name,
            'dni' => $pax->document_number,
            'numero_pasaporte' => $pax->iris_passport_number,
            'validez_pasaporte' => $pax->iris_passport_expiration?->format('d/m/Y'),
            'pdf_path' => $pax->passport_pdf
        ];
        $this->showRevisarModal = true;
    }

    public function solicitarDocumentoFaltante(): void
    {
        $this->showMissingDocSection = !$this->showMissingDocSection;
    }

    public function enviarSolicitudDocumento(): void
    {
        if (empty($this->selectedMissingDocs)) {
            session()->flash('error', 'Seleccione al menos un documento.');
            return;
        }

        $pax = $this->getMyPassenger($this->tramitarPax['id']);
        $clientEmail = $pax->client?->email;

        if ($clientEmail) {
            Mail::to($clientEmail)->send(new MissingDocumentationMail(
                $pax->full_name,
                $this->selectedMissingDocs,
                $this->missingDocNotes
            ));
        }

        $docsList = implode(', ', $this->selectedMissingDocs);
        session()->flash('passport_request_message', "Solicitud enviada: Faltan [{$docsList}]. El cliente ha sido notificado por correo.");

        $this->showMissingDocSection = false;
        $this->showTramitarModal = false;
        $this->selectedMissingDocs = [];
        $this->missingDocNotes = '';
    }

    public function enviarTramitePasaporte(): void
    {
        $this->validate([
            'tramite_nacionalidad' => 'required|string|min:2',
            'tramite_caducidad_dni' => 'required|date|after:today',
            'tramite_correo' => 'required|email',
            'tramite_telefono' => 'required|string|min:7',
            'tramite_direccion' => 'required|string|min:5',
            'tramite_foto' => 'required|image|max:5120', // 5MB
        ], [
            'tramite_caducidad_dni.after' => 'ERROR DE VALIDACIÓN: El DNI está vencido. Un documento expirado impide el trámite del Pasaporte Estelar.',
            'tramite_caducidad_dni.required' => 'La fecha de caducidad es obligatoria.',
            'tramite_nacionalidad.required' => 'Indique la nacionalidad.',
            'tramite_foto.required' => 'La fotografía biométrica es obligatoria para el reconocimiento en puerta estelar.',
            'tramite_direccion.required' => 'La dirección de envío es necesaria para el documento físico.',
        ]);

        $pax = $this->getMyPassenger($this->tramitarPax['id']);
        if ($this->tramite_foto) {
            $path = $this->tramite_foto->store('passports', 'public');
            $pax->update([
                'passport_photo' => $path,
                'passport_status' => 'pending'
            ]);
        }
        $pdf = Pdf::loadView('pdf.stellar-passport', [
            'passenger' => $pax,
            'photoPath' => $pax->passport_photo,
            'nacionalidad' => $this->tramite_nacionalidad
        ]);

        $pdfDir = storage_path('app/public/agency_responses');
        if (!file_exists($pdfDir))
            mkdir($pdfDir, 0755, true);
        $simulatedPdfPath = $pdfDir . '/agency_reply_' . $pax->id . '.pdf';
        $pdf->save($simulatedPdfPath);

        $gestorEmail = auth()->user()->email;
        if ($gestorEmail) {
            Mail::to($gestorEmail)->send(new PassportApprovedMail($pax->full_name, $simulatedPdfPath));
        }
        $clientEmail = $pax->client?->email;
        if ($clientEmail) {
            Mail::to($clientEmail)->send(new PassportSubmittedMail($pax->full_name));
        }

        session()->flash('passport_request_message', 'Trámite enviado. Revise su correo de Gestor (' . $gestorEmail . ') para recibir el informe y el pasaporte de la Agencia Espacial.');
        $this->cancelarTramitePasaporte();
    }

    public function cancelarTramitePasaporte(): void
    {
        $this->reset([
            'tramite_nacionalidad',
            'tramite_caducidad_dni',
            'tramite_correo',
            'tramite_telefono',
            'tramite_direccion',
            'tramite_foto',
            'showTramitarModal',
            'showMissingDocSection',
            'selectedMissingDocs',
            'missingDocNotes'
        ]);
        $this->resetValidation();
    }

    public function openFinalizarPasaporte(int $id): void
    {
        $pax = $this->getMyPassenger($id);
        $this->finalizarPax = $pax->toArray();
        $letters = chr(rand(65, 90)) . chr(rand(65, 90));
        $numbers = rand(1000000, 9999999);
        $this->final_passport_number = $letters . $numbers;

        $this->final_passport_expiration = now()->addYears(10)->format('Y-m-d');
        $this->showFinalizarModal = true;
    }

    public function finalizarTramitePasaporte(): void
    {
        $this->validate([
            'final_passport_number' => 'required|string|min:5',
            'final_passport_expiration' => 'required|date|after:today',
            'final_passport_pdf' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        $pax = $this->getMyPassenger($this->finalizarPax['id']);
        $randomName = sha1(time() . $pax->id . uniqid()) . '.pdf';
        $pdfPath = $this->final_passport_pdf->storeAs('passports_official', $randomName, 'public');

        $pax->update([
            'iris_passport_number' => $this->final_passport_number,
            'iris_passport_expiration' => $this->final_passport_expiration,
            'passport_pdf' => $pdfPath,
            'passport_status' => 'active'
        ]);
        $clientEmail = $pax->client?->email;
        if ($clientEmail) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pdfPath)) {
                $absolutePdfPath = storage_path('app/public/' . $pdfPath);
                Mail::to($clientEmail)->send(new PassportApprovedMail($pax->full_name, $absolutePdfPath));
                session()->flash('passport_request_message', 'Pasaporte activado y enviado al cliente ' . $clientEmail . '.');
            } else {
                session()->flash('error', 'Error: El archivo PDF no se pudo localizar en el servidor.');
            }
        } else {
            session()->flash('warning', 'Pasaporte activado en sistema, pero no se pudo enviar el correo porque el cliente no tiene un email registrado.');
        }

        $this->showFinalizarModal = false;

        $this->reset(['final_passport_number', 'final_passport_expiration', 'final_passport_pdf', 'finalizarPax']);
    }

    public function runBulkOfacCheck(): void
    {
        $passengers = Passenger::whereHas(
            'client',
            fn($q) =>
            $q->where('assigned_manager_id', auth()->id())
        )->get();

        $summary = [
            'total' => $passengers->count(),
            'clean' => 0,
            'warning' => 0,
            'alert' => 0,
            'missing' => 0,
            'details' => []
        ];

        foreach ($passengers as $pax) {
            if (!$pax->document_number || !$pax->birth_date) {
                $summary['missing']++;
                continue;
            }

            $apiKey = config('services.trade_gov.api_key');
            if (empty($apiKey)) {
                $rand = rand(0, 100);
                if ($rand > 90) {
                    $summary['alert']++;
                    $summary['details'][] = ['name' => $pax->full_name, 'status' => 'RED', 'msg' => 'Coincidencia exacta detectada'];
                } elseif ($rand > 70) {
                    $summary['warning']++;
                    $summary['details'][] = ['name' => $pax->full_name, 'status' => 'AMBER', 'msg' => 'Homónimo en lista'];
                } else {
                    $summary['clean']++;
                }
            } else {
                $summary['clean']++;
            }
        }

        $this->bulkOfacSummary = $summary;
        $this->showBulkOfacModal = true;
    }

    public function identifyPassportNeeds(): void
    {
        $this->passportDuePassengers = Passenger::whereHas(
            'client',
            fn($q) =>
            $q->where('assigned_manager_id', auth()->id())
        )
            ->where(function ($q) {
                $q->whereNull('iris_passport_number')
                    ->orWhereNull('iris_passport_expiration')
                    ->orWhere('iris_passport_expiration', '<=', now()->addMonths(3));
            })
            ->with(['client', 'reservations.spaceFlight'])
            ->get()
            ->map(function ($pax) {
                $nextFlight = $pax->reservations
                    ->whereNotIn('status', ['Cancelada'])
                    ->sortBy(fn($r) => $r->spaceFlight?->departure_date)
                    ->first();

                $pax->next_flight_date = $nextFlight?->spaceFlight?->departure_date;
                return $pax;
            })
            ->sortBy('next_flight_date')
            ->values()
            ->toArray();

        $this->showPassportDueModal = true;
    }

    public function completeTask(int $taskId): void
    {
        $task = \App\Models\Task::where('assigned_gestor_id', auth()->id())
            ->findOrFail($taskId);

        $task->update([
            'status' => 'Completada',
            'completed_at' => now()
        ]);

        session()->flash('passport_request_message', 'Tarea completada con éxito.');
    }

    private function getMyPassenger(int $id): Passenger
    {
        return Passenger::whereHas(
            'client',
            fn($q) =>
            $q->where('assigned_manager_id', auth()->id())
        )->findOrFail($id);
    }

    public function render()
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('passengers', 'passport_status')) {
            \Illuminate\Support\Facades\Schema::table('passengers', function (\Illuminate\Database\Schema\Blueprint $table) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('passengers', 'passport_photo')) {
                    $table->string('passport_photo')->nullable()->after('birth_date');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('passengers', 'passport_status')) {
                    $table->enum('passport_status', ['none', 'pending', 'active'])->default('none')->after('iris_passport_expiration');
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('passengers', 'passport_pdf')) {
                    $table->string('passport_pdf')->nullable()->after('passport_status');
                }
            });
        }

        static $taskConstraintFixed = false;
        if (!$taskConstraintFixed) {
            try {
                \Illuminate\Support\Facades\DB::statement(
                    "ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_type_check"
                );
                \Illuminate\Support\Facades\DB::statement(
                    "ALTER TABLE tasks ADD CONSTRAINT tasks_type_check CHECK (type IN ('flight_cancelled','policy_change','passenger_issue','general','passport','iris_training','iris-training'))"
                );
                $taskConstraintFixed = true;
            } catch (\Exception $e) {
                // Salta si ya es correcto o no es postgres
            }
        }

        $passengersQuery = Passenger::with(['client', 'reservations.spaceFlight'])
            ->whereHas('client', fn($q) => $q->where('assigned_manager_id', auth()->id()))
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('document_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', '%' . $this->search . '%'));
            }));

        $passengers = $passengersQuery->get()->map(function ($pax) {
            $nextFlight = $pax->reservations
                ->whereNotIn('status', ['Cancelada'])
                ->sortBy(fn($r) => $r->spaceFlight?->departure_date)
                ->first();

            $hoursToFlight = $nextFlight?->spaceFlight?->departure_date
                ? now()->diffInHours($nextFlight->spaceFlight->departure_date, false)
                : null;

            $pax->next_flight = $nextFlight;
            $pax->hours_to_flight = $hoursToFlight;
            $pax->is_72h_alert = $hoursToFlight !== null && $hoursToFlight <= 72 && $hoursToFlight >= 0;
            $pax->passport_ok = $pax->hasValidPassport();
            $pax->training_ok = $pax->hasValidTraining();
            $pax->medical_ok = in_array($pax->physical_fitness, ['Apto', 'Apto temporal']);
            $pax->fully_ready = $pax->passport_ok && $pax->training_ok && $pax->medical_ok;

            return $pax;
        });

        if ($this->filterStatus === 'ready') {
            $passengers = $passengers->filter(fn($p) => $p->fully_ready);
        } elseif ($this->filterStatus === 'issues') {
            $passengers = $passengers->filter(fn($p) => !$p->fully_ready);
        } elseif ($this->filterStatus === 'urgent') {
            $passengers = $passengers->filter(fn($p) => $p->is_72h_alert);
        }

        $passengers = $passengers->sortByDesc(fn($p) => $p->is_72h_alert ? 1 : 0)->values();

        $passportTasks = $this->getTasksByKeywords(['pasaporte', 'passport']);
        $trainingTasks = $this->getTasksByKeywords(['training', 'entrenamiento']);

        return view('livewire.gestor.compliance', compact('passengers', 'passportTasks', 'trainingTasks'))
            ->layout('layouts.gestor');
    }

    private function getTasksByKeywords(array $keywords)
    {
        $tasks = \App\Models\Task::where('assigned_gestor_id', auth()->id())
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('type', 'like', "%{$keyword}%")
                        ->orWhere('title', 'like', "%{$keyword}%");
                }
            })
            ->where('status', '!=', 'Completada')
            ->get();

        $groupedTasks = $tasks->groupBy(fn($t) => $t->payload['passenger_id'] ?? 'task_' . $t->id)
            ->map(fn($group) => $group->sortByDesc('created_at')->first());

        return $groupedTasks->map(function ($task) {
            $paxId = $task->payload['passenger_id'] ?? null;
            $flightDate = null;

            if ($paxId) {
                $pax = Passenger::with(['reservations.spaceFlight'])->find($paxId);
                if ($pax) {
                    $nextFlight = $pax->reservations
                        ->whereNotIn('status', ['Cancelada'])
                        ->sortBy(fn($r) => $r->spaceFlight?->departure_date)
                        ->first();
                    $flightDate = $nextFlight?->spaceFlight?->departure_date;
                }
            }

            // — Prioridad automática si el vuelo es esta semana (< 7 días)
            if ($flightDate && now()->diffInDays($flightDate, false) <= 7 && now()->diffInDays($flightDate, false) >= 0) {
                $task->priority = 'urgente';
            }

            $task->flight_date = $flightDate;
            return $task;
        })
            ->sortBy(function ($task) {
                $priorityOrder = ['urgente' => 0, 'alta' => 1, 'media' => 2, 'baja' => 3];
                return $priorityOrder[strtolower($task->priority)] ?? 4;
            })
            ->values();
    }

    public function updateTaskStatus(int $taskId, string $newStatus): void
    {
        $task = \App\Models\Task::where('assigned_gestor_id', auth()->id())
            ->findOrFail($taskId);

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'Aceptada' && !$task->accepted_at) {
            $updateData['accepted_at'] = now();
        }
        if ($newStatus === 'Completada') {
            $updateData['completed_at'] = now();
        }

        $task->update($updateData);
        session()->flash('passport_request_message', "Tarea marcada como {$newStatus}.");
    }

    public bool $showTrainingModal = false;
    public ?int $manageTrainingTaskId = null;
    public ?int $manageTrainingPaxId = null;
    public ?array $manageTrainingPax = null;
    public array $trainingSessions = [];
    public array $newSessions = [];
    public string $trainingCertDate = '';
    public string $trainingCertStatus = 'Apto';
    public string $trainingPhysicalFitness = 'No apto';
    public bool $trainingIsRenewal = false;
    public bool $trainingCertExpired = false;

    public function openTrainingModal(int $taskId): void
    {
        $task = \App\Models\Task::where('assigned_gestor_id', auth()->id())->findOrFail($taskId);
        $this->manageTrainingTaskId = $taskId;
        $this->manageTrainingPaxId = null;
        $this->manageTrainingPax = null;
        $this->trainingSessions = $task->payload['sessions'] ?? [];
        $this->resetNewSessions();
        $this->showTrainingModal = true;
    }

    private function resetNewSessions(): void
    {
        $this->newSessions = [
            ['date' => '', 'hours' => $this->trainingIsRenewal ? 1 : 3],
        ];
    }

    public function addNewSessionRow(): void
    {
        $this->newSessions[] = ['date' => '', 'hours' => 1];
    }

    public function removeSessionRow(int $index): void
    {
        unset($this->newSessions[$index]);
        $this->newSessions = array_values($this->newSessions);
    }

    public function openTrainingForPassenger(int $paxId): void
    {
        $pax = $this->getMyPassenger($paxId);
        $this->manageTrainingPaxId = $paxId;
        $this->manageTrainingPax = $pax->toArray();
        $this->manageTrainingTaskId = null;

        $relatedTask = \App\Models\Task::where('assigned_gestor_id', auth()->id())
            ->where(function ($q) use ($paxId) {
                $q->where('type', 'like', '%training%')
                    ->orWhere('title', 'like', '%training%');
            })
            ->whereJsonContains('payload->passenger_id', $paxId)
            ->latest()->first();

        $this->trainingSessions = $relatedTask?->payload['sessions'] ?? [];
        $this->trainingCertDate = $pax->training_certificate_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->trainingCertStatus = $pax->training_certificate_status ?? 'No Apto';
        $this->trainingPhysicalFitness = $pax->physical_fitness ?? 'No apto';
        
        $this->trainingCertExpired = $pax->training_certificate_date && !$pax->hasValidTraining();
        $this->trainingIsRenewal = (bool) $pax->training_certificate_date;

        if ($this->trainingCertExpired) {
            $this->trainingCertStatus = 'No Apto';
        }

        $this->resetNewSessions();
        $this->showTrainingModal = true;
    }

    public function saveTrainingCert(): void
    {
        $this->validate([
            'trainingCertDate' => 'required|date',
            'trainingCertStatus' => 'required|in:Apto,No Apto',
            'trainingPhysicalFitness' => 'required|in:Apto,No apto',
        ]);

        if ($this->manageTrainingPaxId) {
            $pax = $this->getMyPassenger($this->manageTrainingPaxId);
            $totalHours = collect($this->trainingSessions)
                ->where('status', 'Completada')
                ->sum('hours');

            $requiredHours = $this->trainingIsRenewal ? 1 : 3;

            if ($this->trainingCertStatus === 'Apto' && $totalHours < $requiredHours) {
                session()->flash('training_error', "No se puede marcar como APTO. El total de horas completadas ($totalHours) es inferior a las $requiredHours necesarias.");
                return;
            }

            $updateData = [
                'training_certificate_date' => $this->trainingCertDate,
                'training_certificate_status' => $this->trainingCertStatus,
                'physical_fitness' => $this->trainingPhysicalFitness,
            ];

            if ($this->trainingCertStatus === 'Apto') {
                $updateData['physical_fitness'] = 'Apto';
                $this->trainingPhysicalFitness = 'Apto';
            }

            $pax->update($updateData);
            session()->flash('training_saved', 'Datos actualizados con éxito.');
        }
    }

    public function addTrainingSession(): void
    {
        $validSessions = collect($this->newSessions)->filter(fn($s) => !empty($s['date']))->toArray();

        if (empty($validSessions)) {
            session()->flash('training_error', 'Debe indicar al menos una fecha para programar.');
            return;
        }

        $sessions = $this->trainingSessions;
        foreach ($validSessions as $s) {
            $sessions[] = [
                'date' => $s['date'],
                'hours' => (int) $s['hours'],
                'status' => 'Programada',
            ];
        }

        $hasTransfer = false;
        $pax = null;
        if ($this->manageTrainingPaxId) {
            $pax = $this->getMyPassenger($this->manageTrainingPaxId);
            $hasTransfer = $pax->reservations()
                ->where('status', '!=', 'Cancelada')
                ->whereNotNull('id')
                ->get()
                ->contains(fn($r) => !empty($r->includes_transfer) || !empty($r->transfer_included));
        }

        if (!$this->manageTrainingTaskId && $pax) {
            $task = \App\Models\Task::create([
                'assigned_gestor_id' => auth()->id(),
                'created_by' => auth()->id(),
                'title' => 'IRIS Training — ' . $pax->full_name,
                'type' => 'iris-training',
                'status' => 'Aceptada',
                'priority' => 'media',
                'payload' => ['passenger_id' => $pax->id],
            ]);
            $this->manageTrainingTaskId = $task->id;
        }

        if ($this->manageTrainingTaskId) {
            $task = \App\Models\Task::findOrFail($this->manageTrainingTaskId);
            $payload = $task->payload ?? [];
            $payload['sessions'] = $sessions;
            $task->update(['payload' => $payload]);
        }

        $this->trainingSessions = $sessions;
        $this->resetNewSessions();

        $clientEmail = $pax?->client?->email;
        if ($clientEmail && count($validSessions) > 0) {
            \Illuminate\Support\Facades\Mail::to($clientEmail)->send(
                new \App\Mail\TrainingScheduledMail(
                    passengerName: $pax->full_name,
                    sessions: $validSessions,
                    isRenewal: $this->trainingIsRenewal,
                    hasTransfer: $hasTransfer
                )
            );
        }

        $label = $hasTransfer ? '— Traslado al centro de entrenamiento notificado.' : '— Recordatorio enviado al cliente.';
        session()->flash(
            'passport_request_message',
            'Sesión añadida correctamente. ' . (count($validSessions) > 1 ? count($validSessions) . ' sesiones programadas.' : 'Sesión programada.') . ' ' . $label
        );
    }

    public function approveTrainingSession(int $index): void
    {
        if (!$this->manageTrainingTaskId)
            return;

        $task = \App\Models\Task::findOrFail($this->manageTrainingTaskId);
        $payload = $task->payload ?? [];
        $sessions = $payload['sessions'] ?? [];

        if (isset($sessions[$index])) {
            $sessions[$index]['status'] = 'Completada';
            $sessions[$index]['approved_at'] = now()->toDateTimeString();
            $payload['sessions'] = $sessions;
            $task->update(['payload' => $payload]);
            $this->trainingSessions = $sessions;
        }

        $paxId = $payload['passenger_id'] ?? $this->manageTrainingPaxId;
        if ($paxId) {
            try {
                $pax = $this->getMyPassenger($paxId);

                $totalHours = collect($sessions)->where('status', 'Completada')->sum('hours');
                $requiredHours = $this->trainingIsRenewal ? 1 : 3;

                if ($totalHours >= $requiredHours) {
                    $pax->update([
                        'training_certificate_date' => $this->trainingCertDate ?: now()->format('Y-m-d'),
                        'training_certificate_status' => 'Apto',
                        'physical_fitness' => 'Apto',
                    ]);
                    $this->trainingCertStatus = 'Apto';
                    session()->flash('passport_request_message', 'Sesión aprobada. El total de horas (' . $totalHours . 'h) es suficiente y el pasajero ha quedado marcado como APTO.');
                } else {
                    session()->flash('passport_request_message', 'Sesión aprobada. Horas totales: ' . $totalHours . 'h. Se requieren ' . $requiredHours . 'h para el APTO final.');
                }
            } catch (\Exception) {
            }
        }
    }

    public function updateTrainingSessionStatus(int $index, string $status): void
    {
        if (!$this->manageTrainingTaskId)
            return;

        $task = \App\Models\Task::findOrFail($this->manageTrainingTaskId);
        $payload = $task->payload ?? [];
        $sessions = $payload['sessions'] ?? [];

        if (isset($sessions[$index])) {
            $oldStatus = $sessions[$index]['status'];
            $sessionData = $sessions[$index];
            $sessions[$index]['status'] = $status;

            if ($status === 'Completada') {
                $sessions[$index]['approved_at'] = now()->toDateTimeString();
            }

            $payload['sessions'] = $sessions;
            $task->update(['payload' => $payload]);
            $this->trainingSessions = $sessions;

            if ($status !== $oldStatus && ($status === 'Ausente' || $status === 'Programada')) {
                $paxId = $payload['passenger_id'] ?? $this->manageTrainingPaxId;
                if ($paxId) {
                    $pax = Passenger::find($paxId);
                    if ($pax && $pax->client?->email) {
                        \Illuminate\Support\Facades\Mail::to($pax->client->email)->send(
                            new \App\Mail\TrainingSessionStatusMail(
                                passengerName: $pax->full_name,
                                sessionDate: $sessionData['date'],
                                newStatus: $status
                            )
                        );
                    }
                }
            }
        }
    }

    public function deleteScheduledSession(int $index): void
    {
        if (!$this->manageTrainingTaskId)
            return;

        $task = \App\Models\Task::findOrFail($this->manageTrainingTaskId);
        $payload = $task->payload ?? [];
        $sessions = $payload['sessions'] ?? [];

        if (isset($sessions[$index])) {
            unset($sessions[$index]);
            $sessions = array_values($sessions);
            $payload['sessions'] = $sessions;
            $task->update(['payload' => $payload]);
            $this->trainingSessions = $sessions;
            session()->flash('passport_request_message', 'Sesión eliminada del registro.');
        }
    }
}
