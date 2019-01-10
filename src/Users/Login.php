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
 * Date: 1/9/19 - 4:37 PM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Users;

use Geeshoe\BlueFish\Exceptions\BlueFishException;

/**
 * Class Login
 *
 * @package Geeshoe\BlueFish\Users
 */
class Login
{
    /**
     * @param string $username
     * @param string $password
     *
     * @throws BlueFishException
     */
    protected static function checkEmptyCredentials(string $username, string $password): void
    {
        if (empty($username)) {
            BlueFishException::usernameEmpty();
        }

        if (empty($password)) {
            BlueFishException::passwordEmpty();
        }
    }

    /**
     * As the name implies, method sanitizes the user provided username & password.
     *
     * Method check's if either of the two param's are empty, filters them, then
     * re-check's if either are empty. If the param's are found to be empty during
     * either empty check, an exception is thrown. Otherwise, the sanitized param's
     * are returned as an array.
     *
     * @param string    $username
     * @param string    $password
     *
     * @return array    $user['username' => 'sanitized_user', 'password' => 'sanitized_pass']
     *
     * @throws BlueFishException
     */
    public static function sanitizeLoginCredentials(string $username, string $password): array
    {
        self::checkEmptyCredentials($username, $password);

        $user = [];

        $user['username'] = trim(filter_var($username, FILTER_SANITIZE_STRING));
        $user['password'] = trim(filter_var($password, FILTER_SANITIZE_STRING));

        self::checkEmptyCredentials($user['username'], $user['password']);

        return $user;
    }
}
