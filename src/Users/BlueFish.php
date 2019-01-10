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
 * Date: 11/16/18 - 1:12 PM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Users;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\DbLib\Core\PreparedStatements;

/**
 * Class BlueFish
 *
 * @package Geeshoe\BlueFish\Users
 */
class BlueFish
{
    /**
     * @var PreparedStatements
     */
    protected $dblPrepStmt;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var User
     */
    protected $userRecord;

    /**
     * BlueFish constructor.
     *
     * @param PreparedStatements $preparedStatements
     */
    public function __construct(PreparedStatements $preparedStatements)
    {
        $this->dblPrepStmt = $preparedStatements;
    }

    /**
     * @param User $user User data from database.
     */
    protected function populateUserRecord(User $user): void
    {
        $user->id = null;
        $user->username = null;
        $user->password = null;
        $user->displayName = trim(filter_var($user->displayName, FILTER_SANITIZE_STRING));
        $user->role = trim(filter_var($user->role, FILTER_SANITIZE_STRING));
        $user->status = trim(filter_var($user->status, FILTER_SANITIZE_STRING));

        $this->userRecord = $user;
    }

    /**
     * @param string $password
     * @return bool
     * @throws BlueFishException
     */
    protected function comparePassword(string $password): bool
    {
        $knownPassword = trim(filter_var($password, FILTER_SANITIZE_STRING));

        if (password_verify($this->password, $knownPassword)) {
            return true;
        }

        throw new BlueFishException(
            'Password mismatch.',
            102
        );
    }

    /**
     * @return User
     *
     * @throws BlueFishException
     */
    protected function getUser(): User
    {
        try {
            $sql = 'SELECT `username`, `password`, `displayName`, `role`, `status`';
            $sql .= ' FROM `BF_Users` WHERE username = :username';

            $result = $this->dblPrepStmt->executePreparedFetchAsClass(
                $sql,
                ['username' => $this->username],
                User::class
            );
        } catch (\Exception $exception) {
            throw new BlueFishException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $result;
    }

    /**
     * @return bool
     * @throws BlueFishException
     */
    protected function validateUser(): bool
    {
        $user = $this->getUser();

        if (is_null($user->username)) {
            $this->username = null;
            $this->password = null;
            throw new BlueFishException(
                'User does not exist.',
                101
            );
        }

        if (self::comparePassword($user->password)) {
            self::populateUserRecord($user);
            return true;
        }

        //This return statement has intentionally been left here. In theory,
        //it will never be executed and is untestable. It's here as a fallback
        //in case something within the class breaks. Better safe than sorry
        //when it comes to authentication.
        return false;
    }

    /**
     * Public entry point to BlueFish.
     *
     * @param string $username
     * @param string $password
     * @return User
     *
     * @throws BlueFishException
     */
    public function login(string $username, string $password): User
    {
        $credentials = Login::sanitizeLoginCredentials($username, $password);

        $this->username = $credentials['username'];
        $this->password = $credentials['password'];

        if (self::validateUser()) {
            return $this->userRecord;
        }

        //This exception has been intentionally left here as a fallback in case
        //something breaks within the class. In theory, it's untestable and
        //should never be thrown, however better safe than sorry when it comes
        //to authentication.
        throw new BlueFishException(
            'Unable to login. Contact administrator.',
            103
        );
    }
}
