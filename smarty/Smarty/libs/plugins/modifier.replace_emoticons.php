<?php

/**
 * modifier for replacing emoticons in text
 *
 * @param string $text
 * @return string
 */
function smarty_modifier_replace_emoticons($text) {
	return Utils::replaceEmoticonsInText($text);
}

?> 