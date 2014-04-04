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
}

?> 