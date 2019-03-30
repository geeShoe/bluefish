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
 * Date: 1/10/19 - 7:21 AM
 */

namespace Geeshoe\BlueFish\Tests\FunctionalTests;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Tests\DBSetupForFuncTests;
use Geeshoe\BlueFish\Users\BlueFish;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;

/**
 * Class BlueFishTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests
 */
class BlueFishTest extends TestCase
{
    use DBSetupForFuncTests;

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
     */
    public function testLoginIsSuccessful(): void
    {
        $blueFish = new BlueFish(self::$preparedStatement);
        $user = $blueFish->login('testName', 'password');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('TestingAdmin', $user->displayName);
    }

    /**
     * @throws BlueFishException
     */
    public function testBlueFishThrowsExceptionWithInvalidUsername(): void
    {
        $blueFish = new BlueFish(self::$preparedStatement);

        $this->expectException(BlueFishException::class);
        $this->expectExceptionMessage('User does not exist.');
        $this->expectExceptionCode(101);

        $blueFish->login('wrongUser', 'wrongPass');
    }

    /**
     * @throws BlueFishException
     */
    public function testBlueFishThrowsExceptionWithInvalidPassword(): void
    {
        $blueFish = new BlueFish(self::$preparedStatement);

        $this->expectException(BlueFishException::class);
        $this->expectExceptionMessage('Password mismatch.');
        $this->expectExceptionCode(102);

        $blueFish->login('testName', 'wrongPassword');
    }
}
