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
 * Date: 3/28/19 - 10:08 PM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Helpers;

/**
 * Class UuidFilters
 *
 * @package Geeshoe\BlueFish\Helpers
 */
class UuidFilters
{
    /**
     * Replace spaces with dashes, filter out all non alpha numeric's, and
     * return lower case string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function filterAlphaNumDash(string $string): string
    {
        return preg_replace(
            '/[^a-z0-9-]/',
            '',
            strtolower(self::filterSpaceToDash($string))
        );
    }

    /**
     * @param string $string
     * @return string
     */
    public static function filterSpaceToDash(string $string): string
    {
        return preg_replace('/[\s]/', '-', $string);
    }
}
