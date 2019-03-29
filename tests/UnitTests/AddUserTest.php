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

namespace Geeshoe\BlueFish\Tests\UnitTests;

use Geeshoe\BlueFish\Db\PreparedStatementsExt;
use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Management\AddUser;
use Geeshoe\BlueFish\Management\UserProspect;
use Geeshoe\DbLib\Core\PreparedStatements;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AddUserTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests
 */
class AddUserTest extends TestCase
{
    /**
     * @var MockObject|PreparedStatementsExt
     */
    public $prepStmtMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->prepStmtMock = $this->getMockBuilder(PreparedStatementsExt::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @throws BlueFishException
     */
    public function testComparePasswordsThrowsExceptionIfPasswordsDontMatch(): void
    {
        $addUser = new AddUser($this->prepStmtMock);
        $this->expectException(BlueFishException::class);
        $this->expectExceptionCode(102);

        $user = new \Geeshoe\BlueFish\Model\UserProspect();
        $user->username = 'someUser';
        $user->password = 'somePass';
        $user->passwordVerify = 'passSome';
        $user->displayName = 'someDisplay';
        $user->role = ROLEUUID;
        $user->status = STATUSUUID;

        $addUser->createUserAccount($user);
    }
}
