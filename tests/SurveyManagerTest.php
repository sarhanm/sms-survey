<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 1:20 AM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../src/import.php";

class SurveyManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SurveyManager
     */
    private $manager;

    public function setUp()
    {
        SurveyEntityManager::testSetup();
        $this->manager = new SurveyManager();
    }

    protected function tearDown()
    {
        SurveyEntityManager::testTearDown();
    }

    public function testUpdate()
    {
        //Re-running the same test to make sure that things get blown away between tests!
        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('testing a new survey!');
        $survey->setStatus(SurveyStatus::active());
        $survey->setCreated(new \DateTime());

        $surveyId = $this->manager->createSurvey($survey);
        SurveyEntityManager::testClear();

        $this->assertNotNull($survey->getId());
        $this->assertNotNull($surveyId);

        //Now lets update the survey
        $status = SurveyStatus::active();
        $survey->setStatus($status);
        $this->manager->updateSurvey($survey);

        $ss = $this->manager->getSurvey($surveyId);
        $this->assertEquals($status, $ss->getStatus());

        //Re-run without detaching, to make sure this still works

        $status = SurveyStatus::disabled();
        $survey->setStatus($status);
        $this->manager->updateSurvey($survey);

        $ss = $this->manager->getSurvey($surveyId);
        $this->assertEquals($status, $ss->getStatus());


        $status = SurveyStatus::active();
        $survey = new Survey($surveyId);
        $survey->setSurveyName("hu");
        $survey->setDescription('testing a new survey!');
        $survey->setStatus($status);
        $survey->setCreated(new \DateTime());
        $this->manager->updateSurvey($survey);

        $ss = $this->manager->getSurvey($surveyId);
        $this->assertEquals($status, $ss->getStatus());

        $this->assertEquals($surveyId,$ss->getId());

        //after all that updating, there should be only one
        // survey
        $count = count(SurveyEntityManager::getEntityManager(true,true)->getRepository(Survey::NAME)->findAll());
        $this->assertEquals(1,$count,"Only one survey should exist!");
    }

    public function testCreate()
    {

        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('testing a new survey!');
        $survey->setStatus(SurveyStatus::active());
        $survey->setCreated(new \DateTime());

        $surveyId = $this->manager->createSurvey($survey);

        $this->assertNotNull($survey->getId());
        $this->assertNotNull($surveyId);

        $count = count(SurveyEntityManager::getEntityManager(true,true)->getRepository(Survey::NAME)->findAll());
        $this->assertEquals(1,$count,"Only one survey should exist!");
    }


    public function testAddQuestions()
    {
        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('stuff');

        $num = rand(3,10);
        for($i = 0; $i < $num;++$i)
        {
            $q = new SurveyQuestion();
            $q->setType(QuestionType::Text());
            $q->setQuestion("q$i");
            $survey->addQuestion($q);
        }

        $this->manager->createSurvey($survey);

        SurveyEntityManager::testClear();

        $ss = $this->manager->getSurvey($survey->getId());
        $this->assertEquals($ss->getId(),$survey->getId());
        $this->assertNotNull($ss->getQuestions());
        $this->assertEquals($num, count($ss->getQuestions()));

        $q = $ss->getQuestions()[$num -1];
        $this->assertNotNull($q);
        $this->assertEquals("q".($num-1), $q->getQuestion());
        $this->assertEquals(QuestionType::Text(),$q->getType());

    }

    public function testAnswerQuestion()
    {
        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('stuff');

        $q = new SurveyQuestion();
        $q->setType(QuestionType::StarRating());
        $q->setQuestion("A question");
        $survey->addQuestion($q);
        $this->manager->createSurvey($survey);

        $ss = $this->manager->getSurvey($survey->getId());
        $q = $ss->getQuestions()[0];

        $ans = new SurveyAnswer();
        $ans->setAnswer("awesome!");
        $ans->setAnsweredBy("+12064122496");
        $this->manager->addAnswer($q->getId(),$ans);
    }

    public function testGetSurveyByName()
    {
        $name = "hello there";
        $s = new Survey();
        $s->setDescription("a survey");
        $s->setSurveyName($name);
        $s->setThankYouMessage("A wonder world!");

        $id = $this->manager->createSurvey($s);

        $ss = $this->manager->getSurveyByName($name);
        $this->assertNotNull($ss, "Test valid retrieval by survey name='$name'");
        $this->assertEquals($id, $ss->getId());
    }


}

?> 