<?php

namespace App\Actions\Checkout;

use App\Models\Team;
use App\Models\User;
use App\Support\CheckoutService;

class FetchCheckoutDataAction
{
    private CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function execute(User $user, Team $team): array
    {
        $this->checkoutService->getTeam($team);
        $response = $this->checkoutService->getJsonResponse();

        unset($response['success']);
        $response['formUrl'] = $this->checkoutService->makeCardUpdateUrl($user);
        $response['stripeKey'] = config('services.stripe.key');
        $response['handover'] = [
            'baseUrl' => $this->checkoutService->makeLoginUrl($user),
            'actions' => ['upgrade', 'change-billing-address', 'list-invoices', 'verify'],
        ];

        return $response;
    }
}
