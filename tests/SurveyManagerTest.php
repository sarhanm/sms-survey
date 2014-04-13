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

        //SurveyEntityManager::testClear();

        $answers = $this->manager->getAnswers($survey->getId());

        $this->assertNotNull($answers);
        $this->assertEquals(1,count($answers));
        $this->assertEquals($answers->getAnswers($q->getId())[0]->getAnswer(),"awesome!");
        $this->assertEquals($answers->getAnswers($q->getId())[0]->getSurveyQuestionId(),$q->getId());

        //add another question
        $q2 = new SurveyQuestion();
        $q2->setType(QuestionType::StarRating());
        $q2->setQuestion("Second question");
        $survey->addQuestion($q2);
        $this->manager->updateSurvey($survey);

        $this->assertNotNull($q2->getId(),"Asserting the second question was saved");

        //Create another survey and question to make sure we do not
        // have leaks.
        $survey2 = new Survey();
        $survey2->setSurveyName("hi2");
        $survey2->setDescription('stuff2');

        $q3 = new SurveyQuestion();
        $q3->setType(QuestionType::StarRating());
        $q3->setQuestion("A question3");
        $survey2->addQuestion($q3);
        $this->manager->createSurvey($survey2);

        //Now lets add a lot more answers!
        $max= rand(1,10);
        for($i = 0; $i < $max ; ++$i)
        {
            $ans = new SurveyAnswer();
            $ans->setAnswer("awesome$i");
            $ans->setAnsweredBy("+12064122496");
            $this->manager->addAnswer($q->getId(),$ans);

            $ans2 = new SurveyAnswer();
            $ans2->setAnswer("2awesome$i");
            $ans2->setAnsweredBy("+12064122496");
            $this->manager->addAnswer($q2->getId(),$ans2);

            $ans3 = new SurveyAnswer();
            $ans3->setAnswer("don't return me");
            $ans3->setAnsweredBy("+12064122496");
            $this->manager->addAnswer($q3->getId(),$ans3);
        }
        $answers = $this->manager->getAnswers($survey->getId());

        $this->assertNotNull($answers);

        $this->assertEquals(1+$max*2,$answers->getTotalAnswers());

        $answers = $this->manager->getAnswers($survey2->getId());
        $this->assertNotNull($answers);
        $this->assertEquals($max,$answers->getTotalAnswers());
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

    public function testGetAllActive()
    {
        $max = rand(1,10);
        for($i = 0; $i < $max ; ++$i)
        {
            $s = new Survey();
            $s->setDescription("a survey description".$i);
            $s->setSurveyName("a name");
            $s->setThankYouMessage("A wonder world!");
            $this->manager->createSurvey($s);
        }

        $s = new Survey();
        $s->setDescription("a survey description".$max);
        $s->setSurveyName("a name");
        $s->setThankYouMessage("A wonder world!");
        $s->setStatus(SurveyStatus::disabled());
        $this->manager->createSurvey($s);

        $surveys = $this->manager->getSurveys(SurveyStatus::active());
        $this->assertNotNull($surveys);
        $this->assertEquals($max,count($surveys));
        $this->assertEquals("a survey description0",$surveys[0]->getDescription());
        $this->assertEquals("a survey description".($max-1),$surveys[$max-1]->getDescription());

        //Now only get the disabled survey
        $surveys = $this->manager->getSurveys(SurveyStatus::disabled());
        $this->assertNotNull($surveys);
        $this->assertEquals(1,count($surveys));
        $this->assertEquals("a survey description".$max,$surveys[0]->getDescription());
    }

    public function testDeleteQuestionFromSurvey()
    {
        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('stuff');

        $qtypes = QuestionType::toArray();

        $max = rand(4,5);
        for($i = 0; $i<$max;++$i)
        {
            $q = new SurveyQuestion();
            $q->setType(new QuestionType(rand(1,count($qtypes))));
            $q->setQuestion("q".$i);
            $survey->addQuestion($q);
        }

        $this->manager->createSurvey($survey);

        $toDelete = $survey->getQuestions()[rand(0,$max-1)];
        $idToDelete = $toDelete->getId();

        $survey->deleteQuestion($idToDelete);
        $this->manager->updateSurvey($survey);
        $this->assertEquals($max-1,count($survey->getQuestions()));

    }

}

?> 