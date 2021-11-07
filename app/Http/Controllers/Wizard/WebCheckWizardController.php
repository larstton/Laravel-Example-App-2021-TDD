<?php

/** @noinspection PhpRedundantCatchClauseInspection */

namespace App\Http\Controllers\Wizard;

use App\Actions\Wizard\CreateWebCheckFromWizardAction;
use App\Data\Wizard\CreateWebCheckWizardData;
use App\Exceptions\CheckPreflightException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wizard\CreateWebCheckWizardRequest;
use App\Http\Resources\WebCheckResource;

class WebCheckWizardController extends Controller
{
    public function __invoke(
        CreateWebCheckWizardRequest $request,
        CreateWebCheckFromWizardAction $createWebCheckFromWizardAction
    ) {
        try {
            $webCheck = $createWebCheckFromWizardAction->execute(
                current_user(),
                CreateWebCheckWizardData::fromRequest($request)
            );
        } catch (CheckPreflightException $e) {
            return $this->json([
                'data' => [
                    'error'   => $e->getMessage(),
                    'success' => false,
                    'console' => $e->getConsole(),
                ],
            ], 412);
        }

        return WebCheckResource::make($webCheck);
    }
}
