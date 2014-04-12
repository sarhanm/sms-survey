<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 1/19/14
 * Time: 1:53 PM
 */

namespace sarhan\survey;

require_once(__DIR__ . "/../src/import.php");

class SurveyStateTest extends \PHPUnit_Framework_TestCase
{
    function testSurveyState()
    {
        $questionStub = $this->getMock('sarhan\survey\SurveyQuestion');

        $surveyStub = $this->getMock("sarhan\survey\Survey");
        $surveyStub->expects($this->any())
            ->method('getQuestions')->will($this->returnValue(array($questionStub,$questionStub,$questionStub)));

        $questionStub->expects($this->any())->method("getType")->will($this->returnValue(QuestionType::YesNo()));
        $this->assertEquals(array($questionStub,$questionStub,$questionStub),$surveyStub->getQuestions());

        $s = new SurveyState();
        $s->setSurvey($surveyStub);

        $this->assertFalse($s->hasId());
        $s->setId("foobar");
        $this->assertTrue($s->hasId());

        $s->setQuestionIndex(0);
        $q = $s->getQuestion();
        $this->assertNotNull($q);

        $s->setQuestionIndex(3);
        $q = $s->getQuestion();
        $this->assertNull($q);
    }

    public function testExpiration()
    {
        $s = new SurveyState(time()-500);
        $this->assertTrue($s->isExpired(10));

        $s = new SurveyState(time());
        $this->assertTrue($s->isExpired(1000));


        $s = new SurveyState(time()-500);
        $s->setSurveyExecutionState(SurveyExecutionState::InProgress());
        $this->assertTrue($s->isExpired(10));

        $s = new SurveyState(time());
        $s->setSurveyExecutionState(SurveyExecutionState::InProgress());
        $this->assertFalse($s->isExpired(1000));
    }

    public function testSerialization()
    {
        $s = new SurveyState(time()-500);
        $serialzedValue = serialize($s);

        /**
         * @vars $state SurveyState
         */
        $state = unserialize($serialzedValue);

        $this->assertTrue($state->isState(SurveyExecutionState::Unknown()));
    }
}
 