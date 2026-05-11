@extends('layouts.email', ['title' => 'Notificación de Misión - Iris Aerospace'])

@section('content')
    <div class="title">{{ $actionType === 'created' ? 'Nueva Misión Asignada' : 'Misión Actualizada' }}</div>
    <div class="subtitle">Control de Operaciones Gestor</div>

    <p style="color: #888888; line-height: 1.6;">
        Hola **{{ $user->name }}**, el sistema de control central te ha
        {{ $actionType === 'created' ? 'asignado una nueva' : 'actualizado una' }} tarea operativa.
    </p>

    <div class="box">
        <div class="field-label">Título de la Tarea</div>
        <div class="field-value">{{ $task->title }}</div>

        <div class="field-label">Prioridad de Tarea</div>
        <div class="field-value">
            @php
                $priorityClasses = [
                    'baja' => 'status-emerald',
                    'media' => 'status-amber',
                    'alta' => 'status-rose',
                    'urgente' => 'status-rose',
                ];
                $pClass = $priorityClasses[$task->priority] ?? 'status-amber';
            @endphp
            <span class="status-badge {{ $pClass }}">
                {{ strtoupper($task->priority) }}
            </span>
        </div>

        <div class="field-label">Tipo de Tarea</div>
        <div class="field-value" style="font-size: 13px; font-family: 'JetBrains Mono', monospace;">
            {{ strtoupper($task->type) }}</div>
    </div>

    <p style="color: #888888; line-height: 1.6;">
        Por favor, accede a tu terminal de control para revisar los detalles y proceder con las acciones necesarias. La
        integridad de la misión depende de tu gestión oportuna.
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ url('/gestor/tasks') }}" class="cta-button">Revisar Tarea</a>
    </div>
@endsection