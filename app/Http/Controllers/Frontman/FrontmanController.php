<?php

namespace App\Http\Controllers\Frontman;

use App\Actions\Frontman\CreateFrontmanAction;
use App\Actions\Frontman\DeleteFrontmanAction;
use App\Actions\Frontman\UpdateFrontmanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontman\CreateFrontmanRequest;
use App\Http\Requests\Frontman\UpdateFrontmanRequest;
use App\Http\Resources\Frontman\FrontmanResource;
use App\Models\Frontman;

class FrontmanController extends Controller
{
    public function index()
    {
        $this->authorize(Frontman::class);

        return FrontmanResource::collection(
            Frontman::private()->with('filteredHosts')->latest()->get()
        );
    }

    public function store(CreateFrontmanRequest $request, CreateFrontmanAction $createFrontmanAction)
    {
        $this->authorize(Frontman::class);

        $frontman = $createFrontmanAction->execute($this->user(), $request->location);

        return FrontmanResource::make($frontman->load('filteredHosts'));
    }

    public function show(Frontman $frontman)
    {
        $this->authorize($frontman);

        return FrontmanResource::make($frontman->load('filteredHosts'));
    }

    public function update(
        UpdateFrontmanRequest $request,
        Frontman $frontman,
        UpdateFrontmanAction $updateFrontmanAction
    ) {
        $this->authorize($frontman);

        $frontman = $updateFrontmanAction->execute($frontman, $request->location);

        return FrontmanResource::make($frontman->load('filteredHosts'));
    }

    public function destroy(Frontman $frontman, DeleteFrontmanAction $deleteFrontmanAction)
    {
        $this->authorize($frontman);

        $deleteFrontmanAction->execute($frontman);

        return $this->noContent();
    }
}
