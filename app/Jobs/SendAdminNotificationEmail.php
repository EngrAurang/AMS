<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationEmail;

class SendAdminNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $startDate;
    protected $endDate;
    protected $numberOfDays;
    protected $recipient;

    /**
     * Create a new job instance.
     *
     * @param string $name
     * @param string $startDate
     * @param string $endDate
     * @param int $numberOfDays
     * @param string $recipient
     */
    public function __construct($name, $startDate, $endDate, $numberOfDays, $recipient)
    {
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->numberOfDays = $numberOfDays;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new AdminNotificationEmail($this->name, $this->startDate, $this->endDate, $this->numberOfDays);

        Mail::to($this->recipient)->send($email);
    }
}