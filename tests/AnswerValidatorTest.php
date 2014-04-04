<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 1/19/14
 * Time: 1:35 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../src/import.php";

class AnswerValidatorTest extends \PHPUnit_Framework_TestCase
{

    function testYesNoSurveyQuestion()
    {
        $v = new ValidatorProvider();
        $q = $v->getValidator(QuestionType::YesNo());
        $this->assertFalse($q->isValid("what"));
        $this->assertTrue($q->isValid("Yes"));
        $this->assertTrue($q->isValid("yes"));
        $this->assertTrue($q->isValid("yEs"));
        $this->assertTrue($q->isValid("Y"));
        $this->assertTrue($q->isValid("y"));

        $this->assertTrue($q->isValid("No"));
        $this->assertTrue($q->isValid("no"));
        $this->assertTrue($q->isValid("NO"));
        $this->assertTrue($q->isValid("n"));
        $this->assertTrue($q->isValid("N"));
        $this->assertFalse($q->isValid(null));

    }

    function testStarSurveyQuestion()
    {
        $max = 5;
        $v = new ValidatorProvider();
        $q = $v->getValidator(QuestionType::StarRating());

        $this->assertFalse($q->isValid("what"));

        $this->assertFalse($q->isValid(0));
        $i = 1;
        for(; $i <= $max; ++$i)
        {
            $this->assertTrue($q->isValid($i));
        }

        $this->assertFalse($q->isValid($i),"Making sure that $i is not a valid star rating with max $max");
        $this->assertFalse($q->isValid(null));

    }

    function testStringSurveyQuestion()
    {
        $v = new ValidatorProvider();
        $q = $v->getValidator(QuestionType::Text());

        $this->assertFalse($q->isValid(NULL));
        $this->assertFalse($q->isValid(""));
        $this->assertFalse($q->isValid(" "));
        $this->assertTrue($q->isValid("anything else"));
        $this->assertFalse($q->isValid(null));
    }
}
 