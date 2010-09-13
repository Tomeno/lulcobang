<?php

class Emoticons {
	protected static $emoticons = array(
		array(
			'title' => 'Kiss',
			'default' => ':-*',
			'alternatives' => array(':-*'),
			'image' => 'kiss.jpg',
		),
		array(
			'title' => 'Blushing',
			'default' => ':-[',
			'alternatives' => array(':-['),
			'image' => 'blushing.jpg',
		),
		array(
			'title' => 'Laughing out loud (LOL)',
			'default' => ':-D',
			'alternatives' => array(':D', ':-D', 'LOL', 'lol', ':))', ':-))'),
			'image' => 'laughing_out_loud.jpg',
		),
		array(
			'title' => 'Angel',
			'default' => 'O:-)',
			'alternatives' => array('O:)', 'O:-)', 'o:)', 'o:-)'),
			'image' => 'angel.jpg',
		),	
		array(
			'title' => 'Smile',
			'default' => ':-)',
			'alternatives' => array(':)', ':-)'),
			'image' => 'smile.jpg'
		),
		array(
			'title' => 'Sad',
			'default' => ':-(',
			'alternatives' => array(':(', ':-('),
			'image' => 'sad.jpg'
		),
		array(
			'title' => 'Blinking',
			'default' => ';-)',
			'alternatives' => array(';)', ';-)'),
			'image' => 'blinking.jpg',
		),
		array(
			'title' => 'Tongue out',
			'default' => ':-P',
			'alternatives' => array(':P', ':-P', ':p', ':-p'),
			'image' => 'tongue_out.jpg',
		),
		array(
			'title' => 'Laughing',
			'default' => '*JOKINGLY*',
			'alternatives' => array('*JOKINGLY*', '*jokingly*'),
			'image' => 'laughing.jpg',
		),
		array(
			'title' => 'Crying',
			'default' => ":\'-(",
			'alternatives' => array(':\'(', ':\'-('),
			'image' => 'crying.jpg',
		),
		array(
			'title' => 'Kissed',
			'default' => '*KISSED*',
			'alternatives' => array('*KISSED*', '*kissed*'),
			'image' => 'kissed.jpg',
		),
		array(
			'title' => 'Nothing to say',
			'default' => ':-$',
			'alternatives' => array(':-$', ':-|'),
			'image' => 'nothing_to_say.jpg',
		),
		array(
			'title' => 'Surprised',
			'default' => '=-O',
			'alternatives' => array('=-O', ':-O', '=-o', ':-o'),
			'image' => 'surprised.jpg',
		),
		array(
			'title' => 'Cool',
			'default' => '8-)',
			'alternatives' => array('8)', '8-)'),
			'image' => 'cool.jpg',
		),
		array(
			'title' => 'Listening to music',
			'default' => '[:-}',
			'alternatives' => array('[:-}'),
			'image' => 'listening_to_music.jpg',
		),
		array(
			'title' => 'Falling asleep',
			'default' => '*TIRED*',
			'alternatives' => array('*TIRED*', '*tired*'),
			'image' => 'falling_asleep.jpg',
		),
		array(
			'title' => 'Gross',
			'default' => ':-!',
			'alternatives' => array(':-!'),
			'image' => 'gross.jpg',
		),
		array(
			'title' => 'Stop',
			'default' => '*STOP*',
			'alternatives' => array('*STOP*', '*stop*'),
			'image' => 'stop.jpg',
		),
		array(
			'title' => 'Mad',
			'default' => ':-\\',
			'alternatives' => array(':-\\', ':-/'),
			'image' => 'mad.jpg',
		),
		array(
			'title' => 'Can\'t say / Shut up',
			'default' => ':-X',
			'alternatives' => array(':-X', ':-x'),
			'image' => 'shut_up.jpg',
		),
		array(
			'title' => 'Scream',
			'default' => ':-@',
			'alternatives' => array(':-@'),
			'image' => 'scream.jpg',
		),
		array(
			'title' => 'Kissing',
			'default' => '*KISSING*',
			'alternatives' => array('*KISSING*', '*kissing*'),
			'image' => 'kissing.jpg',
		),
		array(
			'title' => 'Devil',
			'default' => ']:->',
			'alternatives' => array(']:->'),
			'image' => 'devil.jpg',
		),
		array(
			'title' => 'Thank you',
			'default' => '@}->--',
			'alternatives' => array('@}->--'),
			'image' => 'thank_you.jpg',
		),
		array(
			'title' => 'Bomb',
			'default' => '@=',
			'alternatives' => array('@='),
			'image' => 'bomb.jpg',
		),
		array(
			'title' => 'Drinking',
			'default' => '*DRINK*',
			'alternatives' => array('*DRINK*', '*drink*'),
			'image' => 'drinking.jpg',
		),
		array(
			'title' => 'Thumbs up',
			'default' => '*THUMBS UP*',
			'alternatives' => array('*THUMBS UP*', '*thumbs up*'),
			'image' => 'thumbs_up.jpg',
		),
		array(
			'title' => 'In love',
			'default' => '*IN LOVE*',
			'alternatives' => array('*IN LOVE*', '*in love*'),
			'image' => 'in_love.jpg',
		),
	);
	
	public static function getEmoticons() {
		return self::$emoticons;
	}
}

?>