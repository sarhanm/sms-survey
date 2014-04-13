
[![Build Status](https://travis-ci.org/sarhanm/sms-survey.png)](https://travis-ci.org/sarhanm/sms-survey)

# Manual Setup

### PHP setup

1. Clone this repo.

2. Run composer to install all dependencies
    
```bash
php composer.phar install
```    

Note: You can download composer.phar from https://getcomposer.org/

### Database Configuration
1. Add a file called "db-config.php" to `survey-sms/config`

File should contain the following:
    
```php
<?php
if(!defined('SARHAN_SURVEY'))
    die("Improper use of file!");

class SurveyDBConfig
{
    private $test = array(
        'driver' => 'pdo_sqlite',
        'memory' => true);

    private $production = array(
        'driver' => 'pdo_mysql', //or whatever driver you will be using.
        'dbname'=>'your_dbname',
        'user'=>'your_dbuser',
        'password'=>'your_dbpassword',
        'host'=>'your_dbhost'
        );

    public function getConfig($isTest = false)
    {
        if($isTest)
            return $this->test;
        return $this->production;
    }

}
?>
```

Driver options can be found on [Doctrin's documentation page](http://doctrine-dbal.readthedocs.org/en/latest/reference/configuration.html#driver)

2. Generate the SQL code to generate the tables.

```bash
php vendor/bin/doctrine orm:schema-tool:create  --dump-sql
```

Take the output and run in your database.

You can alternatively like doctrine execute the sql statements for you (as long as you properly configured your database in step 1 above).

```bash
php vendor/bin/doctrine orm:schema-tool:create
```


### Test Manual Setup

```bash    
php vendor/bin/phpunit tests/
```

NOTE: By default, this does not test your database setup as the tests are run against a in-memory database. If you would like to test against a real database, just change the `$test` in `db-config.php` to reference your database.

### Twilio Setup

Add `http://your-host-name.com/path-to-git-repo/survey-sms/src/request/SurveyRequestService.php` to your twilio Messaging callback settings

### Admin pages.

Admin pages are available to manage your surveys and view reporting.

**Warning: These pages are rough, quick and dirty implementations. They need a lot of work.**

`http://your-host-name.com/path-to-git-repo/survey-sms/src/web/admin`

# Downloading this package via composer

TODO: Go through how to properly configure if the package is downloaded via composer.
