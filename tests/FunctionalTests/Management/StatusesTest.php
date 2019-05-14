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

namespace Geeshoe\BlueFish\Tests\FunctionalTests\Management;

use Geeshoe\BlueFish\Management\Statuses;
use Geeshoe\BlueFish\Model\Status;
use Geeshoe\BlueFish\Tests\Utilities\TestDatabase;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class StatusesTest
 *
 * @package Geeshoe\BlueFish\Tests\FunctionalTests\Management
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class StatusesTest extends TestCase
{
    use TestDatabase;

    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @var PreparedStoredProcedures
     */
    public static $storedProcedures;

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$pdo = self::getConnection();
        self::$pdo->exec('USE ' . getenv('GSD_BFTD_DATABASE'));
        self::$storedProcedures = new PreparedStoredProcedures(self::$pdo);
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAddStatusAddsStatusToDatabase(): void
    {
        $status = new Status();
        $status->id = Uuid::uuid4()->toString();
        $status->status = 'FuncTest';

        $statuses = new Statuses(self::$storedProcedures);
        $statuses->addStatus($status);

        $result = self::$storedProcedures->executePreparedFetchAsClass(
            'SELECT status, UuidFromBin(id) as id FROM BF_Status where status = :status',
            ['status' => $status->status],
            Status::class
        );

        $this->assertSame($status->id, $result->id);
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetStatusByNameReturnsStatusObject(): void
    {
        $id = Uuid::uuid4();

        $status = new Status();
        $status->id = $id->toString();
        $status->status = 'FuncTestByName';

        $statuses = new Statuses(self::$storedProcedures);

        self::$storedProcedures->executePreparedInsertQuery(
            'BF_Status',
            [
                'id' => $id->getBytes(),
                'status' => $status->status
            ]
        );

        $result = $statuses->getStatusByName($status->status);

        $this->assertSame($status->id, $result->id);
        $this->assertSame($status->status, $result->status);
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetStatusByIdReturnsStatusObject(): void
    {
        $id = Uuid::uuid4();

        $status = new Status();
        $status->id = $id->toString();
        $status->status = 'FuncTestById';

        $statuses = new Statuses(self::$storedProcedures);

        self::$storedProcedures->executePreparedInsertQuery(
            'BF_Status',
            [
                'id' => $id->getBytes(),
                'status' => $status->status
            ]
        );

        $result = $statuses->getStatusById($status->id);

        $this->assertSame($status->id, $result->id);
        $this->assertSame($status->status, $result->status);
    }
}
