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
use Geeshoe\BlueFish\Tests\DBSetupForFuncTests;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class AbstractUserDBFunctionsTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests\Management
 */
class AbstractUserDBFunctionsTest extends TestCase
{
    use DBSetupForFuncTests;

    /**
     * @var Object
     */
    public $class;

    /**
     * @var PreparedStoredProcedures
     */
    protected static $preparedStatement;

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        self::$preparedStatement = self::getDbSetup();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass()
    {
        self::tearDownDB();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function setUp()
    {
        $this->class = $this->extendAbstractClass();
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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetUserByIdReturnsUserObject(): void
    {
        $user = $this->class->userId(USERUUID);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(USERUUID, $user->id);
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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetUserByNameReturnsUserObject(): void
    {
        $user = $this->class->userName('testName');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('TestingAdmin', $user->displayName);
    }

    public function testGetUserByNameThrowsExceptionWithInvalidUsername(): void
    {
        $this->expectException(BlueFishException::class);
        $this->class->userName('someUserName');
    }
}
