<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ActivationEMail extends Mailable {
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user) {
        $this->user = $user;
    }
    public function build() {
        return $this->subject('Activation email')
                    ->view('emails.activation')->with(['user' => $this->user]);;
    }
}
