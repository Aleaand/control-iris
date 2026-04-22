<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passenger extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'document_number',
        'document_country',
        'name',
        'primarylastname',
        'secondarylastname',
        'birth_date',
        'blood_type',
        'allergies',
        'physical_fitness',
        'iris_passport_number',
        'iris_passport_expiration',
        'training_certificate_date',
        'training_certificate_status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'iris_passport_expiration' => 'date',
            'training_certificate_date' => 'date',
        ];
    }

    // ── Relaciones ───────────────────────────────────────────

    /**
     * El cliente (titular de cuenta) al que pertenece este pasajero.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Las reservas donde vuela este pasajero.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // ── Helpers de Documentación ─────────────────────────────

    /**
     * ¿Tiene un certificado de Training válido? (vigente 10 años)
     */
    public function hasValidTraining(): bool
    {
        if (!$this->training_certificate_date || $this->training_certificate_status !== 'Apto') {
            return false;
        }

        return !$this->training_certificate_date->copy()->addYears(10)->isPast();
    }

    /**
     * ¿Tiene derecho al descuento del 10%? (certificado < 3 años)
     */
    public function hasTrainingDiscount(): bool
    {
        if (!$this->training_certificate_date || $this->training_certificate_status !== 'Apto') {
            return false;
        }

        return !$this->training_certificate_date->copy()->addYears(3)->isPast();
    }

    /**
     * ¿Tiene un Pasaporte Espacial vigente para una fecha específica?
     */
    public function isValidPassportForDate(\Carbon\Carbon $date): bool
    {
        if (!$this->iris_passport_number || !$this->iris_passport_expiration) {
            return false;
        }

        // El pasaporte debe ser válido al menos en la fecha del vuelo.
        // Podríamos añadir una ventana de margen (ej: 6 meses) si fuera necesario, 
        // pero por ahora validamos que no expire antes de ese día.
        return $this->iris_passport_expiration->isAfter($date) || $this->iris_passport_expiration->isSameDay($date);
    }

    /**
     * ¿Tiene un Pasaporte Espacial vigente hoy?
     */
    public function hasValidPassport(): bool
    {
        return $this->isValidPassportForDate(now());
    }

    /**
     * ¿Es mayor de edad? (18 años para seguridad Iris)
     */
    public function isAdult(): bool
    {
        return $this->birth_date && $this->birth_date->age >= 18;
    }

    /**
     * ¿Está listo para volar? (Adulto + Training + Pasaporte)
     */
    public function isFlightReady(): bool
    {
        return $this->isAdult() && $this->hasValidTraining() && $this->hasValidPassport();
    }

    /**
     * Validación condicional: ¿Puede reservar con los servicios indicados?
     * Si le falta Training o Pasaporte, solo puede reservar si los está comprando.
     */
    public function canReserveWith(bool $trainingIncluded, bool $passportIncluded): array
    {
        $errors = [];

        if (!$this->hasValidTraining() && !$trainingIncluded) {
            $errors[] = 'Este pasajero no tiene Certificado Iris Training vigente. Debe incluir "Iris Training" en la reserva.';
        }

        if (!$this->hasValidPassport() && !$passportIncluded) {
            $errors[] = 'Este pasajero no tiene Pasaporte Espacial vigente. Debe incluir "Gestión de Pasaporte" en la reserva.';
        }

        return $errors;
    }

    /**
     * Detector de Ubicuidad: ¿Tiene otra reserva activa para la misma fecha?
     * Retorna bool para usarse en executeGroupSave() (Crítico 1.1).
     */
    public function hasConflictOnDate(Carbon $date, ?int $excludeReservationId = null): bool
    {
        $query = $this->reservations()
            ->whereNotIn('status', ['Cancelada', 'Cancelled'])
            ->where('is_adenda', false)
            ->whereHas('spaceFlight', function ($q) use ($date) {
                $q->whereDate('departure_date', $date->toDateString());
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->exists();
    }

    /**
     * Nombre completo del pasajero.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->primarylastname} {$this->secondarylastname}");
    }

}
