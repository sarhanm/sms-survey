<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/4/14
 * Time: 1:21 AM
 */

namespace sarhan\survey\web\admin;

require_once __DIR__."/../../import.php";

use sarhan\survey\Survey;
use sarhan\survey\SurveyManager;

$surveyId = $_REQUEST['id'];

$manager = new SurveyManager();
$survey = null;

$surveyName = "";
$desc = "";
$thankyouMsg="";
$submitText="Add Survey";
if($surveyId && is_numeric($surveyId))
{
    $survey = $manager->getSurvey($surveyId);
    if(!$survey || is_null($survey))
        header("Location: index.php");

    $surveyName = $survey->getSurveyName();
    $desc = $survey->getDescription();
    $thankyouMsg = $survey->getThankYouMessage();
    $submitText="Update Survey";
}

if($_REQUEST['submit'])
{
    if(!$survey || is_null($survey))
        $survey = new Survey();

    $survey->setThankYouMessage($_REQUEST['thankyou']);
    $survey->setDescription($_REQUEST['description']);
    $survey->setSurveyName($_REQUEST['name']);

    if($survey->getId() > 0)
    {
        $id = $survey->getId();
        $manager->updateSurvey($survey);
    }
    else
        $id = $manager->createSurvey($survey);

    header("Location: update.php?id=$id");
}



?>
<html>
    <head>
        <link rel="stylesheet" href="http://yui.yahooapis.com/combo?3.15.0/build/cssreset/reset-min.css&amp;3.15.0/build/cssfonts/fonts-min.css&amp;3.15.0/build/cssbase/base-min.css">
        <script src="http://yui.yahooapis.com/3.15.0/build/yui/yui-min.js"></script>
    </head>
    <body class="yui3-skin-sam">
    <div class="yui3-widget-bd">
        <form>
            <input type="hidden" name="id" value="<?php echo $surveyId ?>">
            <fieldset>
                <p>
                    <label for="name">Survey Name</label><br/>
                    <input type="text" name="name" id="name" value="<?php echo $surveyName?>" placeholder="Survey Name">
                </p>
                <p>
                    <label for="description">Description</label><br/>
                    <input type="text" name="description" id="description" value="<?php echo $desc ?>" placeholder="Description">
                </p>
                <p>
                    <label for="thankyou">Thank you message</label><br/>
                    <textarea name="thankyou" id="thankyou" placeholder="Thank you message"><?php echo $thankyouMsg?></textarea>
                </p>
                <p>
                    <input type="submit" name="submit" id="submit" value="<?php echo $submitText?>">
                </p>
            </fieldset>
        </form>
    </div>
    </body>
</html>