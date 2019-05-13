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
 * Date: 1/10/19 - 4:08 AM
 */

namespace Geeshoe\BlueFish\Tests\UnitTests\Users;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Users\BlueFish;
use Geeshoe\BlueFish\Model\User;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class BlueFishTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests
 */
class BlueFishTest extends TestCase
{
    /**
     * @var MockObject|PreparedStoredProcedures
     */
    protected $prepStmtMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->prepStmtMock = $this->getMockBuilder(PreparedStoredProcedures::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers \Geeshoe\BlueFish\Users\BlueFish::getUser()
     *
     * @throws BlueFishException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testGetUserCallsMySQLStoredProcedure(): void
    {
        $user = new User();
        $user->password = '1234';

        $this->prepStmtMock->expects($this->once())
            ->method('executePreparedFetchAsClass')
            ->with(
                'CALL get_user_login_credentials(:username);',
                ['username' => 'UnitTest'],
                User::class
            )
            ->willReturn($user);

        $this->expectException(BlueFishException::class);

        $bluefish = new BlueFish($this->prepStmtMock);
        $bluefish->login('UnitTest', '1234');
    }

    /**
     * @throws BlueFishException
     */
    public function testValidateUserThrowsExceptionWhenUserDoesNotExist(): void
    {
        $this->prepStmtMock->method('executePreparedFetchAsClass')
            ->willThrowException(new DbLibPreparedStmtException());
        $this->expectException(BlueFishException::class);
        $this->expectExceptionMessage('User does not exist.');
        $this->expectExceptionCode(101);

        $blueFish = new BlueFish($this->prepStmtMock);
        $blueFish->login('username', 'password');
    }

    /**
     * @throws BlueFishException
     */
    public function testComparePasswordThrowsExceptionWithWrongPassword(): void
    {
        $userObject = new User();
        $userObject->username = 'username';
        $userObject->password = password_hash('password', PASSWORD_DEFAULT);

        $this->prepStmtMock->method('executePreparedFetchAsClass')
            ->willReturn($userObject);

        $this->expectException(BlueFishException::class);
        $this->expectExceptionMessage('Password mismatch.');
        $this->expectExceptionCode(102);

        $blueFish = new BlueFish($this->prepStmtMock);
        $blueFish->login('username', 'wrongPassword');
    }

    /**
     * Data provider for testBlueFishReturnsUserObjectWithGoodCredentials
     *
     * @return array
     */
    public function userObjectDataProvider(): array
    {
        return [
            ['displayName'],
            ['role'],
            ['status']
        ];
    }

    /**
     * @dataProvider userObjectDataProvider
     *
     * @param string $property
     *
     * @throws BlueFishException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testBlueFishReturnsUserObjectWithGoodCredentials(string $property): void
    {
        $userObject = new User();
        $userObject->id = Uuid::uuid4()->toString();
        $userObject->username = 'username';
        $userObject->password = password_hash('password', PASSWORD_DEFAULT);
        $userObject->displayName = 'myName';
        $userObject->role = 'person';
        $userObject->status = 'active';

        $this->prepStmtMock->method('executePreparedFetchAsClass')
            ->willReturn($userObject);

        $blueFish = new BlueFish($this->prepStmtMock);
        $user = $blueFish->login('username', 'password');

        $this->assertSame($userObject->$property, $user->$property);
    }
}
