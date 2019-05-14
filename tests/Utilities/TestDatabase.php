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

use Geeshoe\BlueFish\Tests\Connection;
use Geeshoe\BlueFish\Tests\Database;

/**
 * Trait TestDatabase
 *
 * @package Geeshoe\BlueFish\Tests\Utilities
 */
trait TestDatabase
{
    /**
     * @return \PDO
     *
     * @throws \PDOException
     */
    public static function getConnection(): \PDO
    {
        $connection = new Connection();
        return $connection->getPDO();
    }

    /**
     * @param \PDO $pdo
     */
    public static function buildDatabase(\PDO $pdo): void
    {
        $db = new Database($pdo);
        $db->createSchema();
        $db->execSQLFiles();
    }

    /**
     * @param \PDO $pdo
     */
    public static function dropDatabase(\PDO $pdo): void
    {
        $db = new Database($pdo);
        $db->dropSchema();
    }
}
