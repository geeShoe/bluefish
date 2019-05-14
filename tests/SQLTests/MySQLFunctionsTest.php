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

namespace Geeshoe\BlueFish\Tests\SQLTests;

use Geeshoe\BlueFish\Tests\Utilities\TestDatabase;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class MySQLFunctionsTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class MySQLFunctionsTest extends TestCase
{
    use TestDatabase;

    /**
     * @var PreparedStoredProcedures
     */
    public static $storedProcedures;

    /**
     * @var \PDO
     */
    public static $pdo;

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$pdo = self::getConnection();
        self::$pdo->exec('USE ' . getenv('GSD_BFTD_DATABASE'));
        self::$pdo->exec('CREATE TABLE uuid_table(id BINARY(16) PRIMARY KEY);');
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        self::$pdo->exec('DELETE FROM uuid_table;');
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec('DROP TABLE IF EXISTS uuid_table;');
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUuidToBin(): void
    {
        $id = Uuid::uuid4();

        $string = $id->toString();

        self::$pdo->exec("INSERT INTO uuid_table SET id = UuidToBin('".$string."');");

        $query = self::$pdo->query('SELECT * FROM uuid_table;');

        $result = $query->fetchAll();

        $this->assertSame($id->getBytes(), $result[0]['id']);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUuidFromBin(): void
    {
        $id = Uuid::uuid4();

        $string = $id->toString();

        $bin = $id->getBytes();

        self::$pdo->exec("INSERT INTO uuid_table SET id = '".$bin."';");

        $query = self::$pdo->query('SELECT UuidFromBin(id) as id FROM uuid_table;');

        $results = $query->fetchAll();

        $this->assertSame($string, $results[0]['id']);
    }
}
