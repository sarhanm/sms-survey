<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:26 PM
 */

namespace sarhan\survey;

require_once(__DIR__ . "/../import.php");

class ValidatorProvider
{

    /**
     * @var AnswerValidator[]
     */
    private $validators;

    function __construct()
    {
        $this->validators[QuestionType::Text] = new TextAnswerValidator();
        $this->validators[QuestionType::StarRating] = new StarRatingAnswerValidator();
        $this->validators[QuestionType::YesNo] = new YesNoAnswerValidator();
    }

    public function getValidator(QuestionType $type)
    {
        return $this->validators[$type->getValue()];
    }
}

?> 