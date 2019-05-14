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

use Geeshoe\Helpers\Files\FileHelpers;

/**
 * Class Database
 *
 * @package Geeshoe\BlueFish\Tests
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class Database
{
    /**
     * @var \PDO
     */
    public $pdo;

    /**
     * @var string
     */
    public $schema;

    /**
     * @var string
     */
    public $sqlDir;

    /**
     * @var array
     */
    public $sqlFiles = [];

    /**
     * Database constructor.
     *
     * @param \PDO        $pdo
     * @param string|null $sqlDir
     */
    public function __construct(\PDO $pdo, string $sqlDir = null)
    {
        $this->pdo = $pdo;
        $this->schema = getenv('GSD_BFTD_DATABASE');

        $dir = dirname(__DIR__, 1) . '/sql/';

        if ($sqlDir !== null) {
            $dir = $sqlDir;
        }

        $this->sqlDir = $dir;
    }

    public function createSchema(): void
    {
        $this->pdo->exec(
            "CREATE SCHEMA IF NOT EXISTS $this->schema;"
        );
    }

    public function dropSchema(): void
    {
        $this->pdo->exec("DROP SCHEMA $this->schema;");
    }

    /**
     * Run .sql in the provided sql directory
     */
    public function execSQLFiles()
    {
        $this->pdo->exec("USE $this->schema;");

        if (empty($this->sqlFiles)) {
            $this->parseFiles();
        }

        foreach ($this->sqlFiles as $file) {
            $this->pdo->exec($file);
        }
    }

    /**
     * Check file is readable and get contents.
     */
    protected function parseFiles(): void
    {
        $keys = ['tables', 'functions', 'views', 'procedures'];

        foreach ($keys as $key) {
            if (FileHelpers::checkFileIsR("$this->sqlDir$key.sql")) {
                $this->sqlFiles[] = file_get_contents("$this->sqlDir$key.sql");
            }
        }
    }
}
