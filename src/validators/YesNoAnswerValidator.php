<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:17 PM
 */

namespace sarhan\survey;

require_once "AnswerValidator.php";

/**
 * Valid answers are Yes, No, Y or N (not case sensitive)
 * Class YesNoSurveyQuestion
 */
class YesNoAnswerValidator implements AnswerValidator
{
    public function isValid($answer)
    {
        $valid_values = array("yes","no","y","n");

        // In array will take care of casing differences
        return in_array(strtolower($answer), $valid_values);
    }

    /**
     * @param $answer
     *
     * @return string
     */
    public function normalize($answer)
    {
        $yes = array("yes","y");
        if(in_array(strtolower($answer), $yes))
        {
            return 'Yes';
        }

        return "No";
    }

    /**
     * @return string Text to present to the user on how to format their answer
     */
    public function getHelperText()
    {
        return "Your answer must be either Yes or No.";
    }

}

?> 