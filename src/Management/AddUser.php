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
 * Date: 1/10/19 - 9:09 AM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Management;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\BlueFish\Model\UserProspect;
use Geeshoe\DbLib\Exceptions\DbLibException;
use Ramsey\Uuid\Uuid;

/**
 * Class AddUser
 *
 * @package Geeshoe\BlueFish\Management
 */
class AddUser extends AbstractUserDBFunctions
{
    /**
     * Create a new user account in the database.
     *
     * Creates the account with user role 2 (Registered) and with a
     * user status of 2 (Unauthenticated).
     *
     * @param UserProspect $userProspect
     *
     * @return User
     *
     * @throws BlueFishException
     */
    public function createUserAccount(UserProspect $userProspect): User
    {
        $user = FilterUserParams::filterUser($userProspect);

        $this->comparePasswords($user->password, $user->passwordVerify);

        try {
            $user->id = Uuid::uuid4()->toString();
        } catch (\Exception $exception) {
            BlueFishException::uuidProblem($exception);
        }

        $user->password = $this->hashPassword($user->password);

        try {
            $this->addUserToDb($user);
        } catch (DbLibException $exception) {
            throw new BlueFishException('Unable to add user account.', 0, $exception);
        }
        return $this->getUserByID($user->id);
    }

    /**
     * @param string $password
     *
     * @param string $passwordVerify
     *
     * @throws BlueFishException
     */
    protected function comparePasswords(string $password, string $passwordVerify): void
    {
        if ($password !== $passwordVerify) {
            BlueFishException::passwordMismatch();
        }
    }

    /**
     * @param string $password
     *
     * @return string
     */
    protected function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param UserProspect $user
     *
     * @throws BlueFishException
     */
    protected function addUserToDb(UserProspect $user): void
    {
        try {
            $this->prepStmt->executePreparedStoredProcedure(
                'add_user_account',
                [
                    'id' => $user->id,
                    'username' => $user->username,
                    'password' => $user->password,
                    'displayName' => $user->displayName,
                    'role' => $user->role,
                    'status' => $user->status
                ]
            );
        } catch (DbLibException $exception) {
            BlueFishException::dbFailure($exception);
        }
    }
}
