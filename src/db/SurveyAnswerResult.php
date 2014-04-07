<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/5/14
 * Time: 7:34 PM
 */

namespace sarhan\survey;

/**
 *
 *
 * Class SurveyAnswerResult
 * @package sarhan\survey
 */
class SurveyAnswerResult
{
    /**
     * @var array
     */
    private $map;

    /**
     * @param SurveyAnswer[] $answers
     */
    function __construct($answers)
    {
        $this->map = array();

        foreach($answers as $ans)
        {
            if(!array_key_exists($ans->getSurveyQuestionId(),$this->map))
            {
                $this->map[$ans->getSurveyQuestionId()] = array();
            }

            $this->map[$ans->getSurveyQuestionId()][] = $ans;
        }
    }

    /**
     * @param $qid
     * @return SurveyAnswer[]
     */
    public function getAnswers($qid)
    {
        if(array_key_exists($qid, $this->map))
        {
            $o = new \ArrayObject($this->map[$qid]);
            return $o->getArrayCopy();
        }

        return null;
    }

    /**
     * @return int
     */
    public function getTotalAnswers()
    {
        $count = 0;
        foreach($this->map as $val)
        {
            $count += count($val);
        }
        return $count;
    }
}

?> 