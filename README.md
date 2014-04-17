
[![Build Status](https://travis-ci.org/sarhanm/sms-survey.png)](https://travis-ci.org/sarhanm/sms-survey)

# SMS Survey Manager

## About this Library
Twilio is a text messaging service which allows you to interact with your users/clients/community via text messages. No need for smart phones or building apps. 

This library uses twilio to conduct surveys. This project was born out of a need for non-profit groups to survey their communities for feed back (mostly after an event/class/seminars) without paying lots and lots of money. Twilio.org offers a discounted rate for 501(c)3 organizations, which makes it ideal.

## Features

### Manage Survey

You can create as many surveys as you want, all running at the same time. Surveys can be created via the admin interface. The admin interface also gives you pie and bar charts of your survey answers to easily digest the results. 

### Survey Questions

Survey questions can be of the following types

1. Text
2. Yes/No
3. 5 start rating.

You can have as many questions as you want per survey. Although you should minimize questions to 2 or 3.

### Starting a Survey

Surveys are activated by texting the survey name to your assigned twilio phone number. The user than has 10 minutes to complete the survey.


#Installation

### Install via Composer

```json
    "require": {
        "sarhanm/sms-survey": "1.0.2"        
    }
```

### Install Manually

1. Clone this repo.

2. Run composer to install all dependencies
    
```bash
php composer.phar install
```    

Note: You can download composer.phar from https://getcomposer.org/

#### Test Manual Setup

```bash    
php vendor/bin/phpunit tests/
```

NOTE: By default, this does not test your database setup as the tests are run against a in-memory database. If you would like to test against a real database, just change the `$test` in `survey-db-config.php` to reference your database. 

# Database Configuration

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

Driver options can be found on [Doctrin's documentation page.](http://doctrine-dbal.readthedocs.org/en/latest/reference/configuration.html#driver)

This file will be discovered in the following way

1. Look for a PHP named constant with the path to the above file. Make sure its defined before you instantiate any of the survey classes.

    ```php
    define('SURVEY_DB_CONFIG_PATH', 'path/to/db/config/survey-db-config.php');
    ```

2. Look for the file `survey-db-config.php` in the `include_path` via `stream_resolve_include_path` 

3. Look for the file `survey-db-config.php` in the `cwd()` or in `cwd()/config`.

#### 2. Generate the SQL code to create DB tables.

```bash
php vendor/bin/doctrine orm:schema-tool:create  --dump-sql
```

Take the output and run in your database.

You can alternatively let doctrine execute the sql statements for you as long as you've properly configured your database. Usually you'll want to run dump-sql first to verify things are setup correctly before the below command.

```bash
php vendor/bin/doctrine orm:schema-tool:create
```

You can see other options by passing a `-h`  to the above command.

# Twilio Setup

**NOTE: You will need to create and setup your own twilio.com account**

In your Twilio Request Handler, add the following

```php
//Order does matter. 
//Do the require before the session start so (de)serialization is aware
//of all the loaded classes.
require_once("survey-sms/src/request/TwilioRequestHandler.php");
session_start();

$twilioRequest = new \sarhan\survey\TwilioRequestHandler();

$twilioAccountAuthToken = "myTwilioAuthToken";

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

# Admin pages

Admin pages are available to manage your surveys and view reporting.

**Warning: These pages are rough, quick and dirty implementations. They need a lot of work. They are also not secure. You should put some password protection on those directories/files**

`http://your-host-name.com/path-to-survey-sms/src/web/admin`

