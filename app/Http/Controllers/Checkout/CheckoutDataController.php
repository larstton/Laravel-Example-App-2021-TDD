<?php

namespace App\Http\Controllers\Checkout;

use App\Actions\Checkout\FetchCheckoutDataAction;
use App\Http\Controllers\Controller;

class CheckoutDataController extends Controller
{
    public function __invoke(FetchCheckoutDataAction $fetchCheckoutDataAction)
    {
        $data = $fetchCheckoutDataAction->execute(current_user(), current_team());

        return $this->success(compact('data'));
    }
}
