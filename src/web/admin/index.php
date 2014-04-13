<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/4/14
 * Time: 12:30 AM
 */
namespace sarhan\survey\web\admin;

use sarhan\survey\SurveyManager;
use sarhan\survey\SurveyStatus;

require_once __DIR__."/../../import.php";

$manager = new SurveyManager();
$surveys = $manager->getSurveys();

class TableData
{
    public $id;
    public $surveyName;
    public $description;
    public $created;
    public $status;
    public $action;

    /**
     * @param $id
     * @param $status
     * @param $surveyName
     * @param $created
     * @param $description
     */
    function __construct($id,SurveyStatus $status, $surveyName,$created, $description)
    {
        $this->created = $created;
        $this->description = $description;
        $this->id = $id;
        $this->status = $status->getName();
        $this->surveyName = $surveyName;

        $this->action = '<a href="report.php?id='.$id.'">View Report</a>';
        if($status->getValue() == SurveyStatus::active)
        {
            $sid = SurveyStatus::disabled;
            $this->action .= " | <a href=\"changeStatus.php?id=$id&status=$sid\">Disable</a>";
        }
        else
        {
            $sid = SurveyStatus::active;
            $this->action .= " | <a href=\"changeStatus.php?id=$id&status=$sid\">Enable</a>";
        }
    }
}

$rows= array();
foreach($surveys as $s)
{
    $rows[] = new TableData($s->getId(),$s->getStatus(),
        $s->getSurveyName(),$s->getCreated()->format('j/n/y H:i'),$s->getDescription());
}

$data = json_encode($rows);

?>

<html>
    <head>
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic">
        <link rel="stylesheet" href="http://yui.yahooapis.com/3.9.0/build/cssgrids/cssgrids-min.css">
        <script src="http://yui.yahooapis.com/3.15.0/build/yui/yui-min.js"></script>

        <script type="text/javascript">
            YUI().use("datatable", function (Y) {

                // A table from data with keys that work fine as column names
                var simple = new Y.DataTable({
                    columns: [
                        { key: "id", formatter: "<a href=\"update.php?id={value}\">{value}</a>" },
                        { key:"surveyName",label: "Survey Name"},
                        { key:"description",label: "Description"},
                        {key: "created",label:"Create Date"},
                        {key:"status", label: "Status"},
                        {key:"action",label:"Actions", allowHTML: true }],
                    data   : <?php echo $data ?> ,
                    summary: "A list of created Surveys",
                    caption: "Surveys"
                });
                simple.render("#simple");
            });
        </script>
    </head>
    <body class="yui3-skin-sam">
        <div class="yui3-skin-sam"> <!-- You need this skin class -->
            <div id="simple"></div>
            <div id="labels"></div>
        </div>

        <div><a href="create-survey.php">Create a new survey</a></div>
    </body>

</html>