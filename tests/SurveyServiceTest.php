<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 1/19/14
 * Time: 2:02 PM
 */

namespace sarhan\survey;

require_once(__DIR__ . "/../src/import.php");

class SurveyServiceTest extends \PHPUnit_Framework_TestCase
{
    private $session;
    private $request;

    /**
     * @var SurveyManager
     */
    private $surveyManager;

    protected function setUp()
    {
        $this->session = array();
        $this->request = array();
        SurveyEntityManager::testSetup();
        $this->surveyManager = new SurveyManager();
    }

    protected function tearDown()
    {
        SurveyEntityManager::testTearDown();
    }


    /*
     * @requires extension mysqli
     */
    function testSessionAssignment()
    {
        //Verify that if we do not pass in null values,
        //that it creates pointers to the session and request objects
        $_SESSION = array();
        $manager = new SurveyRequestService();
        $ses = $manager->getSession();
        $this->assertNotNull($ses);
        $this->assertFalse(array_key_exists('foobar', $ses));
        $_SESSION['foobar'] = "hello!";
        $ses = $manager->getSession();
        $this->assertEquals("hello!", $ses["foobar"]);

    }

    function testConstructor()
    {
        $this->assertFalse(array_key_exists(SurveyRequestService::SESSION_KEY,$this->session));
        $manager = $this->newSurveyService();
        $this->assertTrue($manager->getSurveyState()->isState(SurveyExecutionState::Unknown()));
    }

    function testShouldService()
    {
        $this->assertFalse(array_key_exists(SurveyRequestService::SESSION_KEY, $this->session));
        $manager = $this->newSurveyService();
        $this->assertFalse($manager->shouldService());

        $s = new Survey();
        $s->setSurveyName("survey123");
        $s->setDescription("a survey");
        $s->setThankYouMessage("all done");
        $this->surveyManager->createSurvey($s);
        $this->request[SurveyRequestService::BODY_KEY] = $s->getSurveyName();

        $objRequest = $manager->getRequest();
        $this->assertEquals($s->getSurveyName(),$objRequest[SurveyRequestService::BODY_KEY]);
        $this->assertTrue($manager->shouldService(),"shouldService must return true when request is set to survey");

        //Reset session and request
        $this->session = array();
        $this->request = array();
        $manager = $this->newSurveyService();
        $this->request[SurveyRequestService::BODY_KEY] = "survey";
        $this->assertFalse($manager->shouldService(),"shouldService must return false when no survey exists");
    }

    function testShouldServiceFalse()
    {
        $manager = $this->newSurveyService();
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "";
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "prayer";
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "iqama";
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "survey@me.com";
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "youand@survey.com";
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "awordwithsurveyinit";
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "surveystartstheword";
        $this->assertFalse($manager->shouldService());

        $this->request[SurveyRequestService::BODY_KEY] = "survey and others";
        $this->assertFalse($manager->shouldService());

        unset($this->request[SurveyRequestService::BODY_KEY]);
        $this->assertFalse($manager->shouldService());
    }

    function testFirstQuestion()
    {
        $manager = $this->newSurveyService();
        $q = "whats your name";
        $thankyou = "Thank you boyi!";

        $s = new Survey();
        $s->setSurveyName("survey123");
        $s->setDescription("a survey");
        $s->setThankYouMessage($thankyou);

        $sq = new SurveyQuestion();
        $sq->setQuestion($q);
        $sq->setType(QuestionType::Text());

        $s->addQuestion($sq);
        $this->surveyManager->createSurvey($s);
        SurveyEntityManager::testClear();

        $this->request[SurveyRequestService::BODY_KEY] = $s->getSurveyName();

        $serviced = $manager->service();
        $res = $manager->getResponse();

        $this->assertTrue($serviced);
        $this->assertNotNull($res);
        $this->assertTrue(count($res->getContent()) > 0);

        $content = $res->getContent();
        $this->assertContains($q,$content);

        //Run the same command, make sure we still get the first question
        $serviced = $manager->service();
        $res = $manager->getResponse();

        $this->assertTrue($serviced);
        $this->assertNotNull($res);
        $this->assertTrue(count($res->getContent()) > 0);

        $content = $res->getContent();
        $this->assertContains($q,$content);

        $this->request[SurveyRequestService::BODY_KEY] = "my answer!";

        $serviced = $manager->service();
        $res = $manager->getResponse();
        $this->assertTrue($serviced);
        $this->assertNotNull($res);
        $this->assertTrue(count($res->getContent()) > 0);

        $content = $res->getContent();
        $this->assertContains($thankyou,$content);
    }

