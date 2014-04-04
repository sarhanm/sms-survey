<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 3:45 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../import.php";
use MyCLabs\Enum\Enum;

class SurveyExecutionState extends Enum
{
    const Unknown = 0;
    const InProgress = 1;
    const Completed = 2;
}

?> 