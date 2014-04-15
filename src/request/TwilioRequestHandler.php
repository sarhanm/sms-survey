<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/15/14
 * Time: 12:19 AM
 */

namespace sarhan\survey;

require_once __DIR__.'/../import.php';

class TwilioRequestHandler
{
    /**
     * @param string $twilioAuthToken
     *
     * @return null|SurveyResponse
     */
    public function handleRequest($twilioAuthToken)
    {
        $service = new SurveyRequestService();
        if(self::validateRequest($twilioAuthToken))
        {
            $success = $service->service();
            return $service->getResponse();
        }

        return null;
    }

    /**
     * @param Twilio $authToken
     *
     * @return bool
     */
    private function validateRequest($authToken)
    {
        $validator = new \Services_Twilio_RequestValidator($authToken);
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $postVars = $_POST;
        $signature = $_SERVER["HTTP_X_TWILIO_SIGNATURE"];
        return $validator->validate($signature, $url, $postVars);
    }
}

?> 