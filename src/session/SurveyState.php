<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/1/14
 * Time: 11:04 PM
 */

namespace sarhan\survey;

require_once(__DIR__ . "/../import.php");

/**
 * Session data associated with handling a survey request
 */
class SurveyState
{
    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $questionIndex;

    /**
     * @var SurveyExecutionState
     */
    private $surveyExecutionState;

    /**
     * @var Survey
     */
    private $survey;

    function __construct($startTime = null)
    {
        $this->startTime = $startTime;
        if(is_null($this->startTime))
            $this->startTime = time();

        $this->surveyExecutionState = SurveyExecutionState::Unknown();
        $this->questionIndex = null;
        $this->sessionId = null;
    }

    /**
     * @param int $timeout_min
     * @return bool
     */
    public function isExpired($timeout_min)
    {
        if(!is_long($this->startTime) ||
            ! $this->isState(SurveyExecutionState::InProgress()))
            return true;


        $diff = time() - $this->startTime;
        if ($diff > $timeout_min)
            return true;

        return false;
    }

    /**
     * @return int
     */
    public function getQuestionIndex()
    {
        return $this->questionIndex;
    }

    /**
     * @param int $questionIndex
     */
    public function setQuestionIndex($questionIndex)
    {
        $this->questionIndex = $questionIndex;
    }

    /**
     * @param SurveyExecutionState $surveyExecutionState
     */
    public function setSurveyExecutionState(SurveyExecutionState $surveyExecutionState)
    {
        $this->surveyExecutionState = $surveyExecutionState;
    }

    /**
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @param SurveyExecutionState $state
     *
     * @return bool
     */
    public function isState(SurveyExecutionState $state)
    {
        return $this->surveyExecutionState == $state;
    }

    /**
     * Returns the question for $this->getQuestionIndex()
     * @return string
     */
    public function getQuestion()
    {
        $qobj = $this->getQuestionObj();

        if(is_null($qobj))
            return null;

        $question = $qobj->getQuestion();

        //Append the question hint.
        $question .= $qobj->getType()->getQuestionHint();

        return $question;
    }

    /**
     * Returns the SurveyQuestion for $this->getQuestionIndex()
     * @return null|SurveyQuestion
     */
    public function getQuestionObj()
    {
        if($this->questionIndex >= count($this->survey->getQuestions()))
        {
            return null;
        }

        return $this->survey->getQuestions()[$this->questionIndex];
    }

    /**
     * True if an id has already been set.
     * @return bool
     */
    public function hasId()
    {
        return $this->sessionId && !is_null($this->sessionId);
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

}

?> 