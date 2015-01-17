<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:16 PM
 */

namespace sarhan\survey;

require_once "AnswerValidator.php";
/**
 * Valid answer is between 1 and maxStars
 *
 * Class StarSurveyQuestion
 */
class StarRatingAnswerValidator implements AnswerValidator
{

    private $maxStars;

    function __construct($maxStars = 5)
    {
        $this->maxStars = $maxStars;
    }

    /**
     * @param $answer
     * @return boolean
     */
    public function isValid($answer)
    {
        if(!is_numeric($answer))
            return false;

        $val = (int)$answer;

        return $val > 0 && $val <= $this->maxStars;
    }

    /**
     * @param $answer
     *
     * @return string
     */
    public function normalize($answer)
    {
        return $answer;
    }

    /**
     * @return string Text to present to the user on how to format their answer
     */
    public function getHelperText()
    {
       return "Your answer must be between 1 and $this->maxStars.";
    }


}

?> 