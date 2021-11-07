<?php

namespace Database\Factories;

use App\Data\Recipient\RecipientData;
use App\Enums\RecipientMediaType;
use Illuminate\Foundation\Testing\WithFaker;

class RecipientDataFactory
{
    use WithFaker;

    public static function make(array $params = []): RecipientData
    {
        $faker = (new self)->makeFaker();

        return new RecipientData(array_merge([
            'mediatype'        => RecipientMediaType::Email(),
            'sendto'           => $faker->email,
            'option1'          => null,
            'description'      => null,
            'comments'         => true,
            'warnings'         => true,
            'eventUuids'       => true,
            'alerts'           => true,
            'reminders'        => true,
            'recoveries'       => true,
            'active'           => true,
            'dailySummary'     => true,
            'dailyReports'     => true,
            'weeklyReports'    => true,
            'monthlyReports'   => true,
            'reminderDelay'    => 14400,
            'maximumReminders' => 6,
            'rules'            => null,
            'extraData'        => [],
            'verified'         => true,
        ], $params));
    }
}
