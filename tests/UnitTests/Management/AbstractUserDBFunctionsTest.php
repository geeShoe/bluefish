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

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Management\AbstractUserDBFunctions;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractUserDBFunctionsTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests\Management
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class AbstractUserDBFunctionsTest extends TestCase
{
    /**
     * @var MockObject|PreparedStoredProcedures
     */
    public $mockPrep;

    /**
     * @var AbstractUserDBFunctions
     */
    public $class;

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
     * @return AbstractUserDBFunctions
     */
    public function getClass(): AbstractUserDBFunctions
    {
        return $this->class = new class($this->mockPrep) extends AbstractUserDBFunctions
        {
            public function name(string $name)
            {
                return $this->getUserByUsername($name);
            }

            public function id(string $id)
            {
                return $this->getUserByID($id);
            }
        };
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testGetUserByUsernameCallsMySQLStoredProcedure(): void
    {
        $this->mockPrep->expects($this->once())
            ->method('executePreparedFetchAsClass')
            ->with(
                'CALL get_user_account_by_username(:username);',
                ['username' => 'UnitTest'],
                User::class
            )
            ->willReturn(new User());

        $class = $this->getClass();

        $class->name('UnitTest');
    }

    /**
     * @return array
     */
    public function userDoesNotExistDataProvider(): array
    {
        return [
            'ByUsername' => ['name'],
            'ById' => ['id']
        ];
    }

    /**
     * @dataProvider userDoesNotExistDataProvider
     *
     * @param string $method
     */
    public function testExceptionThrownIfUserDoesNotExist(string $method): void
    {
        $this->mockPrep->method('executePreparedFetchAsClass')
            ->willThrowException(new DbLibPreparedStmtException());

        $class = $this->getClass();

        $this->expectException(BlueFishException::class);
        $class->$method('something');
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testGetUserByIdCallsMySQLStoredProcedure(): void
    {
        $this->mockPrep->expects($this->once())
            ->method('executePreparedFetchAsClass')
            ->with(
                'CALL get_user_account_by_id(:id);',
                ['id' => '1234567890'],
                User::class
            )
            ->willReturn(new User());

        $class = $this->getClass();

        $class->id('1234567890');
    }
}
