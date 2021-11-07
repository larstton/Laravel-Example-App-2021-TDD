<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Email()
 * @method static static Slack()
 * @method static static Webhook()
 * @method static static Pushover()
 * @method static static Telegram()
 * @method static static Sms()
 * @method static static Phonecall()
 * @method static static MSTeams()
 * @method static static Integromat()
 */
final class RecipientMediaType extends Enum
{
    const Email = 'email';
    const Slack = 'slack';
    const Webhook = 'webhook';
    const Pushover = 'pushover';
    const Telegram = 'telegram';
    const Sms = 'sms';
    const Phonecall = 'phonecall';
    const MSTeams = 'msteams';
    const Integromat = 'integromat';
}
