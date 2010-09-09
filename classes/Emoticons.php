<?php

class Emoticons {
	protected static $emoticons = array(
		array(
			'title' => 'Angel',
			'default' => 'O:-)',
			'alternatives' => array('O:)', 'O:-)'),
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
			'alternatives' => array(':P', ':-P'),
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
			'default' => ':\'-(',
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
			'alternatives' => array('=-O', ':-O'),
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
	);
	
	public static function getEmoticons() {
		return self::$emoticons;
	}
}

?>