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
        return "Your response was empty. Please try again";
    }


}

?> 