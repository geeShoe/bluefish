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
 * Date: 1/11/19 - 4:12 AM
 */

namespace Geeshoe\BlueFish\Tests\UnitTests\Management;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Management\AddUser;
use Geeshoe\BlueFish\Model\UserProspect;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use Geeshoe\DbLib\Exceptions\DbLibException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class AddUserTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests
 */
class AddUserTest extends TestCase
{
    /**
     * @var MockObject|PreparedStoredProcedures
     */
    public $prepStmtMock;

    /**
     * {@inheritDoc}
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->prepStmtMock = $this->getMockBuilder(PreparedStoredProcedures::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @throws BlueFishException
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testComparePasswordsThrowsExceptionIfPasswordsDontMatch(): void
    {
        $addUser = new AddUser($this->prepStmtMock);
        $this->expectException(BlueFishException::class);
        $this->expectExceptionCode(102);

        $user = new UserProspect();
        $user->username = 'someUser';
        $user->password = 'somePass';
        $user->passwordVerify = 'passSome';
        $user->displayName = 'someDisplay';
        $user->role = Uuid::uuid4()->toString();
        $user->status = Uuid::uuid4()->toString();

        $addUser->createUserAccount($user);
    }

    /**
     * @throws BlueFishException
     * @throws \InvalidArgumentException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testCreateUserAccountThrowsExceptionUponFailureAtTheDBLevel(): void
    {
        $this->prepStmtMock->method('executePreparedStoredProcedure')
            ->willThrowException(new DbLibException('Testing'));

        $addUser = new AddUser($this->prepStmtMock);

        $userProspect = new UserProspect();
        $userProspect->username = 'Test';
        $userProspect->password = 'myPass';
        $userProspect->passwordVerify = 'myPass';
        $userProspect->displayName = 'someDisplay';
        $userProspect->role = Uuid::uuid4()->toString();
        $userProspect->status = Uuid::uuid4()->toString();

        $this->expectException(BlueFishException::class);
        $addUser->createUserAccount($userProspect);
    }
}
