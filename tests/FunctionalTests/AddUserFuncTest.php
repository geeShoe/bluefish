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

namespace Geeshoe\BlueFish\Tests\FunctionalTests;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Management\AddUser;
use Geeshoe\BlueFish\Model\UserProspect;
use Geeshoe\BlueFish\Tests\DBSetupForFuncTests;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;

/**
 * Class AddUserFuncTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests
 */
class AddUserFuncTest extends TestCase
{
    use DBSetupForFuncTests;

    /**
     * @var string
     */
    public $roleID;

    /**
     * @var string
     */
    public $statusId;

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
     * @throws BlueFishException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testCreateUserAccountAddsNewUser(): void
    {
        $newUser = new UserProspect();
        $newUser->username = 'myName';
        $newUser->password = 'pass';
        $newUser->passwordVerify = 'pass';
        $newUser->displayName = 'myDisplay';
        $newUser->role = ROLEUUID;
        $newUser->status = STATUSUUID;

        $addUser = new AddUser(self::$preparedStatement);
        $user = $addUser->createUserAccount($newUser);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @throws BlueFishException
     */
    public function testCreateUserAccountDoesNotAddDuplicateUsers(): void
    {
        $newUser = new UserProspect();
        $newUser->username = 'myName';
        $newUser->password = 'pass';
        $newUser->passwordVerify = 'pass';
        $newUser->displayName = 'myDisplay';
        $newUser->role = ROLEUUID;
        $newUser->status = STATUSUUID;

        $addUser = new AddUser(self::$preparedStatement);
        $addUser->createUserAccount($newUser);
        $this->expectException(BlueFishException::class);
        $addUser->createUserAccount($newUser);
    }
}
