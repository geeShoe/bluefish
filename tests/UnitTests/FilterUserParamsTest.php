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
 * Date: 1/11/19 - 3:01 AM
 */

namespace Geeshoe\BlueFish\Tests\UnitTests;

use Geeshoe\BlueFish\Exceptions\BlueFishException;
use Geeshoe\BlueFish\Management\FilterUserParams;
use PHPUnit\Framework\TestCase;

/**
 * Class FilterUserParamsTest
 *
 * @package Geeshoe\BlueFish\Tests\UnitTests
 */
class FilterUserParamsTest extends TestCase
{
    /**
     * @throws BlueFishException
     */
    public function testFilterDisplayNameThrowsExceptionWithEmptyString(): void
    {
        $this->expectException(BlueFishException::class);
        $this->expectExceptionCode(106);
        FilterUserParams::filterDisplayName('  ');
    }

    /**
     * @throws BlueFishException
     */
    public function testFilterDisplayNameReturnsFilteredString(): void
    {
        $this->assertSame(
            'my&#60;?php var_dump($_SERVER); ?&#62;userName',
            FilterUserParams::filterDisplayName('my<?php var_dump($_SERVER); ?>userName')
        );
    }

    /**
     * @throws BlueFishException
     */
    public function testFilterPasswordVerifyThrowsExceptionWithEmptyString(): void
    {
        $this->expectException(BlueFishException::class);
        $this->expectExceptionCode(104);
        FilterUserParams::filterPasswordVerify('    ');
    }

    /**
     * @throws BlueFishException
     */
    public function testFilterPasswordVerifyReturnsFilteredString(): void
    {
        $this->assertSame(
            'my&#60;?php var_dump($_SERVER); ?&#62;userName',
            FilterUserParams::filterPasswordVerify('my<?php var_dump($_SERVER); ?>userName')
        );
    }
}
