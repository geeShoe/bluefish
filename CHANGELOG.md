## BlueFish ChangeLog
*BlueFish intends to follows [Semantic Versioning 2.0.0](https://semver.org/)*

As BlueFish is currently in active development, drastic changes are to be expected
during the v0.x.x point releases. Expect stability in the 1.x.x releases.

### v0.4.0
#### Features -
* Added ability to add roles
* Added ability to get roles by id or name
* Added ability to add status'
* Added ability to get status by id or name

#### Fixes -
* Changelog typo (v2.0.0 is v0.2.0)
* MySQL Function UuidFromBin malformed UUID
* Refactored MySQL Function UuidToBin

#### Composer Updates -
* Geeshoe Helpers v0.1.2 => v0.2.0

#### Misc -
* blueFishTables.sql renamed to tables.sql
* Refactored Unit & Functional Tests
* Added test utility traits and interfaces
* Improved functional test database connection logic
* Improved functional test speed

### v0.3.0
*Released - 2019-03-29*

* Heavily modified from 0.1.x & 0.2.x released.
* Tables now utilize UUID's
* Maria/MySQL scripts now available to setup database for BlueFish.
* Stored procedures, views, and functions are utilized in the database to improve
any possible security vulnerabilities.
* Unit/Functional test coverage expanded and performance improved.
* Upgraded Geeshoe/dblib dependency requirement.

### v0.2.0

### v0.1.0
*Released - 2018-11-16*

Initial versioned release of BlueFish