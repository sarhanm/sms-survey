<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/30/14
 * Time: 7:39 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../../enum/QuestionType.php";

/**
 * @Entity @Table(name="SurveyQuestion")
 **/
class SurveyQuestion
{

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * The question to be asked
     * @Column(type="string")
     * @var string
     */
    protected $question;

    /**
     * What type of question it is.
     * @Column(type="smallint")
     * @var QuestionType
     */
    protected $type;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return QuestionType
     */
    public function getType()
    {
        return new QuestionType($this->type);
    }

    /**
     * @param QuestionType $type
     */
    public function setType(QuestionType $type)
    {
        $this->type = $type->getValue();
    }


}

?> 