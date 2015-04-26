<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 12:26 AM
 */

require_once __DIR__."/../src/import.php";

$entityManager = sarhan\survey\SurveyEntityManager::getEntityManager();
$helperSet =  \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
$GLOBALS['doctrine_survey_helperset'] = $helperSet;
return $helperSet;
?>