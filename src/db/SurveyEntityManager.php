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
        global $DEFAULT_DB_CONFIG_FILE;

        if(!SurveyEntityManager::$entityManager)
        {
            // Create a simple "default" Doctrine ORM configuration for Annotations
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/entities"), $isDevMode);

            $fileName = __DIR__."/../../config/db-config.ini";
            $dbFileConfig = false;
            $dbConfig = false;
            if(file_exists($fileName))
            {
                $dbFileConfig = parse_ini_file($fileName, true);
            }

            if($dbFileConfig)
            {
                $dbConfig = $dbFileConfig[$isTest ? "test" : "production"];
            }

            //Try to default to test
            if(!$dbConfig && $isTest)
            {
                //default values to in-memory
                $dbConfig = array(
                    "driver"=>"pdo_sqlite",
                    "memory"=>"true"
                );
            }


            if(!$dbConfig)
            {
                throw new \Exception("No configuration file found at $fileName");
            }

            // obtaining the entity manager
            SurveyEntityManager::$entityManager = EntityManager::create($dbConfig, $config);
        }

        return SurveyEntityManager::$entityManager;
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