<?php

namespace App\Http\Controllers\SubUnit;

use App\Actions\SubUnit\CreateSubUnitAction;
use App\Actions\SubUnit\DeleteSubUnitAction;
use App\Actions\SubUnit\UpdateSubUnitAction;
use App\Data\SubUnit\SubUnitData;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubUnit\CreateSubUnitRequest;
use App\Http\Requests\SubUnit\UpdateSubUnitRequest;
use App\Http\Resources\SubUnitResource;
use App\Models\SubUnit;

class SubUnitController extends Controller
{
    public function index()
    {
        $this->authorize(SubUnit::class);

        return SubUnitResource::collection(
            SubUnit::when(
                filled(optional($user = current_user())->sub_unit_id),
                fn ($query) => $query->where('id', $user->sub_unit_id)
            )->get()
        );
    }

    public function store(CreateSubUnitRequest $request, CreateSubUnitAction $createSubUnitAction)
    {
        $this->authorize(SubUnit::class);

        $subUnit = $createSubUnitAction->execute(SubUnitData::fromStoreRequest($request));

        return SubUnitResource::make($subUnit);
    }

    public function show(SubUnit $subUnit)
    {
        $this->authorize($subUnit);

        return SubUnitResource::make($subUnit);
    }

    public function update(UpdateSubUnitRequest $request, SubUnit $subUnit, UpdateSubUnitAction $updateSubUnitAction)
    {
        $this->authorize($subUnit);

        $subUnit = $updateSubUnitAction->execute($subUnit, SubUnitData::fromUpdateRequest($request));

        return SubUnitResource::make($subUnit);
    }

    public function destroy(SubUnit $subUnit, DeleteSubUnitAction $deleteSubUnitAction)
    {
        $this->authorize($subUnit);

        $deleteSubUnitAction->execute($subUnit);

        return $this->noContent();
    }
}
