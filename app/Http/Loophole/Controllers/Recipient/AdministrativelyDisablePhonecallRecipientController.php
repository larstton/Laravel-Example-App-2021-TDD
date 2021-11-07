<?php


namespace App\Http\Loophole\Controllers\Recipient;


use App\Actions\Recipient\AdministrativelyDisablePhoneCallRecipientsBySendToAction;
use App\Http\Controllers\Controller;

class AdministrativelyDisablePhonecallRecipientController extends Controller
{
    public function __invoke(
        $sendto,
        AdministrativelyDisablePhoneCallRecipientsBySendToAction $administrativelyDisablePhoneCallRecipientsBySendToAction
    ) {

        $administrativelyDisablePhoneCallRecipientsBySendToAction->execute($sendto);

        return $this->accepted();
    }
}
