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
 * Date: 1/10/19 - 9:50 AM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Management;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Users\User;
use Geeshoe\DbLib\Core\PreparedStatements;
use Geeshoe\DbLib\Exceptions\DbLibQueryException;

/**
 * Class AbstractUserDBFunctions
 *
 * @package Geeshoe\BlueFish\Management
 */
abstract class AbstractUserDBFunctions
{
    /**
     * @var PreparedStatements
     */
    protected $prepStmt;

    /**
     * AbstractUserDBFunctions constructor.
     *
     * @param PreparedStatements $preparedStatements
     */
    public function __construct(PreparedStatements $preparedStatements)
    {
        $this->prepStmt = $preparedStatements;
    }

    /**
     * @param string $username
     *
     * @return User
     *
     * @throws BlueFishException
     */
    protected function getUserByUsername(string $username): User
    {
        $sql = 'SELECT * FROM BF_Users WHERE username = :username;';

        try {
            $result = $this->prepStmt->executePreparedFetchAsClass(
                $sql,
                ['username' => $username],
                User::class
            );
        } catch (DbLibQueryException $exception) {
            BlueFishException::userDoesNotExist($exception);
        }

        return $result;
    }

    /**
     * @param string $id
     *
     * @return User
     *
     * @throws BlueFishException
     */
    protected function getUserByID(string $id): User
    {
        $sql = 'SELECT * FROM BF_Users WHERE id = :id;';

        try {
            $result = $this->prepStmt->executePreparedFetchAsClass(
                $sql,
                ['id' => $id],
                User::class
            );
        } catch (DbLibQueryException $exception) {
            BlueFishException::userDoesNotExist($exception);
        }

        return $result;
    }
}
