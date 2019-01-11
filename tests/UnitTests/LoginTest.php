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
 * Date: 1/10/19 - 4:33 AM
 */

namespace Geeshoe\BlueFish\Tests\UnitTests;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Users\Login;
use PHPUnit\Framework\TestCase;

/**
 * Class LoginTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests
 */
class LoginTest extends TestCase
{
    /**
     * Data provider for testSanitizeLoginCredsThrowsExceptionWithEmptyCreds
     *
     * @return array
     */
    public function loginCredsDataSet(): array
    {
        $code100 = 100;
        $username = 'Username cannot be empty.';
        $code104 = 104;
        $password = 'Password cannot be empty.';

        return [
            ['', '', $code100, $username],
            ['username', '', $code104, $password],
            ['', 'password', $code100, $username]
        ];
    }

    /**
     * @dataProvider loginCredsDataSet
     *
     * @param string    $username
     * @param string    $password
     * @param int       $code       Exception code
     * @param string    $message    Exception message
     *
     * @throws BlueFishException
     */
    public function testSanitizeLoginCredsThrowsExceptionWithEmptyCreds(
        string $username,
        string $password,
        int $code,
        string $message
    ): void {
        $this->expectException(BlueFishException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode($code);

        Login::sanitizeLoginCredentials($username, $password);
    }
}
