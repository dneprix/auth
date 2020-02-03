<?php

namespace App\Models;

use Illuminate\Support\Facades\Mail;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\Operation\FindOneAndUpdate;
use App\Mail\ActivationEmail;

/**
 * Class Email
 * @package App\Models
 */
final class Email extends Model
{
    const COLLECTION = 'emails';

    protected $connection = 'mongodb';
    protected $collection = self::COLLECTION;
    protected $primaryKey = '_id';
    protected $fillable = ['user_id', 'type', 'status'];

    const STATUS_NEW = 'new';
    const STATUS_SENDING = 'sending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    const TYPE_ACTIVATION = 'activation';

    /**
     * Activation email for user
     *
     * @param User $user
     */
    public static function activation(User $user)
    {
        // Add email to queue
        self::addToQueue($user->id, self::TYPE_ACTIVATION);
    }

    /**
     * Add email to queue
     *
     * @param string $user_id
     * @param string $type
     */
    private static function addToQueue(string $user_id, string $type)
    {
        // Create new email
        $email = new self();

        // Set user_id for email
        $email->user_id = $user_id;

        // Set email status new
        $email->status = self::STATUS_NEW;

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
    public static function sendFromQueue()
    {
        // Find one new email and update status to sending
        $result = \DB::getCollection(self::COLLECTION)
            ->findOneAndUpdate(
                ['status' => self::STATUS_NEW],
                ['$set' => ['status' => self::STATUS_SENDING]],
                [
                    'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
                    'sort' => ['updated_at' => 1 ],
                ]
            );

        // Check result
        if (!$result) {
            // No email for sending
            return;
        }

        // Get email by id
        $email = self::find($result->_id);

        // Process sending email
        try {
            // Get user by id
            $user = User::find($email->user_id);
            if (!$user) {
                throw new \Exception('Invalid email user');
            }

            // Send email by type
            switch ($email->type) {
                case self::TYPE_ACTIVATION:
                    Mail::to($user->email)->send(new ActivationEmail($user));
                    break;
                default:
                    throw new \Exception('Invalid email type');
            }
        }
        catch (\Exception $e) {
            // Save email status fail
            $email->status = self::STATUS_FAIL;
            $email->save();
            throw new \Exception("Email {$email->_id}: send status: {$email->status}: ". $e->getMessage());
        }

        // Save email status success
        $email->status = self::STATUS_SUCCESS;
        $email->save();

        return $email;
    }
}

