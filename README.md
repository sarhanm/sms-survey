
[![Build Status](https://travis-ci.org/sarhanm/sms-survey.png)](https://travis-ci.org/sarhanm/sms-survey)

# Install via Composer

```json
    "require": {
        "sarhanm/sms-survey": "*"        
    }
```


# Manual Setup

### PHP setup

1. Clone this repo.

2. Run composer to install all dependencies
    
```bash
php composer.phar install
```    

Note: You can download composer.phar from https://getcomposer.org/

### Database Configuration

#### 1. Create a file named "survey-db-config.php"

File should contain the following:
    
```php
<?php
if(!defined('SARHAN_SURVEY'))
    die("Improper use of file!");

return array(
    "test" => array(
        'driver' => 'pdo_sqlite',
        'memory' => true)
    ,
    "production" => array(
        'driver' => 'pdo_mysql',
        'dbname'=>'your_dbname',
        'user'=>'your_dbuser',
        'password'=>'you_dbpasswd',
        'host'=>'your_dbhost'
    )
);
?>
```

Driver options can be found on [Doctrin's documentation page](http://doctrine-dbal.readthedocs.org/en/latest/reference/configuration.html#driver)

This file will be discovered in the following way

1.We'll look to see if a PHP named constant is defined with the path to the above file. 
```php
define('SURVEY_DB_CONFIG_PATH', 'path/to/db/config/survey-db-config.php');
```

2.Look for the file `survey-db-config.php` in the `include_path` via `stream_resolve_include_path` 

3.Look for the file `survey-db-config.php` in the `cwd()` or in `cwd()/config`.


#### 2. Generate the SQL code to create DB tables.

```bash
php vendor/bin/doctrine orm:schema-tool:create  --dump-sql
```

Take the output and run in your database.

You can alternatively let doctrine execute the sql statements for you as long as you properly configured your database.

```bash
php vendor/bin/doctrine orm:schema-tool:create
```

You can see other options by passing a `-h` option to the above command.

### Test Manual Setup

```bash    
php vendor/bin/phpunit tests/
```

NOTE: By default, this does not test your database setup as the tests are run against a in-memory database. If you would like to test against a real database, just change the `$test` in `survey-db-config.php` to reference your database.

### Twilio Setup

In your Twilio Request Handler, add the following

```php
//Order does matter. Do the require before the session start so (de)serialization is aware
//of all the loaded classes.
require_once("survey-sms/src/request/TwilioRequestHandler.php");
session_start();

$twilioRequest = new \sarhan\survey\TwilioRequestHandler();

// $twilioAccountAuthToken is your auth token given to you by twilio
$response = $twilioRequest->handleRequest($twilioAccountAuthToken);

if(!is_null($response))
{
    header($response->getHeader());
    echo $response->getContent();
}
else
{
    echo "Invalid Twilio Request";
}
```

### Admin pages.

Admin pages are available to manage your surveys and view reporting.

**Warning: These pages are rough, quick and dirty implementations. They need a lot of work. They are also not secure. You should put some password proction on those directories/files**

`http://your-host-name.com/path-to-git-repo-survey-sms/src/web/admin`

