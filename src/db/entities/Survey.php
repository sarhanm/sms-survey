<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad sarhan
 * Date: 3/30/14
 * Time: 7:34 PM
 */

namespace sarhan\survey;

use Doctrine\Common\Collections\ArrayCollection;

require_once(__DIR__ . "/../../import.php");


/**
 * @Entity @Table(name="Survey")
 **/
class Survey
{
    const NAME = 'sarhan\survey\Survey';
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $surveyName;

    /**
     * @Column(type="string",nullable=true)
     * @var string
     */
    protected $description;

    /**
     * @Column(type="string",length=500,nullable=true)
     * @var string
     */
    protected $thankYouMessage;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $created;

    /**
     * @Column(type="smallint")
     * @var int
     */
    protected $status;

    /**
     * This is actually a one-to-many since we have the unique=true constraint.
     * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
     *
     * @ManyToMany(targetEntity="SurveyQuestion",cascade={"all","persist"})
     * @JoinTable(name="survey_questions",
     *      joinColumns={@JoinColumn(name="survey_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="question_id", referencedColumnName="id", unique=true)}
     *      )
     *
     * @var ArrayCollection
     **/
    protected $questions;

    function __construct($id = null)
    {
        if($id && !is_null($id))
            $this->id = $id;

        $this->questions = new ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setStatus(SurveyStatus::active());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return SurveyStatus
     */
    public function getStatus()
    {
        return new SurveyStatus($this->status);
    }

    /**
     * @param SurveyStatus $status
     */
    public function setStatus(SurveyStatus $status)
    {
        $this->status = $status->getValue();
    }

    public function addQuestion(SurveyQuestion $q)
    {
        $this->questions->add($q);
    }

    /**
     * @return SurveyQuestion[]
     */
    public function getQuestions()
    {
        return $this->questions->toArray();
    }

    /**
     * @return string
     */
    public function getThankYouMessage()
    {
        return $this->thankYouMessage;
    }

    /**
     * @param string $thankYouMessage
     */
    public function setThankYouMessage($thankYouMessage)
    {
        $this->thankYouMessage = $thankYouMessage;
    }

    /**
     * @return string
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     * @param string $surveyName
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = strtolower($surveyName);
    }

    public function deleteQuestion($questionId)
    {
        $delKey = null;
        foreach($this->questions->toArray() as $key=>$val)
        {
            if($val->getId() == $questionId)
                $delKey = $key;
        }

        if(!is_null($delKey))
        {
            $this->questions->remove($delKey);
        }

    }

}

?> 