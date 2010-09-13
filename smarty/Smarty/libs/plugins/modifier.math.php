<?php

function smarty_modifier_math($string)
{
eval ('$result='.$string.';');
return $result;
}

?>