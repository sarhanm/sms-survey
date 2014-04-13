<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 2:56 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../import.php";

use MyCLabs\Enum\Enum;

/**
 * Class QuestionType
 *
 * @method static QuestionType Text()
 * @method static QuestionType StarRating()
 * @method static QuestionType YesNo()
 * @method static QuestionType MultipleChoice()
 *
 * @package sarhan\survey
 */
class QuestionType extends Enum
{
    const Text = 1;
    const StarRating = 2;
    const YesNo = 3;
    //const MultipleChoice = 4;

    public function getQuestionHint()
    {
        switch($this->value)
        {
            case self::Text:
                return "";
            case self::StarRating:
                return "(Rate 1 to 5)";
            case self::YesNo:
                return "(Y or N)";
//            case self::MultipleChoice:
//                return "(Choose a number)";
        }

        throw new \Exception("Could not find a question hint for the given type!");
    }
}

?> 