<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/12/14
 * Time: 9:57 PM
 */
require_once __DIR__."/../../import.php";

$surveyId = $_REQUEST['id'];
$questionId = $_REQUEST['qid'];

$manager = new \sarhan\survey\SurveyManager();
$survey = $manager->getSurvey($surveyId);

if(is_null($survey))
    header("Location:index.php");

$question = null;
/**
 * @var $val \sarhan\survey\SurveyQuestion
 */
foreach($survey->getQuestions() as $key => $val)
{
    if($questionId == $val->getId())
    {
        $question = $val;
        break;
    }
}

if(is_null($question))
    header("Location:index.php");

$error = "";
if( $_REQUEST['submit'])
{
    $questionText = $_REQUEST['questionText'];
    $type = $_REQUEST['type'];

    if($questionText && $type && is_numeric($type))
    {
        $question->setQuestion($questionText);

        $question->setType(new \sarhan\survey\QuestionType($type));
        $manager->updateSurvey($survey);
        header("Location:update.php?id=$surveyId");
    }
    else
    {
        $error = "Failed!!";
    }
}
else
{
    $questionText = $question->getQuestion();
    $type = $question->getType()->getValue();
}

$typeDropdown = '<select name="type">';
foreach(\sarhan\survey\QuestionType::toArray() as $key => $val)
{
    $selected = $val == $type ? 'selected="selected"' : "";
    $typeDropdown .= "<option value=\"$val\" $selected>$key</option>";
}

$typeDropdown .= '</select>';

?>
<html>
<head>
    <link rel="stylesheet" href="http://yui.yahooapis.com/combo?3.15.0/build/cssreset/reset-min.css&amp;3.15.0/build/cssfonts/fonts-min.css&amp;3.15.0/build/cssbase/base-min.css">
    <script src="http://yui.yahooapis.com/3.15.0/build/yui/yui-min.js"></script>
</head>
<body class="yui3-skin-sam">
<h3><?php echo $survey->getSurveyName()?>: Question Id <?php echo $question->getId()?></h3>
<?php
 if($error)
 {
    echo "<span class=\"color:red\">$error</span>";
 }
?>
<div class="yui3-widget-bd">
    <form>
        <input type="hidden" name="id" value="<?php echo $surveyId ?>">
        <input type="hidden" name="qid" value="<?php echo $questionId ?>">
        <fieldset>
            <p>
                <label for="name">Question Type</label><br/>
                <?php echo $typeDropdown ?>
            </p>
            <p>
                <label for="questionText">Question</label><br/>
                <input type="text" name="questionText" id="questionText" value="<?php echo $questionText ?>" placeholder="Question">
            </p>
            <p>
                <input type="submit" name="submit" id="submit" value="Update Question">
            </p>
        </fieldset>
    </form>
</div>
</body>
</html>