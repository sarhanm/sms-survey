
[![Build Status](https://travis-ci.org/sarhanm/sms-survey.png)](https://travis-ci.org/sarhanm/sms-survey)


# WORK IN PROGRESS. DO NOT USE. EVERYTHING IS SUBJECT TO CHANGE.

Once the project is complete, this library will be able:

1. Handle twilio SMS requests and produce a survey and interact with the user
2. Provide a common UI that can be used to administer the active surveys
3. Provide reporting for completed/running surveys

This project is really meant for non-profit organizations who would like feedback from their constituents via SMS.


### Configuration

TODO

### Generate mysql code
php  vendor/bin/doctrine orm:schema-tool:create  --dump-sql
