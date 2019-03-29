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
 * Date: 1/11/19 - 5:03 AM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Tests;

use Geeshoe\BlueFish\Db\PreparedStatementsExt;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\DbLib\Core\PreparedStatements;
use Ramsey\Uuid\Uuid;

/**
 * Class DBSetup
 *
 * @package Geeshoe\BlueFish\Tests
 */
class DBSetup
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var PreparedStatementsExt
     */
    protected $preparedStatement;

    /**
     * Create a PDO Connection for testing using EnvVars
     */
    protected function makePDO(): void
    {
        $host = getenv('GSD_BFTD_HOST');
        $port = getenv('GSD_BFTD_PORT');

        $this->pdo = new \PDO(
            'mysql:host=' . $host . ';port=' . $port,
            getenv('GSD_BFTD_USER'),
            getenv('GSD_BFTD_PASSWORD')
        );
    }

    /**
     * Setup a test database for functional testing.
     *
     * Should be called with PHPUnit's setUp() method.
     *
     * @return PreparedStatementsExt
     * @throws \Exception
     */
    public function setupDb(): PreparedStatementsExt
    {
        $this->makePDO();

        $this->createTempDB();
        $this->pdo->exec('USE ' . getenv('GSD_BFTD_DATABASE').';');
        $this->createTestTable();

        $this->preparedStatement = new PreparedStatementsExt($this->pdo);

        $this->insertTestUser();

        return $this->preparedStatement;
    }

    /**
     * Destroy DB used for testing.
     *
     * Should be called with PHPUnit's tearDown() method.
     */
    public function tearDownDB(): void
    {
        $this->makePDO();
        $this->removeTestSchema();
        $this->pdo = null;
    }

    /**
     * @return int
     */
    protected function removeTestSchema(): int
    {
        return $this->pdo->exec(
            'DROP SCHEMA`' . getenv('GSD_BFTD_DATABASE') . '`;'
        );
    }

    protected function createTempDB(): void
    {
        $result = $this->createTestSchema();

        if ($result !== 1) {
            $this->removeTestSchema();
            $result = $this->createTestSchema();
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
    protected function createTestSchema(): int
    {
        return $this->pdo->exec(
            'CREATE SCHEMA`' . getenv('GSD_BFTD_DATABASE') . '`;'
        );
    }

    protected function createTestTable(): void
    {
        $baseDir = dirname(__DIR__, 1) . '/sql/';

        $sql['tables'] = file_get_contents($baseDir . 'blueFishTables.sql');
        $sql['functions'] = file_get_contents($baseDir . 'functions.sql');
        $sql['views'] = file_get_contents($baseDir . 'views.sql');
        $sql['procedures'] = file_get_contents($baseDir . 'procedures.sql');

        foreach ($sql as $file) {
            $this->pdo->exec($file);
        }
//        $this->pdo->exec($sql);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function insertTestRoleStatus(): array
    {
        $role = [
            'id' => ROLEUUID,
            'role' => 'test'
        ];

        $status = [
            'id' => STATUSUUID,
            'status' => 'test'
        ];

        $this->pdo->exec('CALL add_user_role("' . $role['id']. '", "'.$role['role'].'");');
//        $this->preparedStatement->executePreparedInsertQuery(
//            'BF_Roles',
//            $role
//        );

        $this->pdo->exec('CALL add_user_status("'.$status['id'].'", "'.$status['status'].'");');
//        $this->preparedStatement->executePreparedInsertQuery(
//            'BF_Status',
//            $status
//        );

        return ['role' => $role['id'], 'status' => $status['id']];
    }

    /**
     * @throws \Exception
     */
    protected function insertTestUser(): void
    {
        $roleStatusUUIDs = $this->insertTestRoleStatus();

        $user = new User();
        $user->id = Uuid::uuid4()->toString();
        $user->username = 'testName';
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $user->displayName = 'TestingAdmin';
        $user->role = $roleStatusUUIDs['role'];
        $user->status = $roleStatusUUIDs['status'];

        $this->preparedStatement->executePreparedStoredProcedure(
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
