<?php
/*
 * Copyright (c) Enalean, 2022-Present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace Tuleap\MediawikiStandalone\Permissions;

use Tuleap\Mediawiki\ForgeUserGroupPermission\MediawikiAdminAllProjects;
use Tuleap\Project\CheckProjectAccess;
use Tuleap\User\ForgePermissionsRetriever;

final class UserPermissionsBuilder implements IBuildUserPermissions
{
    public function __construct(
        private ForgePermissionsRetriever $forge_permissions_retriever,
        private CheckProjectAccess $check_project_access,
        private ProjectPermissionsRetriever $permissions_retriever,
    ) {
    }

    public function getPermissions(\PFUser $user, \Project $project): UserPermissions
    {
        try {
            $this->check_project_access->checkUserCanAccessProject($user, $project);
        } catch (\Project_AccessException) {
            return UserPermissions::noAccess();
        }

        $project_permissions = $this->permissions_retriever->getProjectPermissions($project);

        if (
            $user->isSuperUser() ||
            $this->forge_permissions_retriever->doesUserHavePermission($user, new MediawikiAdminAllProjects()) ||
            $user->isAdmin((int) $project->getID())
        ) {
            return UserPermissions::fullAccess();
        }

        foreach ($project_permissions->writers as $writers_ugroup_id) {
            if ($user->isMemberOfUGroup($writers_ugroup_id, (int) $project->getID())) {
                return UserPermissions::writer();
            }
        }

        foreach ($project_permissions->readers as $readers_ugroup_id) {
            if ($user->isMemberOfUGroup($readers_ugroup_id, (int) $project->getID())) {
                return UserPermissions::reader();
            }
        }

        return UserPermissions::noAccess();
    }
}
