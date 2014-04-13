
[![Build Status](https://travis-ci.org/sarhanm/sms-survey.png)](https://travis-ci.org/sarhanm/sms-survey)

# Manual Setup

### PHP setup

1. Clone this repo.

2. Run composer to install all dependencies

    ```
    php composer.phar install
    ```
Note: You can download composer.phar from https://getcomposer.org/

### Database Configuration
1. Add a file called "db-config.php" to ```survey-sms/config```

File should contain the following:
    
    ```php
    
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

    }```

Driver options can be found on [Doctrin's documentation page](http://doctrine-dbal.readthedocs.org/en/latest/reference/configuration.html#driver)

2. Generate the SQL code to generate the tables.

    ```php vendor/bin/doctrine orm:schema-tool:create  --dump-sql```

### Test Manual Setup

    ```php vendor/phpunit/phpunit/phpunit tests/```

### Twilio Setup

Add ```http://your-host-name.com/path-to-git-repo/survey-sms/src/request/SurveyRequestService.php``` to your twilio Messaging callback settings

### Admin pages.

Admin pages are available to manage your surveys and view reporting.

**Warning: These pages are rough, quick and dirty implementations. They need a lot of work.**

```http://your-host-name.com/path-to-git-repo/survey-sms/src/web/admin```

