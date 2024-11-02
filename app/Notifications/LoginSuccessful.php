<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class LoginSuccessful extends Notification
{
    use Queueable;

    protected $student;
    protected $loginDate;
    protected $device;

    public function __construct($student, $loginDate, $device)
    {
        $this->student = $student;
        $this->loginDate = $loginDate;
        $this->device = $device;
    }

    public function via($notifiable)
    {
        // You can return an empty array since we're sending a raw email
        return [];
    }

    public function toMail($notifiable)
    {
        // This method will not be called anymore, so you can omit it.
    }

    public function sendLoginNotification()
    {
        // Create a plain text email
        Mail::raw(
            $this->buildMessage(),
            function ($message) {
                $message->to($this->student->email) // Use the student's email address
                    ->from('noreply@gmail.com', 'Qma Email Security') // Send from the noreply address
                    ->subject('Login Notification');
            }
        );
    }

    protected function buildMessage()
    {
        return "Hello " . $this->student->name . ",\n\n" .
               "You have successfully logged into your account on " . $this->loginDate . ".\n" .
               "Device: " . $this->device . "\n\n" .
               "If this was not you, please report it to the registrar's office and take the necessary actions.\n\n" .
               "Thank you for using our application!";
    }
}
