<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:15 PM
 */

namespace sarhan\survey;

require_once "AnswerValidator.php";

/**
 * Valid answer is any other than an empty/null string
 * Class StringSurveyQuestion
 */
class TextAnswerValidator implements AnswerValidator
{
    /**
     * @param $answer
     * @return boolean
     */
    public function isValid($answer)
    {
        //Just need to make sure that the answer is set and has text.
        return $answer != NULL && trim($answer) != '';
    }
}

?> 