<?php
/**
 * Copyright (c) Enalean, 2012-Present. All Rights Reserved.
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
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tuleap\ForgeConfigSandbox;
use Tuleap\GlobalResponseMock;

//phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class PermissionsManagerSavePermissionsPlatformForAnonymousProjectPublicTest extends \Tuleap\Test\PHPUnit\TestCase
{
    use MockeryPHPUnitIntegration;
    use ForgeConfigSandbox;
    use GlobalResponseMock;

    protected $permissions_manager;
    protected $project;
    protected $permission_type;
    protected $object_id;
    protected $permissions_dao;
    protected $project_id;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project_id          = 404;
        $this->project             = \Mockery::spy(\Project::class)->shouldReceive('getId')->andReturns($this->project_id)->getMock();
        $this->permissions_dao     = \Mockery::spy(\PermissionsDao::class);
        $this->permission_type     = 'FOO';
        $this->object_id           = 'BAR';
        $this->permissions_manager = new PermissionsManager($this->permissions_dao);
        $this->permissions_dao->shouldReceive('clearPermission')->andReturns(true);
        ForgeConfig::set(ForgeAccess::CONFIG, ForgeAccess::ANONYMOUS);
        $this->project->shouldReceive('isPublic')->andReturns(true);
    }

    protected function expectPermissionsOnce($ugroup): void
    {
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, $ugroup)
            ->once()
            ->andReturns(true);
    }

    protected function savePermissions($ugroups): void
    {
        $this->permissions_manager->savePermissions($this->project, $this->object_id, $this->permission_type, $ugroups);
    }

    public function testItSavesAnonymousSelectedAnonymous(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::ANONYMOUS);

        $this->savePermissions([ProjectUGroup::ANONYMOUS]);
    }

    public function testItSavesRegisteredWhenSelectedAuthenticated(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::REGISTERED);

        $this->savePermissions([ProjectUGroup::AUTHENTICATED]);
    }

    public function testItSavesRegisteredWhenSelectedRegistered(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::REGISTERED);

        $this->savePermissions([ProjectUGroup::REGISTERED]);
    }

    public function testItSavesProjectMembersWhenSelectedProjectMembers(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::PROJECT_MEMBERS);

        $this->savePermissions([ProjectUGroup::PROJECT_MEMBERS]);
    }

    public function testItSavesOnlyAnonymousWhenPresentWithOtherProjectMembersProjectAdminsAndStaticGroup(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::ANONYMOUS);

        $this->savePermissions([ProjectUGroup::ANONYMOUS, ProjectUGroup::PROJECT_ADMIN, 104]);
    }

    public function testItSavesOnlyRegisteredWhenPresentWithOtherProjectMembersProjectAdminsAndStaticGroup(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::REGISTERED);

        $this->savePermissions([ProjectUGroup::REGISTERED, ProjectUGroup::PROJECT_ADMIN, 104]);
    }

    public function testItSavesOnlyRegisteredWhenPresentWithAuthenticatedProjectAdminsAndStaticGroup(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::REGISTERED);

        $this->savePermissions([ProjectUGroup::AUTHENTICATED, ProjectUGroup::PROJECT_ADMIN, 104]);
    }

    public function testItSavesMembersAndStaticWhenPresentWithMembersProjectAdminsAndStaticGroup(): void
    {
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, ProjectUGroup::PROJECT_MEMBERS)
            ->once()
            ->andReturns(true);
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, 104)
            ->once()
            ->andReturns(true);

        $this->savePermissions([ProjectUGroup::PROJECT_MEMBERS, ProjectUGroup::PROJECT_ADMIN, 104]);
    }

    public function testItSavesAdminsAndStaticWhenPresentWithProjectAdminsAndStaticGroup(): void
    {
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, ProjectUGroup::PROJECT_ADMIN)
            ->once()
            ->andReturns(true);
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, 104)
            ->once()
            ->andReturns(true);

        $this->savePermissions([ProjectUGroup::PROJECT_ADMIN, 104]);
    }

    public function testItSavesSVNAdminWikiAdminAndStatic(): void
    {
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, ProjectUGroup::SVN_ADMIN)
            ->once()
            ->andReturns(true)
            ->atLeast()->once();
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, ProjectUGroup::WIKI_ADMIN)
            ->once()
            ->andReturns(true)
            ->atLeast()->once();
        $this->permissions_dao->shouldReceive('addPermission')
            ->with($this->permission_type, $this->object_id, 104)
            ->once()
            ->andReturns(true)
            ->atLeast()->once();

        $this->savePermissions([ProjectUGroup::SVN_ADMIN, ProjectUGroup::WIKI_ADMIN, 104]);
    }

    public function testItSavesProjectMembersWhenSVNAdminWikiAdminAndProjectMembers(): void
    {
        $this->expectPermissionsOnce(ProjectUGroup::PROJECT_MEMBERS);

        $this->savePermissions([ProjectUGroup::SVN_ADMIN, ProjectUGroup::WIKI_ADMIN, ProjectUGroup::PROJECT_MEMBERS]);
    }
}
