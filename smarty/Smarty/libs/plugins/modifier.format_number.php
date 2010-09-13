<?php

/**
 * formatovanie cisel
 * vrati sformatovane cislo
 *
 * @param string $string, int $decimal
 * @return string
 */

function smarty_modifier_format_number($string, $decimal = 0)
{
	if (!is_numeric($string))
	{
		return $string;
	}
	
	return Util::formatNumber($string, $decimal);
}

?>