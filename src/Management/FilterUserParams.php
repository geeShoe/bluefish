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
 * Date: 1/10/19 - 9:14 AM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Management;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Users\Login;

/**
 * Class FilterUserParams
 *
 * @package Geeshoe\BlueFish\Management
 */
class FilterUserParams extends Login
{
    /**
     * @param string $displayName
     *
     * @return string
     *
     * @throws BlueFishException
     */
    public static function filterDisplayName(string $displayName): string
    {
        $displayName = trim(filter_var($displayName, FILTER_SANITIZE_SPECIAL_CHARS));

        if (empty($displayName)) {
            BlueFishException::displayNameEmpty();
        }

        return $displayName;
    }

    /**
     * @param string $password
     *
     * @return string
     *
     * @throws BlueFishException
     */
    public static function filterPasswordVerify(string $password): string
    {
        $password = trim(filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS));

        if (empty($password)) {
            BlueFishException::passwordEmpty();
        }

        return $password;
    }

    /**
     * @param UserProspect $user
     *
     * @return UserProspect
     *
     * @throws BlueFishException
     */
    public static function filterUser(UserProspect $user): UserProspect
    {
        $cleanUser = new UserProspect();

        $filtered = self::sanitizeLoginCredentials($user->username, $user->password);

        $cleanUser->username = $filtered['username'];
        $cleanUser->password = $filtered['password'];
        $cleanUser->passwordVerify = self::filterPasswordVerify($user->passwordVerify);
        $cleanUser->displayName = self::filterDisplayName($user->displayName);

        return $cleanUser;
    }
}
