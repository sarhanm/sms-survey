<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 4/12/14
 * Time: 10:45 PM
 */

require_once __DIR__."/../../import.php";

$surveyId = $_REQUEST['id'];

$manager = new \sarhan\survey\SurveyManager();
$survey = $manager->getSurvey($surveyId);
if(is_null($survey))
{
    header("Location:index.php");
}

$statusId = $_REQUEST['status'];
if($statusId && is_numeric($statusId))
{
    $status = new \sarhan\survey\SurveyStatus($statusId);
    $survey->setStatus($status);
    $manager->updateSurvey($survey);
}

header("Location:index.php");

?>