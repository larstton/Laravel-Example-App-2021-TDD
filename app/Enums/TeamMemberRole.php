<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static Member()
 * @method static static Guest()
 * @method static static Deleted()
 */
final class TeamMemberRole extends Enum
{
    const Admin = 'ROLE_TEAM_ADMIN';
    const Member = 'ROLE_TEAM_MEMBER';
    const Guest = 'ROLE_READ_ONLY';
    const Deleted = 'DELETED';
}
