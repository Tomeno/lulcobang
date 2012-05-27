<?php

/**
 * abstract class for checker
 */
abstract class Checker {

	/**
	 * command
	 * 
	 * @var	Command
	 */
	protected $command = NULL;

	/**
	 * checking methods
	 *
	 * @var	array
	 */
	protected $precheckerParams = array();

	public function __construct(Command $command, array $precheckerParams) {
		$this->command = $command;
		$this->precheckerParams = $precheckerParams;
	}

	/**
	 * @return	boolean
	 */
	abstract public function check();

	/**
	 * adds message to command
	 *
	 * @param	mixed	$message
	 * @return	void
	 */
	protected function addMessage($message) {
		// vsetky prechecky su od systemu urcene pre aktualneho usera
		if (!$message['user']) {
			$message['user'] = User::SYSTEM;
		}
		if (!$message['toUser']) {
			$loggedUser = $this->command->getLoggedUser();
			$message['toUser'] = $loggedUser['id'];
		}
		$this->command->addMessage($message);
	}
}

?>