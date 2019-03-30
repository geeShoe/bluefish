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
 * Date: 3/29/19 - 9:33 PM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Tests;

use Geeshoe\BlueFish\Model\User;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;

/**
 * Class DbTestBootStrap
 *
 * @package Geeshoe\BlueFish\Tests
 */
class DbTestBootStrap
{
    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @throws \PDOException
     */
    protected static function makePDO(): void
    {
        $host = getenv('GSD_BFTD_HOST');
        $port = getenv('GSD_BFTD_PORT');

        self::$pdo = new \PDO(
            'mysql:host=' . $host . ';port=' . $port,
            getenv('GSD_BFTD_USER'),
            getenv('GSD_BFTD_PASSWORD')
        );
    }

    /**
     * Destroy DB used for testing.
     *
     * Should be called with PHPUnit's tearDown() method.
     */
    public function tearDownDB(): void
    {
        self::makePDO();
        self::removeTestSchema();
        self::$pdo = null;
    }

    /**
     * @return PreparedStoredProcedures
     * @throws \Geeshoe\DbLib\Exceptions\DbLibException
     * @throws \PDOException
     */
    public static function setupDb(): PreparedStoredProcedures
    {
        self::makePDO();
        self::createTempDb();
        self::$pdo->exec('USE ' . getenv('GSD_BFTD_DATABASE').';');
        self::createTestTable();

        $storedProcedure = new PreparedStoredProcedures(self::$pdo);

        self::insertTestUser($storedProcedure);

        return $storedProcedure;
    }

    /**
     * @throws \PDOException
     */
    protected static function createTempDb(): void
    {
        $result = self::createTestSchema();

        if ($result !== 1) {
            self::removeTestSchema();
            $result = self::createTestSchema();
        }

        if ($result !== 1) {
            throw new \PDOException(
                'Unable to create test schema.'
            );
        }
    }

    /**
     * @return int
     */
    protected static function createTestSchema(): int
    {
        return self::$pdo->exec(
            'CREATE SCHEMA`' . getenv('GSD_BFTD_DATABASE') . '`;'
        );
    }

    /**
     * @return int
     */
    protected static function removeTestSchema(): int
    {
        return self::$pdo->exec(
            'DROP SCHEMA`' . getenv('GSD_BFTD_DATABASE') . '`;'
        );
    }

    /**
     *
     */
    protected static function createTestTable(): void
    {
        $baseDir = dirname(__DIR__, 1) . '/sql/';

        $sql['tables'] = file_get_contents($baseDir . 'blueFishTables.sql');
        $sql['functions'] = file_get_contents($baseDir . 'functions.sql');
        $sql['views'] = file_get_contents($baseDir . 'views.sql');
        $sql['procedures'] = file_get_contents($baseDir . 'procedures.sql');

        foreach ($sql as $file) {
            self::$pdo->exec($file);
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected static function insertTestRoleStatus(): array
    {
        $role = [
            'id' => ROLEUUID,
            'role' => 'test'
        ];

        $status = [
            'id' => STATUSUUID,
            'status' => 'test'
        ];

        self::$pdo->exec('CALL add_user_role("' . $role['id']. '", "'.$role['role'].'");');

        self::$pdo->exec('CALL add_user_status("'.$status['id'].'", "'.$status['status'].'");');

        return ['role' => $role['id'], 'status' => $status['id']];
    }

    /**
     * @param PreparedStoredProcedures $storedProcedures
     *
     * @throws \Geeshoe\DbLib\Exceptions\DbLibException
     */
    protected static function insertTestUser(PreparedStoredProcedures $storedProcedures): void
    {
        $roleStatusUUIDs = self::insertTestRoleStatus();

        $user = new User();
        $user->id = USERUUID;
        $user->username = 'testName';
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $user->displayName = 'TestingAdmin';
        $user->role = $roleStatusUUIDs['role'];
        $user->status = $roleStatusUUIDs['status'];

        $storedProcedures->executePreparedStoredProcedure(
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
    }
}
