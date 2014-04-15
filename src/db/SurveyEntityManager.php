<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 12:14 AM
 */

namespace sarhan\survey;


require_once __DIR__ . "/../import.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use SebastianBergmann\Exporter\Exception;

Class SurveyEntityManager
{

    const DB_CONFIG_FILE= "survey-db-config.php";

    /**
     * @var EntityManager
     */
    private static $entityManager = null;

    /**
     * @param bool $isDevMode
     * @param bool $isTest
     *
     * @throws \Exception
     * @return EntityManager
     */
    public static function getEntityManager($isDevMode = false, $isTest = false)
    {
        if(!SurveyEntityManager::$entityManager)
        {
            // Create a simple "default" Doctrine ORM configuration for Annotations
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/entities"), $isDevMode);

            $dbConfig = self::getDatabaseConfig();

            //Default db values only for test.
            if(is_null($dbConfig) && $isTest)
            {
                //default values to in-memory
                $dbConfig = array(
                    "test" => array(
                        "driver"=>"pdo_sqlite",
                        "memory"=>"true")
                );
            }

            if(is_null($dbConfig))
            {
                throw new \Exception("Was not able to find survey-db-config.php anywhere from the current working directory");
            }

            $key = $isTest ? "test" :"production";

            if(!array_key_exists($key,$dbConfig))
            {
                throw new \Exception("$key not specified in DB config array");
            }

            SurveyEntityManager::$entityManager = EntityManager::create($dbConfig[$key], $config);
        }

        return SurveyEntityManager::$entityManager;
    }

    private static function getDatabaseConfig()
    {
        //We'll try to get the DB config in the following ways:
        // 1. See if the file path is defined as a constant 'SURVEY_DB_CONFIG_PATH'
        // 2. See if the survey-db-config.php in in the include_path
        // 3. See if the file is in the current working directory OR in a config folder
        // in the current working directory OR in the survey-sms' config folder.

        if(defined("SURVEY_DB_CONFIG_PATH") && file_exists(SURVEY_DB_CONFIG_PATH))
        {
            $dbConfig = self::include_file(SURVEY_DB_CONFIG_PATH);
            if(!is_null($dbConfig))
            {
                return $dbConfig;
            }
        }

        //Check the include paths to see if the file exists
        $includePath = stream_resolve_include_path(self::DB_CONFIG_FILE);
        if($includePath && file_exists($includePath))
        {
            $dbConfig = self::include_file($includePath);
            if(!is_null($dbConfig))
            {
                return $dbConfig;
            }
        }

        $directories = array(getcwd(), getcwd() . DIRECTORY_SEPARATOR . 'config',
        __DIR__.'/../../config');

        $dbFile = null;
        foreach ($directories as $directory) {
            $dbFile = $directory . DIRECTORY_SEPARATOR . self::DB_CONFIG_FILE;
            if (file_exists($dbFile)) {
                break;
            }
        }

        if ( ! file_exists($dbFile)) {
            return null;
        }

        if ( ! is_readable($dbFile)) {
            return null;
        }

        $dbConfig = self::include_file($dbFile);
        if(!is_null($dbConfig))
        {
            return $dbConfig;
        }

        throw new \Exception("Could not find survey-db-config.php in any location");
    }

    /**
     * Includes a file and returns the value from the include.
     * @param $file
     *
     * @return string[]|null
     */
    private static function include_file($file)
    {
        $dbConfig = include($file);
        if($dbConfig && is_array($dbConfig))
        {
            return $dbConfig;
        }

        return null;

    }

    public static function reset()
    {
        SurveyEntityManager::$entityManager = null;
    }

    public static function testSetup()
    {
        $entityManager = SurveyEntityManager::getEntityManager(true, true);

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($classes);
    }

    public static function testTearDown()
    {
        $entityManager = SurveyEntityManager::getEntityManager(true, true);

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $schemaTool->dropDatabase();

        //Ensure we do not leak across tests.
        SurveyEntityManager::$entityManager = null;
    }

    /**
     * Detaches all entties from the current entity manager.
     */
    public static function testClear()
    {
        $em = SurveyEntityManager::getEntityManager(true, true);
        $em->clear();
    }
}

?>