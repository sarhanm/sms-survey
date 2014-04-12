<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/12/14
 * Time: 6:54 AM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../src/import.php";

class QuestionTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testQuestionHint()
    {
        $qt = QuestionType::YesNo();
        $this->assertEquals("(Y or N)", $qt->getQuestionHint());
    }
}

?> 