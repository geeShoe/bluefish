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

namespace Geeshoe\BlueFish\Tests\UnitTests\Management;

use Geeshoe\BlueFish\Management\Statuses;
use Geeshoe\BlueFish\Model\Status;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class StatusesTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests\Management
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class StatusesTest extends TestCase
{
    /**
     * @var MockObject|PreparedStoredProcedures
     */
    public $mockPrep;

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->mockPrep = $this->getMockBuilder(PreparedStoredProcedures::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \ReflectionException
     */
    public function testAddStatus(): void
    {
        $status = new Status();
        $status->id = Uuid::uuid4()->toString();
        $status->status = 'UnitTest';

        $mockPrepStrdProc = $this->getMockBuilder(PreparedStoredProcedures::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockPrepStrdProc->expects($this->once())
            ->method('executePreparedStoredProcedure')
            ->with('add_status', ['id' => $status->id, 'status' => $status->status]);

        $statuses = new Statuses($mockPrepStrdProc);

        $statuses->addStatus($status);
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testGetStatusByNameCallsMySQLProcedure(): void
    {
        $this->mockPrep->expects($this->once())
            ->method('executePreparedFetchAsClass')
            ->with(
                'CALL get_status_by_name(:status)',
                ['status' => 'MyStatus'],
                Status::class
            )
            ->willReturn(new Status());

        $statuses = new Statuses($this->mockPrep);
        $statuses->getStatusByName('MyStatus');
    }

    /**
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testGetStatusByIdCallsMySQLProcedure(): void
    {
        $this->mockPrep->expects($this->once())
            ->method('executePreparedFetchAsClass')
            ->with(
                'CALL get_status_by_id(:id)',
                ['id' => '1234567890'],
                Status::class
            )
            ->willReturn(new Status());

        $statuses = new Statuses($this->mockPrep);
        $statuses->getStatusById('1234567890');
    }
}
