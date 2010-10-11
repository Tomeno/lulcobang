<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty count_words modifier plugin
 *
 * Type:     modifier<br>
 * Name:     count_words<br>
 * Purpose:  count the number of words in a text
 * @link http://smarty.php.net/manual/en/language.modifier.count.words.php
 *          count_words (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @return integer
 */
function smarty_modifier_position_class($actualPlayer, $player) {
    return GameUtils::getClassForPosition($actualPlayer, $player);
}

?>