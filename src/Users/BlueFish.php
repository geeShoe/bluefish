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
use Geeshoe\BlueFish\Model\User;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException;

/**
 * Class BlueFish
 *
 * @package Geeshoe\BlueFish\Users
 */
class BlueFish
{
    /**
     * @var PreparedStoredProcedures
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
     * @param PreparedStoredProcedures $preparedStatements
     */
    public function __construct(PreparedStoredProcedures $preparedStatements)
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
        $user->displayName = trim(filter_var($user->displayName, FILTER_SANITIZE_SPECIAL_CHARS));
        $user->role = trim(filter_var($user->role, FILTER_SANITIZE_SPECIAL_CHARS));
        $user->status = trim(filter_var($user->status, FILTER_SANITIZE_SPECIAL_CHARS));

        $this->userRecord = $user;
    }

    protected function getUserRecord(string $userUUID): User
    {
        try {
            $result = $this->dblPrepStmt->executePreparedFetchAsClass(
                'Call get_user_account_by_id(:id)',
                ['id' => $userUUID],
                User::class
            );
        } catch (DbLibPreparedStmtException $exception) {
            BlueFishException::dbFailure($exception);
            $result = new User();
        }

        return $result;
    }

    /**
     * @param string $password
     * @return bool
     * @throws BlueFishException
     */
    protected function comparePassword(string $password): bool
    {
        $knownPassword = trim(filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS));

        if (!password_verify($this->password, $knownPassword)) {
            BlueFishException::passwordMismatch();
        }

        return true;
    }

    /**
     * @return User
     *
     * @throws BlueFishException
     */
    protected function getUser(): User
    {
        try {
            $result = $this->dblPrepStmt->executePreparedFetchAsClass(
                'CALL get_user_login_credentials(:username)',
                ['username' => $this->username],
                User::class
            );
        } catch (DbLibPreparedStmtException $exception) {
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
        try {
            $user = $this->getUser();
        } catch (BlueFishException $exception) {
            BlueFishException::userDoesNotExist($exception);
        }

        if (self::comparePassword($user->password)) {
            $userRecord = self::getUserRecord($user->id);
            $user = null;
            self::populateUserRecord($userRecord);
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
        BlueFishException::unableToLoginFallBack();
    }
}
