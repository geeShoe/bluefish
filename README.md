# BlueFish
BlueFish is a PHP user authentication library.

Please note that this project is in initial development and as such, some 
documentation may be incomplete. 

## Getting Started

BlueFish is intended to be fully compliant with 
[PSR-1](https://www.php-fig.org/psr/psr-1/),
[PSR-2](https://www.php-fig.org/psr/psr-2/),
 & [PSR-4](https://www.php-fig.org/psr/psr-4/)

### Prerequisites

* PHP 7.2+
* [PDO_MYSQL extension](http://php.net/manual/en/ref.pdo-mysql.php)
* MariaDb / MySQL

To check if the PDO MySQL driver is enabled, run the following command in the CLI or
 on your web server. (Do not make phpinfo() accessible to anyone!)

```
phpinfo(); <-- Use in script on webserver.
php -i <-- Use with CLI
```
and ensure PDO drivers lists MySQL. If it doesn't or you cannot find any mention of PDO in phpinfo(). You may need to 
recompile PHP using:
```
./configure --with-pdo-mysql
```

### Installing

To add BlueFish to your project, run:

```
composer require geeshoe/bluefish
```

If you prefer to use the development branch of BlueFish, use following line of code in the composer.json file.

```
composer require geeshoe/bluefish dev-develop
```

### Configure

BlueFish itself requires no configuration. However, BlueFish utilizes DbLib to handle
database queries. As such DbLib needs to be configured with database credential's. 
See DbLib's [documentation](https://geeshoe.com/projects/DbLib/docs) for further instruction's.

### Documentation

API & usage documentation is soon to come.

### Authors

* **Jesse Rushlow** - *Lead developer* - [geeShoe Development](http://geeshoe.com)

Source available at (https://github.com/geeshoe)

For questions, comments, or rant's, drop me a line at 
```
jr (at) geeshoe (dot) com
```