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
 * Date: 3/29/19 - 3:25 PM
 */

namespace Geeshoe\BlueFish\Tests\UnitTests;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use PHPUnit\Framework\TestCase;

/**
 * Class BlueFishExceptionTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests
 */
class BlueFishExceptionTest extends TestCase
{
    /**
     * @return array
     */
    public function exceptionDataProvider(): array
    {
        return [
            'UsernameEmpty' => ['usernameEmpty', 'Username cannot be empty.', 100],
            'User Does Not Exist' => ['userDoesNotExist', 'User does not exist.', 101],
            'Password Mismatch' => ['passwordMismatch', 'Password mismatch.', 102],
            'Unable to Login' => ['unableToLoginFallBack', 'Unable to login. Contact administrator.', 103],
            'Password Empty' => ['passwordEmpty', 'Password cannot be empty.', 104],
            'UUID Problem' => ['uuidProblem', 'Problem with creating a UUID.', 105],
            'Display Name Empty' => ['displayNameEmpty', 'Display name cannot be empty.', 106],
            'Db Failure' => ['dbFailure', 'Db query failed.', 107]
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     *
     * @param string $exceptionMethod
     * @param string $message
     * @param int    $code
     */
    public function testBlueFishExceptions(string $exceptionMethod, string $message, int $code): void
    {
        $this->expectException(BlueFishException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode($code);

        call_user_func('Geeshoe\BlueFish\Exceptions\BlueFishException::'.$exceptionMethod);
    }
}
