<?php
/**
 * Copyright 2019 Jesse Rushlow - Geeshoe Development
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Geeshoe\BlueFish\Tests\UnitTests\Management;

use Geeshoe\BlueFish\Management\Roles;
use Geeshoe\BlueFish\Model\Role;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class RolesTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests\Management
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class RolesTest extends TestCase
{
    /**
     * @var MockObject|PreparedStoredProcedures
     */
    public $mockPrep;

    /**
     * {@inheritDoc}
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->mockPrep = $this->getMockBuilder(PreparedStoredProcedures::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \ReflectionException
     */
    public function testAddRole(): void
    {
        $role = new Role();

        $role->id = Uuid::uuid4()->toString();
        $role->role = 'UnitTest';

        $mockPrepStrdProc = $this->getMockBuilder(PreparedStoredProcedures::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockPrepStrdProc->expects($this->once())
            ->method('executePreparedStoredProcedure')
            ->with('add_role', ['id' => $role->id, 'role' => $role->role]);

        $roles = new Roles($mockPrepStrdProc);

        $roles->addRole($role);
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testGetRoleByNameCallsMySQLProcedure(): void
    {
        $this->mockPrep->expects($this->once())
            ->method('executePreparedFetchAsClass')
            ->with(
                'CALL get_role_by_name(:role);',
                ['role' => 'UnitTest'],
                Role::class
            )
            ->willReturn(new Role());

        $roles = new Roles($this->mockPrep);
        $roles->getRoleByName('UnitTest');
    }
}
