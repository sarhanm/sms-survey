<?php
/**
 * Created by IntelliJ IDEA.
 * User: mohammad
 * Date: 3/31/14
 * Time: 2:56 PM
 */

namespace sarhan\survey;

require_once __DIR__ . "/../import.php";

use MyCLabs\Enum\Enum;


class QuestionType extends Enum
{
    const Text = 1;
    const StarRating = 2;
    const YesNo = 3;
}

?> 