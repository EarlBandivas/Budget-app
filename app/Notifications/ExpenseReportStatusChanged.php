<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ExpenseReportStatusChanged extends Notification
{
    use Queueable;

    public $status;
    public $reportId;

    public function __construct($status, $reportId)
    {
        $this->status = $status;
        $this->reportId = $reportId;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statusText = ucfirst($this->status);
        
        return (new MailMessage)
            ->subject("Expense Report {$statusText}")
            ->line("Your expense report has been {$this->status}.")
            ->line("Report ID: {$this->reportId}")
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Your expense report #{$this->reportId} has been {$this->status}.",
            'status' => $this->status,
            'report_id' => $this->reportId
        ];
    }
}