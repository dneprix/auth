<?php

namespace App\Console\Commands;

use App\Models\Email;

/**
 * Class EmailQueueSendCommand
 * @package App\Console\Commands
 */
class EmailQueueSendCommand extends Command
{
    protected $signature = 'email-queue:send {limit=100} {sleep=1} ';
    protected $description = 'Send emails from queue';

    /**
     *  Handle send emails from email queue
     */
    public function handle()
    {
        $limit = $this->argument('limit');
        $sleep = $this->argument('sleep');

        while ($limit) {
            try {
                // Send emails one by one for parallel commands
                $email = Email::sendFromQueue();
                if (!$email) {
                    $this->warn('Email queue is empty');
                    break;
                }
                $this->info("Email {$email->_id}: send status: {$email->status}");
            } catch
            (\Exception $e) {
                $this->error($e->getMessage());
            }
            $limit--;

            // Pause between emails (if configured)
            if ($limit && $sleep) {
                sleep($sleep);
            }
        }
    }
}
