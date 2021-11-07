<?php

namespace Tests\Concerns;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SimpleMessage;
use PHPUnit\Framework\TestCase;

class MailMessageTester extends SimpleMessage
{
    /**
     * @var MailMessage
     */
    private MailMessage $mailMessage;

    /**
     * @var TestCase
     */
    private TestCase $testSuite;

    /**
     * MailMessageTester constructor.
     *
     * @param  MailMessage  $mailMessage
     * @param               $testSuite
     */
    public function __construct(MailMessage $mailMessage, TestCase $testSuite)
    {
        $this->mailMessage = $mailMessage;
        $this->testSuite = $testSuite;
    }

    public function assertHasCorrectCopy()
    {
        $this->testSuite->assertEquals($this->toArray(), $this->mailMessage->toArray());
    }
}
