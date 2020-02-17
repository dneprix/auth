<?php

namespace App\Services;

use App\Mail\ActivationEMail;
use App\Models\Email;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use MongoDB\Operation\FindOneAndUpdate;

class EmailService
{
    /**
     * Activation email for user
     *
     * @param User $user
     */
    public function activation(User $user)
    {
        // Add email to queue
        $this->addToQueue($user->id, Email::TYPE_ACTIVATION);
    }

    /**
     * Add email to queue
     *
     * @param string $user_id
     * @param string $type
     */
    private function addToQueue(string $user_id, string $type)
    {
        // Create new email
        $email = new Email();

        // Set user_id for email
        $email->user_id = $user_id;

        // Set email status new
        $email->status = Email::STATUS_NEW;

        // Set email type
        $email->type = $type;

        // Save email to db
        $email->save();
    }

    /**
     * Get one email from queue and send
     *
     * @throws \Exception
     */
    public function sendFromQueue()
    {
        // Find one new email and update status to sending
        $result = \DB::getCollection(Email::COLLECTION)
            ->findOneAndUpdate(
                ['status' => Email::STATUS_NEW],
                ['$set' => ['status' => Email::STATUS_SENDING]],
                [
                    'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
                    'sort' => ['updated_at' => 1],
                ]
            );

        // Check result
        if (!$result) {
            // No email for sending
            return;
        }

        // Get email by id
        $email = Email::find($result->_id);

        // Process sending email
        try {
            // Get user by id
            $user = User::find($email->user_id);
            if (!$user) {
                throw new \Exception('Invalid email user');
            }

            // Send email by type
            switch ($email->type) {
                case Email::TYPE_ACTIVATION:
                    Mail::to($user->email)->send(new ActivationEmail($user));
                    break;
                default:
                    throw new \Exception('Invalid email type');
            }
        } catch (\Exception $e) {
            // Save email status fail
            $email->status = Email::STATUS_FAIL;
            $email->save();
            throw new \Exception("Email {$email->_id}: send status: {$email->status}: " . $e->getMessage());
        }

        // Save email status success
        $email->status = Email::STATUS_SUCCESS;
        $email->save();

        return $email;
    }

}
