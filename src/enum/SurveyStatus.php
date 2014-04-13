<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:36 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../import.php";

use MyCLabs\Enum\Enum;


/**
 * Class SurveyStatus
 *
 * @method static SurveyStatus active()
 * @method static SurveyStatus disabled()
 *
 * @package sarhan\survey
 */
class SurveyStatus extends Enum
{
    const active = 1;
    const disabled = 2;

    public function getName()
    {
        foreach(SurveyStatus::toArray() as $key => $val)
        {
            if($this->value == $val)
            {
                return $key;
            }
        }

        return $this->getValue();
    }
}

?> 