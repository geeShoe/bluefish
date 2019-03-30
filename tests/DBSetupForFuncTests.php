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
 * Date: 1/11/19 - 5:02 AM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Tests;

use Geeshoe\DbLib\Core\PreparedStatements;

/**
 * Trait DBSetupForFuncTests
 *
 * @package Geeshoe\BlueFish\Tests
 */
trait DBSetupForFuncTests
{
    /**
     * Create a test DB and tables for functional tests.
     *
     * Call with PHPUnit's setUp() method.
     *
     * @return PreparedStatements
     *
     * @throws \Exception
     */
    public static function getDbSetup(): PreparedStatements
    {
        return DbTestBootStrap::setupDb();
    }

    /**
     * Destroy the test DB used for functional tests.
     *
     * Call with PHPUnit's tearDown() method.
     */
    public static function tearDownDB(): void
    {
        $setup = new DBSetup();
        $setup->tearDownDB();
    }
}
