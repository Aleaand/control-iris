<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $actionType; // 'created' or 'modified'

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $actionType = 'created')
    {
        $this->task = $task;
        $this->actionType = $actionType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        \Illuminate\Support\Facades\Log::debug("Construyendo MailMessage para tarea #{$this->task->id} destinada a {$notifiable->email}");

        $subject = $this->actionType === 'created'
            ? 'Iris Aerospace - Nueva Tarea Asignada'
            : 'Iris Aerospace - Tarea Actualizada';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.task-notification', [
                'task' => $this->task,
                'actionType' => $this->actionType,
                'user' => $notifiable,
            ]);
    }
}
