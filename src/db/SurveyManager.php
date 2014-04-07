<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 1:05 AM
 */

namespace sarhan\survey;

use Doctrine\ORM\UnitOfWork;

require_once(__DIR__ . "/../import.php");

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
        $this->save($survey);
        return $survey->getId();
    }

    /**
     * Update a survey
     * @param Survey $survey
     */
    public function updateSurvey(Survey $survey)
    {
        $this->save($survey);
    }

    /**
     * @param $surveyQuestionId
     * @param SurveyAnswer $surveyAnswer
     */
    public function addAnswer($surveyQuestionId, SurveyAnswer &$surveyAnswer)
    {
        $surveyAnswer->setSurveyQuestionId($surveyQuestionId);
        $this->save($surveyAnswer);
    }

    /**
     * @param $surveyId
     *
     * @return SurveyAnswerResult
     */
    public function getAnswers($surveyId)
    {
        $query = $this->getRepo(SurveyAnswer::NAME)->createNativeNamedQuery("find-answers-by-surveyId");
        $query->setParameter(0, $surveyId);

        /** @var SurveyAnswer[] $answers */
        $answers = $query->getResult();
        return new SurveyAnswerResult($answers);
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

    private function save(&$obj)
    {
        //Because we cascade all operations, we need
        //to do the correct operations based on detached or not.
        $state = $this->entityManager->getUnitOfWork()->getEntityState($obj);
        if($state == UnitOfWork::STATE_DETACHED)
        {
            $this->entityManager->merge($obj);
        }
        else
        {
            $this->entityManager->persist($obj);
        }

        $this->entityManager->flush();
    }

}

?> 