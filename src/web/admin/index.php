<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/4/14
 * Time: 12:30 AM
 */
namespace sarhan\survey\web\admin;

use sarhan\survey\SurveyManager;

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

    function __construct($id,$status, $surveyName,$created, $description)
    {
        $this->created = $created;
        $this->description = $description;
        $this->id = $id;
        $this->status = $status;
        $this->surveyName = $surveyName;
    }
}

$rows= array();
foreach($surveys as $s)
{
    $rows[] = new TableData($s->getId(),$s->getStatus()->getValue(),
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
                    columns: ["id", "surveyName", "description","created","status"],
                    data   : <?php echo $data ?> ,
                    summary: "A list of created Surveys",
                    caption: "Surveys"
                });
                simple.render("#simple");
            });
        </script>
    </head>
    <body>
        <div class="yui3-skin-sam"> <!-- You need this skin class -->
            <div id="simple"></div>
            <div id="labels"></div>
        </div>

        <div><a href="create-survey.php">Create a new survey</a></div>
    </body>

</html>