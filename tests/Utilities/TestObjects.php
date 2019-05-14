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

declare(strict_types=1);

namespace Geeshoe\BlueFish\Tests\Utilities;

use Geeshoe\BlueFish\Model\Role;
use Geeshoe\BlueFish\Model\Status;
use Geeshoe\BlueFish\Model\User;
use Ramsey\Uuid\Uuid;

/**
 * Trait TestObjects
 *
 * @package Geeshoe\BlueFish\Tests\Utilities
 */
trait TestObjects
{
    /**
     * @return User
     *
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public static function userObject(): User
    {
        $user = new User();
        $user->id = Uuid::uuid4();
        $user->displayName = 'TestName';
        $user->username = 'TestUserName';

        return $user;
    }

    /**
     * @return Status
     *
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public static function statusObject(): Status
    {
        $status = new Status();
        $status->id = Uuid::uuid4();
        $status->status = 'TestStatus';

        return $status;
    }

    /**
     * @return Role
     *
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public static function roleObject(): Role
    {
        $role = new Role();
        $role->id = Uuid::uuid4();
        $role->role = 'TestRole';

        return $role;
    }

    /**
     * @return array
     *
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public static function getUserRoleStatusArray(): array
    {
        $status = self::statusObject();
        $role = self::roleObject();
        $user = self::userObject();

        $user->role = $role->id->toString();
        $user->status = $status->id->toString();

        return ['user' => $user, 'role' => $role, 'status' => $status];
    }
}
