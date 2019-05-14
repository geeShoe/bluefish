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

namespace Geeshoe\BlueFish\Tests;

use Geeshoe\DbLib\Core\PreparedStoredProcedures;

/**
 * Class Connection
 *
 * @package Geeshoe\BlueFish\Tests
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class Connection
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var PreparedStoredProcedures
     */
    protected $prepProc;

    /**
     * @return \PDO
     *
     * @throws \PDOException
     */
    public function getPDO(): \PDO
    {
        if (!is_object($this->pdo)) {
            $this->establishPDO();
        }

        return $this->pdo;
    }

    /**
     * @return PreparedStoredProcedures
     *
     * @throws \PDOException
     */
    public function getPrepProc(): PreparedStoredProcedures
    {
        if (!is_object($this->prepProc)) {
            $this->createPrepProc();
        }

        return $this->prepProc;
    }

    /**
     * @throws \PDOException
     */
    public function establishPDO(): void
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
     * @throws \PDOException
     */
    protected function createPrepProc(): void
    {
        $pdo = $this->getPDO();

        $this->prepProc = new PreparedStoredProcedures($pdo);
    }
}
