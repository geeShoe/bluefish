<?php
/**
 * Copyright 2018 Jesse Rushlow - Geeshoe Development
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
 * Date: 11/16/18 - 1:21 PM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Exceptions;

use Throwable;

/**
 * Class BlueFishException
 *
 * @package Geeshoe\BlueFish\Exceptions
 */
class BlueFishException extends \Exception
{
    /**
     * BlueFishException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function usernameEmpty(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'Username cannot be empty.',
            100,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function userDoesNotExist(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'User does not exist.',
            101,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function passwordMismatch(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'Password mismatch.',
            102,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function unableToLoginFallBack(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'Unable to login. Contact administrator.',
            103,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function passwordEmpty(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'Password cannot be empty.',
            104,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function uuidProblem(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'Problem with creating a UUID.',
            105,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function displayNameEmpty(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'Display name cannot be empty.',
            106,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws BlueFishException
     */
    public static function dbFailure(Throwable $previous = null): void
    {
        throw new BlueFishException(
            'Db query failed.',
            107,
            $previous
        );
    }
}
