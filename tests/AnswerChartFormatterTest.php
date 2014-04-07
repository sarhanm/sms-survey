<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/5/14
 * Time: 9:12 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../src/import.php";


class AnswerChartFormatterTest extends \PHPUnit_Framework_TestCase
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

    public function testPieChart()
    {

        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('stuff');

        $q = new SurveyQuestion();
        $q->setType(QuestionType::YesNo());
        $q->setQuestion("Is the world round?");
        $survey->addQuestion($q);

        $this->manager->createSurvey($survey);

        $max= rand(100,200);
        $yes = $no = 0;
        for($i = 0; $i < $max ; ++$i)
        {
            $ans = new SurveyAnswer();


            $val = "No";
            if((rand(0,1) % 2 == 0))
            {
                $val = "Yes";
                $yes++;
            }
            else
            {
                $no++;
            }
            $ans->setAnswer($val);
            $ans->setAnsweredBy("+12064122496");
            $this->manager->addAnswer($q->getId(),$ans);
        }
        $answers = $this->manager->getAnswers($survey->getId());

        $str = ReportChartFormatter::getChartData($answers->getAnswers($q->getId()),ChartFormats::Pie());
        $this->assertNotNull($str);

        $this->assertContains('{"key":"No","val":'.$no.'}', $str);
        $this->assertContains('{"key":"Yes","val":'.$yes.'}',$str);
    }

    public function testBarChart()
    {

        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('stuff');

        $q = new SurveyQuestion();
        $q->setType(QuestionType::StarRating());
        $q->setQuestion("how would you rate the event?");
        $survey->addQuestion($q);

        $this->manager->createSurvey($survey);

        $max= rand(100,200);
        $ratings = array(1=>0,2=>0,3=>0,4=>0,5=>0);

        for($i = 0; $i < $max ; ++$i)
        {
            $ans = new SurveyAnswer();
            $rand = rand(1,5);
            $ratings[$rand]++;
            $ans->setAnswer($rand);
            $ans->setAnsweredBy("+12064122496");
            $this->manager->addAnswer($q->getId(),$ans);
        }
        $answers = $this->manager->getAnswers($survey->getId());

        $str = ReportChartFormatter::getChartData($answers->getAnswers($q->getId()),ChartFormats::Bar());
        $this->assertNotNull($str);

        foreach($ratings as $key=>$rating)
        {
            $this->assertContains('{"key":'.$key.',"val":' . $rating . '}', $str);
        }
    }

    public function testTagCloud()
    {

        $survey = new Survey();
        $survey->setSurveyName("hi");
        $survey->setDescription('stuff');

        $q = new SurveyQuestion();
        $q->setType(QuestionType::Text());
        $q->setQuestion("Your Suggestions");
        $survey->addQuestion($q);

        $this->manager->createSurvey($survey);

        $max= rand(1,20);

        for($i = 0; $i < $max ; ++$i)
        {
            $val = $this->getRandomString();
            $ans = new SurveyAnswer();
            $ans->setAnswer($val);
            $ans->setAnsweredBy("+12064122496");
            $this->manager->addAnswer($q->getId(),$ans);
        }
        $answers = $this->manager->getAnswers($survey->getId());

        $strArr = ReportChartFormatter::getChartData($answers->getAnswers($q->getId()),ChartFormats::TagCloud());
        $this->assertNotNull($strArr);

        //TODO: how the heck do i test this? I guess that it just works?
    }

    private function getRandomString($length = 25)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ   ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

?> 