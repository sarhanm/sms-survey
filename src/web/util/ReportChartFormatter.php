<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/5/14
 * Time: 7:45 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../../import.php";

use MyCLabs\Enum\Enum;

class ChartFormats extends Enum
{
    const Pie = 1;
    const Bar = 2;
    const Line = 3;
    const TagCloud = 4;
}

/**
 *
 *
 * Class AnswerChartFormatter
 * @package sarhan\survey
 */
class ReportChartFormatter
{
    /**
     * @param SurveyAnswer[] $answers
     * @param ChartFormats $format
     *
     * @throws \Exception
     * @return null
     */
    public static function getChartData(array $answers, ChartFormats $format)
    {
        //TODO: Based on the format, return the appropriate string

        switch($format->getValue())
        {
            case ChartFormats::Pie:
                return json_encode(self::getData($answers));
            case ChartFormats::Bar:
            case ChartFormats::Line:
                $data = self::getData($answers);
                usort($data,"sarhan\survey\ReportChartFormatter::sortFunc");
                return json_encode($data);
            case ChartFormats::TagCloud:
                $data = self::getData($answers);
                usort($data,"sarhan\survey\ReportChartFormatter::sortFunc");
                return $data;
            default:
                throw new \Exception("No format output defined for ". $format);
        }
    }

    /**
     * @param SurveyAnswer[] $answers
     *
     * @return string
     */
    private static function getData(array $answers)
    {
        $map = array();
        foreach($answers  as $ans)
        {
            if(!array_key_exists($ans->getAnswer(),$map))
            {
                $map[$ans->getAnswer()] = array("key"=>$ans->getAnswer(),"val"=>0);
            }

            $map[$ans->getAnswer()]["val"]++;
        }

        $data = array();
        foreach($map as $v)
        {
            $data[] = $v;
        }

        return $data;
    }

    /**
     * @param $a
     * @param $b
     */
    private function sortFunc($a,$b)
    {
        $k1 = $a['key'];
        $k2 = $b['key'];
        if($k1 == $k2)
        {
            return 0;
        }

        return ($k1 < $k2) ? -1 : 1;
    }
}

?> 