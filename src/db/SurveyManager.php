<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 1:05 AM
 */

namespace sarhan\survey;

require_once(dirname(__FILE__) . "/../import.php");
#require_once(dirname(__FILE__) . "/../enum/QuestionType.php");

class SurveyManager
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    function __construct()
    {
        $this->entityManager = SurveyEntityManager::getEntityManager();
    }

    /**
     * Get a survey from the database
     * @param int $surveyId
     * @return Survey
     */
    public function getSurvey($surveyId)
    {
        return $this->entityManager->getRepository(Survey::NAME)->find($surveyId);
    }

    /**
     * @param SurveyStatus $status
     * @return Survey[]
     */
    public function getSurveys(SurveyStatus $status = null)
    {
        if(is_null($status))
            $status = SurveyStatus::active();

        return $this->entityManager->getRepository(Survey::NAME)->findBy(array('status'=> $status->getValue()));
    }

    /**
     * Create a survey
     *
     * @param Survey $survey
     * @return int
     */
    public function createSurvey(Survey $survey)
    {
        $this->entityManager->persist($survey);
        $this->entityManager->flush();
        return $survey->getId();
    }

    /**
     * Update a survey
     * @param Survey $survey
     */
    public function updateSurvey(Survey $survey)
    {
        $this->entityManager->merge($survey);
        $this->entityManager->flush();
    }

    /**
     * @param $surveyQuestionId
     * @param SurveyAnswer $surveyAnswer
     */
    public function addAnswer($surveyQuestionId, SurveyAnswer $surveyAnswer)
    {
        $surveyAnswer->setSurveyQuestionId($surveyQuestionId);
        $this->entityManager->persist($surveyAnswer);
    }

    public function getAnswers($surveyId)
    {

    }

    /**
     * Get a survey by the name of the survey
     * @param string $name
     * @return Survey
     */
    public function getSurveyByName($name)
    {
        return $this->getRepo(Survey::NAME)->findOneBy(array("surveyName" => strtolower($name)));
    }

    /**
     * @param $entity
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepo($entity)
    {
        $entityManager = SurveyEntityManager::getEntityManager();
        return $entityManager->getRepository($entity);
    }

}

?> 