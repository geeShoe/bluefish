<?php
/**
 * Copyright 2018 Jesse Rushlow - Geeshoe Development
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
 * Date: 11/16/18 - 3:42 PM
 */

namespace Geeshoe\BlueFish\Tests;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Users\BlueFish;
use Geeshoe\DbLib\DbLib;
use Geeshoe\DbLib\DbLibException;
use PHPUnit\Framework\TestCase;

/**
 * Class BlueFishTest
 *
 * @package Geeshoe\BlueFish\Tests
 */
class BlueFishTest extends TestCase
{
    /**
     * @var null
     */
    public $dbMock = null;

    public function setUp()
    {
        $this->dbMock = $this->getMockBuilder(DbLib::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @throws BlueFishException
     */
    public function testLoginSuccessful()
    {
        $bluefish = new BlueFish($this->dbMock);

        $this->dbMock->method('manipulateDataWithSingleReturn')
            ->willReturn([
                'username' => 'someUser',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'displayName' => 'unitTestName',
                'role' => 'user',
                'status' => 'testing'
            ]);

        $expected = [
            'displayName' => 'unitTestName',
            'role' => 'user',
            'status' => 'testing'
        ];

        $array = $bluefish->login('someUser', 'password');
        self::assertSame($expected, $array);
    }

    /**
     * @return array
     */
    public function exceptionDataset()
    {
        return [
            'Exception 100 not thrown with empty username.' => [
                100,
                'Username and/or password cannot be empty.',
                '',
                'password'
            ],
            'Exception 100 not thrown with empty password.' => [
                100,
                'Username and/or password cannot be empty.',
                'username',
                ''
            ],
            'Exception 100 not thrown with malicious username.' => [
                100,
                'Username and/or password cannot be empty.',
                '<?php',
                'password?!'
            ],
            'Exception 100 not thrown with malicious password.' => [
                100,
                'Username and/or password cannot be empty.',
                'user_132name',
                '<?php'
            ],
            'Exception 101 not thrown with wrong username' => [
                101,
                'User does not exist.',
                'user1234',
                'password'
            ],
            'Exception 102 not thrown with wrong password' => [
                102,
                'Password mismatch.',
                'someUser',
                'wrongPassword'
            ]
        ];
    }

    /**
     * @dataProvider exceptionDataset
     */
    public function testBluefishReturnsExceptions($code, $message, $username, $password)
    {
        if ($code === 101) {
            $this->dbMock->method('manipulateDataWithSingleReturn')
                ->willReturn([]);
        } else {
            $this->dbMock->method('manipulateDataWithSingleReturn')
                ->willReturn([
                    'username' => 'someUser',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'displayName' => 'unitTestName',
                    'role' => 'user',
                    'status' => 'testing'
                ]);
        }

        $bluefish = new BlueFish($this->dbMock);
        self::expectException(BlueFishException::class);
        self::expectExceptionCode($code);
        self::expectExceptionMessage($message);
        $bluefish->login($username, $password);
    }

    /**
     * @throws BlueFishException
     */
    public function testBlueFishThrowsCaughtDbLibException()
    {
        $this->dbMock->method('manipulateDataWithSingleReturn')
            ->willThrowException(new DbLibException('DbLib exception', 123));

        $bluefish = new BlueFish($this->dbMock);
        self::expectException(BlueFishException::class);
        self::expectExceptionMessage('DbLib exception');
        self::expectExceptionCode(123);
        $bluefish->login('user', 'pass');
    }
}
