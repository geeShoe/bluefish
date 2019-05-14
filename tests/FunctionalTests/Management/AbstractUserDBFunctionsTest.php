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
 * Date: 3/29/19 - 3:58 PM
 */

namespace Geeshoe\BlueFish\Tests\FunctionalTests\Management;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Management\AbstractUserDBFunctions;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\BlueFish\Tests\Utilities\DatabaseInterface;
use Geeshoe\BlueFish\Tests\Utilities\TestDatabase;
use Geeshoe\BlueFish\Tests\Utilities\TestObjects;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class AbstractUserDBFunctionsTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests\Management
 */
class AbstractUserDBFunctionsTest extends TestCase implements DatabaseInterface
{
    use TestDatabase,
        TestObjects;

    /**
     * @var Object
     */
    public $class;

    /**
     * @var PreparedStoredProcedures
     */
    protected static $preparedStatement;

    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @var array
     */
    public $userObjectArray;


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
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->class = $this->extendAbstractClass();
        self::$pdo->exec('DELETE FROM BF_Users;DELETE FROM BF_Status;DELETE FROM BF_Roles;');
    }

    /**
     * @return AbstractUserDBFunctions
     */
    public function extendAbstractClass(): AbstractUserDBFunctions
    {
        return new class(self::$preparedStatement) extends AbstractUserDBFunctions
        {
            public function userId(string $id): User
            {
                return $this->getUserByID($id);
            }

            public function userName(string $name): User
            {
                return $this->getUserByUsername($name);
            }
        };
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function getUserObject(): void
    {
        $this->userObjectArray = self::getUserRoleStatusArray();
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function insertUser(): void
    {
        $this->getUserObject();

        $role = $this->userObjectArray['role'];
        $status = $this->userObjectArray['status'];
        $user = $this->userObjectArray['user'];

        self::$preparedStatement->executePreparedInsertQuery(
            'BF_Roles',
            ['id' => $role->id->getBytes(), 'role' => $role->role]
        );

        self::$preparedStatement->executePreparedInsertQuery(
            'BF_Status',
            ['id' => $status->id->getBytes(), 'status' => $status->status]
        );

        self::$preparedStatement->executePreparedInsertQuery(
            'BF_Users',
            [
                'id' => $user->id->getBytes(),
                'username' => $user->username,
                'displayName' => $user->displayName,
                'password' => password_hash('1234', PASSWORD_DEFAULT),
                'role' => $role->id->getBytes(),
                'status' => $status->id->getBytes()
            ]
        );
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetUserByIdReturnsUserObject(): void
    {
        $this->insertUser();

        $result = $this->class->userId($this->userObjectArray['user']->id->toString());
        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($this->userObjectArray['user']->id->toString(), $result->id);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testExceptionIsThrownWithInvalidUUID(): void
    {
        $this->expectException(BlueFishException::class);
        $this->class->userId(Uuid::uuid4()->toString());
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetUserByNameReturnsUserObject(): void
    {
        $this->insertUser();

        $result = $this->class->userName($this->userObjectArray['user']->username);
        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($this->userObjectArray['user']->id->toString(), $result->id);
    }

    public function testGetUserByNameThrowsExceptionWithInvalidUsername(): void
    {
        $this->expectException(BlueFishException::class);
        $this->class->userName('someUserName');
    }
}
