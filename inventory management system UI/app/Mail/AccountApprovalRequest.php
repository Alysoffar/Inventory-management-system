<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AccountApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Account Registration - Approval Required')
                    ->view('emails.account-approval-request')
                    ->with([
                        'user' => $this->user,
                        'approvalUrl' => route('admin.approve-user-get', $this->user->id)
                    ]);
    }
}
