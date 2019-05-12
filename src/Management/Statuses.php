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

namespace Geeshoe\BlueFish\Management;

use Geeshoe\BlueFish\Model\Status;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;

/**
 * Class Statuses
 *
 * @package Geeshoe\BlueFish\Management
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class Statuses
{
    /**
     * @var PreparedStoredProcedures
     */
    public $prepStmt;

    /**
     * Status constructor.
     *
     * @param PreparedStoredProcedures $preparedStoredProcedures
     */
    public function __construct(PreparedStoredProcedures $preparedStoredProcedures)
    {
        $this->prepStmt = $preparedStoredProcedures;
    }

    /**
     * @param Status $status
     *
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     */
    public function addStatus(Status $status): void
    {
        $this->prepStmt->executePreparedStoredProcedure(
            'add_status',
            [
                'id' => $status->id,
                'status' => $status->status
            ]
        );
    }

    /**
     * @param string $name
     *
     * @return Status
     *
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     */
    public function getStatusByName(string $name): Status
    {
        $status = $this->prepStmt->executePreparedFetchAsClass(
            'CALL get_status_by_name(:status)',
            ['status' => $name],
            Status::class
        );

        return $status;
    }
}
