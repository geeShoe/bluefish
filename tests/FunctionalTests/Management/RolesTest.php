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

namespace Geeshoe\BlueFish\Tests\FunctionalTests\Management;

use Geeshoe\BlueFish\Management\Roles;
use Geeshoe\BlueFish\Model\Role;
use Geeshoe\BlueFish\Tests\DBSetupForFuncTests;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class RolesTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests\Management
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class RolesTest extends TestCase
{
    use DBSetupForFuncTests;

    /**
     * @var PreparedStoredProcedures
     */
    public static $storedProcedures;

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$storedProcedures = self::getDbSetup();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        self::tearDownDB();
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAddRole(): void
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->role = 'FuncTest';

        $roles = new Roles(self::$storedProcedures);

        $roles->addRole($role);

        $result = self::$storedProcedures->executePreparedFetchAsClass(
            'SELECT role, UuidFromBin(id) as id FROM BF_Roles WHERE role = :name',
            ['name' => $role->role],
            Role::class
        );

        $this->assertSame($role->id, $result->id);
    }
}
