<?php


namespace App\Support\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlainTextMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
    }
}
