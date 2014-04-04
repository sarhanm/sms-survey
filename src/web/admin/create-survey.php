<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/4/14
 * Time: 1:21 AM
 */

namespace sarhan\survey\web\admin;

require_once __DIR__."/../../import.php";

//TODO: Create survey and move on to creating questions for the survey
use sarhan\survey\Survey;
use sarhan\survey\SurveyManager;

if($_REQUEST['submit'])
{
    $survey = new Survey();
    $survey->setThankYouMessage($_REQUEST['thankyou']);
    $survey->setDescription($_REQUEST['description']);
    $survey->setSurveyName($_REQUEST['name']);
    $manager = new SurveyManager();
    $manager->createSurvey($survey);

    header("Location: index.php");
}

?>
<html>
    <head>

    </head>
    <body>
    <div class="yui3-widget-bd">
        <form>
            <fieldset>
                <p>
                    <label for="name">Survey Name</label><br/>
                    <input type="text" name="name" id="name" value="" placeholder="Survey Name">
                </p>
                <p>
                    <label for="description">Description</label><br/>
                    <input type="text" name="description" id="description" value="" placeholder="Description">
                </p>
                <p>
                    <label for="thankyou">Thank you message</label><br/>
                    <textarea name="thankyou" id="thankyou" placeholder="Thank you message"></textarea>
                </p>
                <p>
                    <input type="submit" name="submit" id="submit" value="Add Survey">
                </p>
            </fieldset>
        </form>
    </div>
    </body>
</html>