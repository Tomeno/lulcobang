<?php

function smarty_modifier_linkify($string)
{
	return preg_replace('@https?://(.*?)(/(.*?))?(?=\s|$)@u', '<a href="\0" onclick=\'window.open("\0", "_blank"); return false;\'>\1</a>', $string);
}

?> 