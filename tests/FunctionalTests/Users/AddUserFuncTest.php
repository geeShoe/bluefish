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

/**
 * User: Jesse Rushlow - Geeshoe Development
 * Date: 1/11/19 - 5:01 AM
 */

namespace Geeshoe\BlueFish\Tests\FunctionalTests\Users;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Management\AddUser;
use Geeshoe\BlueFish\Model\Role;
use Geeshoe\BlueFish\Model\Status;
use Geeshoe\BlueFish\Model\UserProspect;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\BlueFish\Tests\Utilities\TestDatabase;
use Geeshoe\BlueFish\Tests\Utilities\TestObjects;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;

/**
 * Class AddUserFuncTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests
 */
class AddUserFuncTest extends TestCase
{
    use TestDatabase,
        TestObjects;

    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @var PreparedStoredProcedures
     */
    protected static $preparedStatement;

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$pdo = self::getConnection();
        self::$pdo->exec('USE ' . getenv('GSD_BFTD_DATABASE'));
        self::$preparedStatement = new PreparedStoredProcedures(self::$pdo);
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        self::$pdo->exec('DELETE FROM BF_Users;DELETE FROM BF_Status;DELETE FROM BF_Roles;');
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec('DELETE FROM BF_Users;DELETE FROM BF_Status;DELETE FROM BF_Roles;');
    }

    /**
     * @param Status $status
     * @param Role   $role
     * @inheritDoc
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     */
    public function addRoleStatusToDB(Status $status, Role $role): void
    {
        self::$preparedStatement->executePreparedInsertQuery(
            'BF_Roles',
            ['id' => $role->id->getBytes(), 'role' => $role->role]
        );

        self::$preparedStatement->executePreparedInsertQuery(
            'BF_Status',
            ['id' => $status->id->getBytes(), 'status' => $status->status]
        );
    }

    /**
     * @throws BlueFishException
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testCreateUserAccountAddsNewUser(): void
    {
        $userObject = self::getUserRoleStatusArray();

        $this->addRoleStatusToDB($userObject['status'], $userObject['role']);

        $newUser = new UserProspect();
        $newUser->username = $userObject['user']->username;
        $newUser->password = 'pass';
        $newUser->passwordVerify = 'pass';
        $newUser->displayName = $userObject['user']->displayName;
        $newUser->role = $userObject['role']->id->toString();
        $newUser->status = $userObject['status']->id->toString();

        $addUser = new AddUser(self::$preparedStatement);
        $user = $addUser->createUserAccount($newUser);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @throws BlueFishException
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testCreateUserAccountDoesNotAddDuplicateUsers(): void
    {
        $userObject = self::getUserRoleStatusArray();

        $this->addRoleStatusToDB($userObject['status'], $userObject['role']);

        $newUser = new UserProspect();
        $newUser->username = $userObject['user']->username;
        $newUser->password = 'pass';
        $newUser->passwordVerify = 'pass';
        $newUser->displayName = $userObject['user']->displayName;
        $newUser->role = $userObject['role']->id->toString();
        $newUser->status = $userObject['status']->id->toString();

        $addUser = new AddUser(self::$preparedStatement);
        $addUser->createUserAccount($newUser);

        $this->expectException(BlueFishException::class);
        $addUser->createUserAccount($newUser);
    }
}
