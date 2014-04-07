<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/30/14
 * Time: 7:51 PM
 */

namespace sarhan\survey;

/**
 * @Entity @Table(name="SurveyAnswer")
 *
 * @NamedNativeQueries({
 *      @NamedNativeQuery(
 *          name           = "find-answers-by-surveyId",
 *          resultClass    = "SurveyAnswer",
 *          query          = "select a.* from SurveyAnswer a join survey_questions q on a.surveyQuestionId = q.question_id where q.survey_id = ?"
 *      ),
 * })
 **/
class SurveyAnswer
{
    const NAME = 'sarhan\survey\SurveyAnswer';

    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;
    /**
     * @Column(type="integer")
     * @var int
     */
    protected $surveyQuestionId;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $answeredBy;

    /**
     * @Column(type="string", length=500)
     * @var string
     */
    protected $answer;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $created;

    function __construct()
    {
        $this->created = new \DateTime();
    }


    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getSurveyQuestionId()
    {
        return $this->surveyQuestionId;
    }

    /**
     * @param int $surveyQuestionId
     */
    public function setSurveyQuestionId($surveyQuestionId)
    {
        $this->surveyQuestionId = $surveyQuestionId;
    }

    /**
     * @return string
     */
    public function getAnsweredBy()
    {
        return $this->answeredBy;
    }

    /**
     * @param string $answeredBy
     */
    public function setAnsweredBy($answeredBy)
    {
        $this->answeredBy = $answeredBy;
    }



}

?> 