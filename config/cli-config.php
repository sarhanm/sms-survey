<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 12:26 AM
 */

require_once __DIR__."/../src/import.php";

$entityManager =SurveyEntityManager::getEntityManager();
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
?>