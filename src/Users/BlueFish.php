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
use Geeshoe\DbLib\DbLib;

/**
 * Class BlueFish
 *
 * @package Geeshoe\BlueFish\Users
 */
class BlueFish
{
    /**
     * @var DbLib|null
     */
    protected $database = null;

    /**
     * @var null|string
     */
    protected $username = null;

    /**
     * @var null|string
     */
    protected $password = null;

    /**
     * @var null|array
     */
    protected $userRecord = null;

    /**
     * BlueFish constructor.
     *
     * @param DbLib $database
     */
    public function __construct(DbLib $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $username
     * @param string $password
     * @throws BlueFishException
     */
    protected function sanitizeLoginCredentials(string $username, string $password)
    {
        if (empty($username) or empty($password)) {
            throw new BlueFishException(
                'Username and/or password cannot be empty.',
                100
            );
        }

        $this->username = trim(filter_var($username, FILTER_SANITIZE_STRING));
        $this->password = trim(filter_var($password, FILTER_SANITIZE_STRING));

        if (empty($this->username) or empty($this->password)) {
            throw new BlueFishException(
                'Username and/or password cannot be empty.',
                100
            );
        }
    }

    /**
     * @param array $user User data from database.
     */
    protected function populateUserRecord(array $user): void
    {
        $this->userRecord = [
            'displayName' => trim(filter_var($user['displayName'], FILTER_SANITIZE_STRING)),
            'role' => trim(filter_var($user['role'], FILTER_SANITIZE_STRING)),
            'status' => trim(filter_var($user['status'], FILTER_SANITIZE_STRING))
        ];
    }

    /**
     * @param string $passwordHash
     * @return bool
     * @throws BlueFishException
     */
    protected function comparePassword(string $passwordHash): bool
    {
        if (password_verify($this->password, $passwordHash)) {
            return true;
        }

        throw new BlueFishException(
            'Password mismatch.',
            102
        );
    }

    /**
     * @return bool
     * @throws BlueFishException
     */
    protected function validateUser(): bool
    {
        $sql = 'SELECT `username`, `password` FROM `BF_Users` WHERE username = :username';

        $param = [':username' => $this->username];

        try {
            $query = $this->database->manipulateDataWithSingleReturn($sql, $param, \PDO::FETCH_ASSOC);
        } catch (\Exception $exception) {
            throw new BlueFishException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $this->database = null;
        unset($param);
        unset($sql);

        if (empty($query)) {
            $query = null;
            $this->username = null;
            $this->password = null;
            throw new BlueFishException(
                'User does not exist.',
                101
            );
        }

        $passwordHash = trim(filter_var($query['password'], FILTER_SANITIZE_STRING));

        if (self::comparePassword($passwordHash)) {
            unset($passwordHash);
            unset($query['username']);
            unset($query['password']);
            $this->username = null;
            $this->password = null;
            self::populateUserRecord($query);
            return true;
        }

        return false;
    }

    /**
     * Public entry point to BlueFish.
     *
     * @param string $username
     * @param string $password
     * @return array
     * @throws BlueFishException
     */
    public function login(string $username, string $password): array
    {
        self::sanitizeLoginCredentials($username, $password);
        if (self::validateUser()) {
            return $this->userRecord;
        }

        return [];
    }
}
