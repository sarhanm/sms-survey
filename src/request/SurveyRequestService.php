<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:36 PM
 */

namespace sarhan\survey;

require_once(__DIR__ . "/../import.php");

class SurveyRequestService
{
    /**
     * @var \Logger
     */
    private $log;

    /**
     * @var int Milliseconds before we abort the survey
     */
    const SURVEY_TIME_OUT = 600000;

    /**
     * Request key. The value of the answer from twilio
     * @var string
     */
    const BODY_KEY = "Body";

    const SESSION_KEY = "survey_session";

    /**
     * @var SurveyState
     */
    private $surveyState;

    /**
     * @var $_SESSION
     */
    private $session;

    /**
     * @var $_REQUEST
     */
    private $request;

    /**
     * The response obj that has the response that should
     * be returned to the user
     * @var SurveyResponse
     */
    private $response;

    /**
     * @var SurveyManager
     */
    private $surveyManager;

    /**
     * @var AnswerValidatorProvider
     */
    private $answerValidatorProvider;

    /**
     * @param array $session_object
     * @param array $request
     */
    function __construct(&$session_object = null, &$request = null)
    {

        $this->log = \Logger::getLogger(__CLASS__);

        $this->surveyManager = new SurveyManager();
        $this->answerValidatorProvider = new AnswerValidatorProvider();

        // We do this for testing purposes, so we can mimic a session
        if(!isset($session_object))
            $this->session = &$_SESSION;
        else
            $this->session = &$session_object;

        if(!isset($request))
            $this->request = &$_REQUEST;
        else
            $this->request = &$request;

        if(array_key_exists(self::SESSION_KEY, $this->session))
        {
            $this->surveyState = $this->session[self::SESSION_KEY];
            //var_dump($this->surveyState);
        }
        else
        {
            $this->reset();
        }
    }

    /**
     * @param Survey $survey
     */
    private function reset(Survey $survey = null)
    {
        unset($this->session[self::SESSION_KEY]);

        //Lets create and add to the session
        $this->surveyState = new SurveyState();

        if(!is_null($survey))
        {
            $this->surveyState->setSurvey($survey);
            $this->surveyState->setSurveyExecutionState(SurveyExecutionState::InProgress());
        }

        $this->session[self::SESSION_KEY] = $this->surveyState;
        //var_dump($this->session[self::SESSION_KEY]);
    }

    /**
     * @param string $response
     */
    private function response($response)
    {
        $res = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $res .= "<Response>\n";
        $res .= "<Message>$response</Message>\n";
        $res .= "</Response>";

        $this->response  = new SurveyResponse();
        $this->response->setHeader("content-type: text/xml");
        $this->response->setContent($res);
    }


    /**
     * public modifier since its used for tests
     *
     * private function.
     * @return bool
     */
    public function shouldService()
    {
        //We only service this request if we are in survey mode.
        $body = $this->getRequestBody();

        if(is_null($body))
            return false;

        if(in_array(strtolower($body), array("exit", "quit","cancel")))
        {
            $this->reset();
            return false;
        }


        if(is_string($body))
        {
            $surveyName = strtolower($body);
            $survey = $this->surveyManager->getSurveyByName($surveyName);
            if($survey && !is_null($survey))
            {
                $this->reset($survey);
                return true;
            }
        }

        //Not in a valid survey
        if($this->surveyState->isState(SurveyExecutionState::Unknown()))
        {
            return false;
        }


        if($this->surveyState->isExpired(self::SURVEY_TIME_OUT))
        {
            $this->reset();
            return false;
        }

        return true;
    }

    /**
     * Public entry point to service
     * @return boolean
     */
    public function service()
    {
        if(!$this->shouldService())
        {
            $this->response("Sorry, could not find a survey by that name.");
            return false;
        }


        $qindex = $this->surveyState->getQuestionIndex();

        //No qindex exists, or we are at the first question.
        if(is_null($qindex))
        {
            $qindex = 0;
            $this->surveyState->setQuestionIndex($qindex);

            //return first question
            $question = $this->surveyState->getQuestion();
            $this->response($question);
            return true;
        }

        $question = $this->surveyState->getQuestionObj();

        $validator = $this->answerValidatorProvider->getValidator($question->getType());

        $answer = $this->getRequestBody();

        if($validator->isValid($answer))
        {
            //move to next question
            $qindex++;

            //persist our question index state
            $this->surveyState->setQuestionIndex($qindex);

            if(!$this->surveyState->hasId())
            {
                $from = session_id();
                if (array_key_exists('From', $this->request))
                    $from = $this->request['From'];
                $this->surveyState->setId($from);
            }

            $answer = $validator->normalize($answer);
            $this->persistAnswer($this->surveyState->getId(),$question,$answer);
        }
        else
        {
            $helperText = $validator->getHelperText();
            $response = "Sorry, we couldn't understand your response. $helperText";
            $this->response($response);
            return true;
        }

        $next_question = $this->surveyState->getQuestion($qindex);
        if(is_null($next_question))
        {
            //no more questions. Send thank you message.
            $this->surveyState->setSurveyExecutionState(SurveyExecutionState::Completed());
            $next_question = $this->surveyState->getSurvey()->getThankYouMessage();
        }

        $this->response($next_question);

        return true;
    }


    /**
     * @param string $from
     * @param SurveyQuestion $question
     * @param string $answerString
     */
    private function persistAnswer($from, SurveyQuestion $question,$answerString)
    {
        $answer = new SurveyAnswer();
        $answer->setAnswer($answerString);
        $answer->setAnsweredBy($from);

        //Persist the answer
        $this->surveyManager->addAnswer($question->getId(),$answer);
    }

    private function getRequestBody()
    {
        if(!array_key_exists(self::BODY_KEY, $this->request))
            return NULL;

        return $this->request[self::BODY_KEY];
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return SurveyResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return SurveyState
     */
    public function getSurveyState()
    {
        return $this->surveyState;
    }

}

?> 