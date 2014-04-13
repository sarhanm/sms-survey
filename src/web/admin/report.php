<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/5/14
 * Time: 3:03 AM
 */

require_once __DIR__."/../../import.php";

use sarhan\survey\ReportData;
use sarhan\survey\SurveyManager;

$surveyId = $_REQUEST['id'];
if(!$surveyId)
{
    header("Location: index.php");
}

$manager = new SurveyManager();
$survey = $manager->getSurvey($surveyId);

if(!$survey || is_null($survey))
    header("Location: index.php");

$answers = $manager->getAnswers($surveyId);

/**
 * @var ReportData[] $reportData
 */
$reportData[] = array();
$index =0;
foreach($survey->getQuestions() as $q)
{
    $ans = $answers->getAnswers($q->getId());
    if(!is_null($ans) && is_array($ans) && count($ans) > 0)
    {
        $reportData[$index] =  new ReportData($ans,$q);
    }
    $index++;
}
?>
<html>
    <head>
        <style type="text/css">
            .text{
                background-color: lightcyan;
            }
            .pie{
                margin:10px 10px 10px 10px;
                width:200px;
                height:200px;
            }

            .bar{
                margin:10px 10px 10px 10px;
                width:700px;
                height:200px;
            }
        </style>
        <script src="http://yui.yahooapis.com/3.15.0/build/yui/yui-min.js"></script>
        <script type="text/javascript">

            <?php
            /**
            * @var $data ReportData
             */
            foreach($reportData as $key=>$data)
            {
                if(is_null($data->yuiType))
                {
                    continue;
                }
             ?>
                YUI().use('charts', function (Y)
                {
                    var pieGraph = new Y . Chart({
                        render:"#chart<?php echo $key?>",
                        categoryKey:"key",
                        seriesKeys:["val"],
                        dataProvider:<?php echo $data->data ?>,
                        type:"<?php echo $data->yuiType?>",
                        seriesCollection:
                        [{
                            categoryKey:"key",
                            valueKey:"val",
                            styles: {
                                fill:{
                                    colors:["#6084d0", "#eeb647", "#6c6b5f", "#d6484f", "#ce9ed1"]
                                }}}]
                    });
                });
                <?php } ?>

        </script>
    </head>
    <body>

        <h1 style="text-align: center">Mihraab Survey for
            <a href="update.php?id=<?php echo $survey->getId()?>">
            <?php echo $survey->getSurveyName()?></a></h1>
        <h3 style="text-align: center;color: #808080"><?php echo $survey->getDescription() ?></h3>
        <h3 style="text-align: center;color: #808080">Report Date: <?php echo date("M j, y") ?></h3>
        <?php
        /**
         * @var $reportData ReportData[]
         */
        foreach($reportData as $key=>$val)
        {
            echo '<h3>'.$val->question->getQuestion().'</h3>';

            if(is_null($val->yuiType))
            {
                foreach($val->data as $word)
                {
                    echo '<span class="text '.$word['val'].'">'.$word['key'].'</span>&nbsp;&nbsp;&nbsp;';
                }
            }
            else
            {
                echo "<div id=\"chart$key\" class=\"chart ".$val->yuiType."\"></div>";
            }

        }
        ?>
    </body>
</html>