<?php

namespace App\Actions\User;

use App\Actions\Team\DeleteTeamAction;
use App\Enums\TeamMemberRole;
use App\Enums\TeamStatus;
use App\Models\User;
use App\Support\CheckoutService;

class DeleteUserAction
{
    private DeleteTeamAction $deleteTeamAction;
    private CheckoutService $checkoutService;

    public function __construct(DeleteTeamAction $deleteTeamAction, CheckoutService $checkoutService)
    {
        $this->deleteTeamAction = $deleteTeamAction;
        $this->checkoutService = $checkoutService;
    }

    public function execute(User $user)
    {
        // todo stats recording and email
        $team = $user->team;

        $teamAdminCount = $team->teamMembers()
            ->notDeleted()
            ->role(TeamMemberRole::Admin())
            ->count();

        if (! $user->isTeamAdmin() || $teamAdminCount > 1) {

            $user->update([
                'email'       => $user->id.'@DELETED',
                'team_status' => TeamStatus::Deleted(),
                'role'        => TeamMemberRole::Deleted(),
                'nickname'    => null,
                'notes'       => null,
            ]);

            $user->settings()->delete();

        } else {

            if ($this->checkoutService->teamHasUnpaidInvoices($team)) {
                fail_validation('unpaid_invoices', trans('base.unpaid_invoices', [], $user->lang));
            }

            $this->deleteTeamAction->execute($team);
        }
    }
}
