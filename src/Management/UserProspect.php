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
 * Date: 1/10/19 - 9:21 AM
 */
declare(strict_types=1);

namespace Geeshoe\BlueFish\Management;

use Geeshoe\BlueFish\Users\User;

/**
 * Class UserProspect
 *
 * @package Geeshoe\BlueFish\Management
 */
class UserProspect extends User
{
    /**
     * @var string
     */
    public $passwordVerify;
}
