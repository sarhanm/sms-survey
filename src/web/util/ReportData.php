<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/6/14
 * Time: 1:14 PM
 */

namespace sarhan\survey;


class ReportData
{
    /**
     * @var SurveyQuestion
     */
    public $question;
    /**
     * @var string
     */
    public $data;

    /**
     * @var ChartFormats
     */
    public $type;

    /**
     * @var string
     */
    public $yuiType;

    /**
     * @param $ans
     * @param SurveyQuestion $question
     */
    function __construct($ans, SurveyQuestion $question)
    {
        switch($question->getType()->getValue())
        {
            case QuestionType::YesNo:
                $this->type =  ChartFormats::Pie();
                $this->yuiType = "pie";
                break;
            case QuestionType::StarRating:
                $this->type = ChartFormats::Bar();
                $this->yuiType = "bar";
                break;
            case QuestionType::Text:
                $this->type = ChartFormats::TagCloud();
                $this->yuiType = null;
                break;
        }

        $this->data = ReportChartFormatter::getChartData($ans,$this->type);
        $this->question = $question;
    }
}

?> 