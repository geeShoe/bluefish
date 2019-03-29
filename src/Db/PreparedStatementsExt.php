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
 * Date: 3/28/19 - 10:15 PM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Db;

use Geeshoe\DbLib\Core\PreparedStatements;
use Geeshoe\DbLib\DbLibException;

/**
 * Class PreparedStatementsExt
 *
 * @package Geeshoe\BlueFish\Db
 */
class PreparedStatementsExt extends PreparedStatements
{
    /**
     * @param array $dataArray
     * @return array
     */
    public function parseDataArray(array $dataArray): array
    {
        foreach ($dataArray as $column => $value) {
            $sqlColumnPlaceHolderPair[] =  ':' . $column;
            $values[':' . $column] = $value;
        }

        return ['placeHolderArray' => $sqlColumnPlaceHolderPair, 'values' => $values];
    }

    /**
     * @param string $procedure
     * @param array  $params
     *
     * @throws DbLibException
     * @throws \Geeshoe\DbLib\Exceptions\DbLibException
     */
    public function executePreparedStoredProcedure(string $procedure, array $params): void
    {
        $dataArray = $this->parseDataArray($params);

        $sql = 'CALL '.$procedure.'('.implode(', ', $dataArray['placeHolderArray']).');';

        $stmt = $this->prepareStatement($sql);

        foreach ($dataArray['values'] as $placeHolder => $value) {
            $this->bindValue($stmt, $placeHolder, $value);
        }

        if (!$stmt->execute()) {
            throw new DbLibException(
                'Failed to execute prepared statement.'
            );
        }
    }
}
