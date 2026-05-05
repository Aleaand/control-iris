<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmailNotification());
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'primarylastname',
        'secondarylastname',
        'phone',
        'birth_date',
        'assigned_manager_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    /**
     * ¿Es mayor de edad? (18 años)
     */
    public function isAdult(): bool
    {
        return $this->birth_date && $this->birth_date->age >= 18;
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }

    public function clients()
    {
        return $this->hasMany(User::class, 'assigned_manager_id');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function passports()
    {
        return $this->hasMany(Passport::class);
    }

    public function medicalCertificates()
    {
        return $this->hasMany(MedicalCertificate::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function refundRequests()
    {
        return $this->hasMany(RefundRequest::class, 'gestor_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_gestor_id');
    }

    public function contactLogs()
    {
        return $this->hasMany(ContactLog::class, 'gestor_id');
    }

    public function paymentLinks()
    {
        return $this->hasMany(PaymentLink::class, 'created_by');
    }

    public function isGestor(): bool
    {
        return $this->role === 'gestor';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
