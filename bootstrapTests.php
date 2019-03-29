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

require __DIR__ . '/vendor/autoload.php';

$dotFile = __DIR__ . '/.env.testing.local';

if (!is_file($dotFile) || !is_readable($dotFile)) {
    throw new RuntimeException(
        'The functional test suite requires a .env.testing.local file.'
    );
}

$env = new Symfony\Component\Dotenv\Dotenv();
$env->load($dotFile);

define('ROLEUUID', \Ramsey\Uuid\Uuid::uuid4()->toString());
define('STATUSUUID', \Ramsey\Uuid\Uuid::uuid4()->toString());