    function testInvalidAnswer()
    {
        $manager = $this->newSurveyService();
        $max = 5;
        $q = "Rate us 1 to $max";

        $thankYou = "Thank you for filling out the survey. Visit us at farooqmasjid.org";

        $s = new Survey();
        $s->setSurveyName("my name is john");
        $s->setDescription("a survey");
        $s->setThankYouMessage($thankYou);

        $sq = new SurveyQuestion();
        $sq->setQuestion($q);
        $sq->setType(QuestionType::StarRating());

        $s->addQuestion($sq);
        $this->surveyManager->createSurvey($s);
        SurveyEntityManager::testClear();

        $this->request[SurveyRequestService::BODY_KEY] = $s->getSurveyName();;

        $serviced = $manager->service();
        $res = $manager->getResponse();

        $this->assertTrue($serviced);
        $this->assertNotNull($res);
        $this->assertTrue(count($res->getContent()) > 0);
        $this->assertTrue(strstr($res->getContent(),$q) !== false);

        $this->request[SurveyRequestService::BODY_KEY] = "15";
        $serviced = $manager->service();
        $res = $manager->getResponse();
        $this->assertTrue($serviced);
        $this->assertNotNull($res);
        $this->assertTrue(count($res->getContent()) > 0);
        $this->assertTrue(strstr($res->getContent(),"Sorry, we couldn't understand your response. Your answer must be between 1 and 5") !== false);

        $this->request[SurveyRequestService::BODY_KEY] = "0";
        $serviced = $manager->service();
        $res = $manager->getResponse();
        $this->assertTrue($serviced);
        $this->assertNotNull($res);
        $this->assertTrue(count($res->getContent()) > 0);
        $this->assertTrue(strstr($res->getContent(),"Sorry, we couldn't understand your response") !== false);

        //$session = $this->session;
        for( $i = 1; $i <= $max ; $i++)
        {
            //reset back to first question every time.
            $this->getSurveyState()->setQuestionIndex(0);
            $this->getSurveyState()->setSurveyExecutionState(SurveyExecutionState::InProgress());

            $this->request[SurveyRequestService::BODY_KEY] = $i;

            $serviced = $manager->service();
            $res = $manager->getResponse();
            $this->assertTrue($serviced,"Testing on i = $i");
            $this->assertNotNull($res);
            $this->assertTrue(count($res->getContent()) > 0);
            $this->assertTrue(strstr($res->getContent(),$thankYou) !== false);
        }

    }

    function testMultipleQuestions()
    {

        $manager = $this->newSurveyService();
        $thankYou = "Thank you for filling out the survey. Visit us at farooqmasjid.org";
        $max = 5;
        $qs[0] = "Rate us";
        $qt[0] = QuestionType::StarRating();

        $qs[1] = "What did you like?";
        $qt[1] = QuestionType::Text();

        $qs[2] = "Would you attend another event like this? (yes or no)";
        $qt[2] = QuestionType::YesNo();


        $s = new Survey();
        $s->setSurveyName("my name is john");
        $s->setDescription("a survey");
        $s->setThankYouMessage($thankYou);

        for ($i = 0; $i < count($qs); ++$i)
        {
            $sq = new SurveyQuestion();
            $sq->setQuestion($qs[$i]);
            $sq->setType($qt[$i]);
            $s->addQuestion($sq);
        }

        $this->surveyManager->createSurvey($s);
        SurveyEntityManager::testClear();

        $this->request[SurveyRequestService::BODY_KEY] = $s->getSurveyName();;

        $serviced = $manager->service();
        $res = $manager->getResponse();

        $this->assertTrue($serviced);
        $this->assertNotNull($res);
        $this->assertTrue(count($res->getContent()) > 0);
        $this->assertContains($qs[0],$res->getContent(),"Checking first question returned");

        $this->request[SurveyRequestService::BODY_KEY] = 3;
        $serviced = $manager->service();
        $this->assertTrue($serviced);
        $res = $manager->getResponse();
        $this->assertContains($qs[1],$res->getContent(),"Checking second question returned");

        $this->request[SurveyRequestService::BODY_KEY] = "I loved and loved and .36879176^&*@#;;!2";
        $serviced = $manager->service();
        $this->assertTrue($serviced);
        $res = $manager->getResponse();
        $this->assertContains($qs[2],$res->getContent(),"Checking third question returned");

        $this->request[SurveyRequestService::BODY_KEY] = 'Yes';
        $serviced = $manager->service();
        $this->assertTrue($serviced);
        $res = $manager->getResponse();
        $this->assertTrue(strstr($res->getContent(),$thankYou) !== false);
    }

    public function testSurveyName()
    {
        $survey = new Survey();
        $survey->setSurveyName("yesno");
        $survey->setDescription('stuff');

        $q = new SurveyQuestion();
        $q->setType(QuestionType::YesNo());
        $q->setQuestion("A question");
        $survey->addQuestion($q);

        //add another question
        $q2 = new SurveyQuestion();
        $q2->setType(QuestionType::StarRating());
        $secondQuestion = "Second question";
        $q2->setQuestion($secondQuestion);
        $survey->addQuestion($q2);

        $this->surveyManager->createSurvey($survey);

        SurveyEntityManager::testClear();

        $service = $this->newSurveyService();
        $this->request['Body'] = "yesno";
        $this->request['From']  = "1234";
        $serviced = $service->service();
        $this->assertTrue($serviced);

        $this->request['Body'] = "no";
        $this->request['From']  = "1234";
        $serviced = $service->service();
        $this->assertTrue($serviced);
        $this->assertContains($secondQuestion,$service->getResponse()->getContent());
    }

    /**
     * @return SurveyRequestService
     */
    private function newSurveyService()
    {
        return new SurveyRequestService($this->session, $this->request);
    }

    /**
     * @return SurveyState
     */
    private function getSurveyState()
    {
        return $this->session[SurveyRequestService::SESSION_KEY];
    }
}
 