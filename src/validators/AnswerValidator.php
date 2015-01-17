<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:11 PM
 */

namespace sarhan\survey;

/**
 * Validator for answers. Each questionType has its own answer validator
 * Interface AnswerValidator
 */
interface AnswerValidator
{
    /**
     * @param $answer
     * @return boolean
     */
    public function isValid($answer);

    /**
     * @param $answer
     *
     * @return string
     */
    public function normalize($answer);

    /**
     * @return string Text to present to the user on how to format their answer
     */
    public function getHelperText();
}


?> 