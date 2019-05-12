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

use Geeshoe\BlueFish\Model\Role;
use Geeshoe\DbLib\Core\PreparedStoredProcedures;

/**
 * Class Roles
 *
 * @package Geeshoe\BlueFish\Management
 * @author  Jesse Rushlow <jr@geeshoe.com>
 * @link    https://geeshoe.com
 */
class Roles
{
    /**
     * @var PreparedStoredProcedures
     */
    public $prepStmt;

    /**
     * Roles constructor.
     *
     * @param PreparedStoredProcedures $preparedStoredProcedures
     */
    public function __construct(PreparedStoredProcedures $preparedStoredProcedures)
    {
        $this->prepStmt = $preparedStoredProcedures;
    }

    /**
     * @param Role $role
     *
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     */
    public function addRole(Role $role): void
    {
        $this->prepStmt->executePreparedStoredProcedure(
            'add_role',
            [
                'id' => $role->id,
                'role' => $role->role
            ]
        );
    }

    /**
     * @param string $name
     *
     * @return Role
     *
     * @throws \Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException
     */
    public function getRoleByName(string $name): Role
    {
        return $this->prepStmt->executePreparedFetchAsClass(
            'CALL get_role_by_name(:role);',
            ['role' => $name],
            Role::class
        );
    }
}
