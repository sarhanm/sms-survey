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

Class SurveyEntityManager
{
    /**
     * @var EntityManager
     */
    private static $entityManager = null;

    /**
     * @return EntityManager
     */
    public static function getEntityManager($isDevMode = false, $isTest = false)
    {
        if(!SurveyEntityManager::$entityManager)
        {
            // Create a simple "default" Doctrine ORM configuration for Annotations
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/entities"), $isDevMode);

            if($isTest)
            {
                $conn = array(
                    'driver' => 'pdo_sqlite',
                    'memory' => true
                );
            }
            else// database configuration parameters
            {
                $conn = array(
                    'driver' => 'pdo_sqlite',
                    'path' => __DIR__ . '/db.sqlite',
                );
            }

            // obtaining the entity manager
            SurveyEntityManager::$entityManager = EntityManager::create($conn, $config);
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