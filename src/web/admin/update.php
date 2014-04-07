<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/4/14
 * Time: 1:53 AM
 */
use sarhan\survey\SurveyManager;
use sarhan\survey\QuestionType;
use sarhan\survey\SurveyQuestion;

require_once ("../../import.php");

$surveyId = $_REQUEST['id'];
if(!$surveyId)
{
    header("Location: index.php");
}

$manager = new SurveyManager();
$survey = $manager->getSurvey($surveyId);

if($_REQUEST['submit'])
{
    echo  "in submit<br/>";
    //add a question to the survey
    $type = $_REQUEST['type'];
    $questionString = trim($_REQUEST['question']);
    echo $questionString.'<br/>';
    echo "type: $type <br/>";

    if(is_numeric($type) && $questionString != "" )
    {
        echo "saving...";
        $sq = new SurveyQuestion();
        $sq->setType(new QuestionType($type));
        $sq->setQuestion($questionString);
        $survey->addQuestion($sq);
        $manager->updateSurvey($survey);
    }
}
else if($_REQUEST['delete'] == "true" && $_REQUEST['qid'])
{
    $qid = $_REQUEST['qid'];
    $survey->deleteQuestion($qid);
    $manager->updateSurvey($survey);
}


class QuestionData
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $question;

    public $action;

    function __construct($id, $question, QuestionType $type)
    {
        global $surveyId;

        $this->id = $id;
        $this->question = $question;

        foreach($type->toArray() as $key => $val)
        {
            if($val == $type->getValue())
                $this->type = $key;
        }

        $this->action = '<a href="update.php?id='.$surveyId.'&qid='.$id.'&delete=true">Delete</a>';
    }
}

$typeDropdown = '<select name="type">';
foreach(QuestionType::toArray() as $key => $val)
{
    $typeDropdown .= "<option value=\"$val\">$key</option>";
}

$typeDropdown .= '</select>';


if(!$survey || is_null($survey))
{
    header("Location: index.php");
}

$rows= array();
foreach($survey->getQuestions() as $q)
{
    $rows[] = new QuestionData($q->getId(),$q->getQuestion(),$q->getType());
}

$data = json_encode($rows);

?>
<html>
    <head>

        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic">
        <link rel="stylesheet" href="http://yui.yahooapis.com/3.9.0/build/cssgrids/cssgrids-min.css">
        <script src="http://yui.yahooapis.com/3.15.0/build/yui/yui-min.js"></script>

        <script type="text/javascript">
            YUI().use("datatable-mutable", function (Y) {

                // A table from data with keys that work fine as column names
                var table = new Y.DataTable({
                    columns: [
                        { key: "id", label: "Question Id",allowHTML: true},
                        { key:"type",label: "Type", allowHTML: true},
                        { key:"question",label: "Question",allowHTML: true},
                        { key:"action", label: "Action",allowHTML: true}],
                    data   : <?php echo $data ?> ,
                    summary: "A list of created Surveys",
                    caption: "SurveyId <?php echo $survey->getId().": ".$survey->getSurveyName() ?><br/><a href=\"create-survey.php?id=<?php echo $survey->getId() ?>\">Edit Survey</a>"
                });
                table.render("#simple");

                table.addRow({id: "&nbsp;",
                              type: '<?php echo $typeDropdown?>',
                              question: '<input type="text" name="question" size="80">',
                              action:'<input type="submit" name="submit">' });
            });


        </script>
    </head>
    <body class="yui3-skin-sam">
    <?php include_once "header.php" ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $surveyId?>">
        <div class="yui3-skin-sam"> <!-- You need this skin class -->
            <div id="simple"></div>
            <div id="labels"></div>
        </div>
    </form>
    </body>

</html>