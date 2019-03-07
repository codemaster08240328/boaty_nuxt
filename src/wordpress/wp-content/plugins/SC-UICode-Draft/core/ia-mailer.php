<?php

/**
 *
 * Author: Ivan acog
 */

class IAMailer {
	private $sender;
	private $recepient;
	private $subject;
	private $message;

	private $headers;

	function __construct($setup) {

	}

	function setup($setup) {
		foreach ($setup as $key => $value) {
			if (isset($this->$key)) {
				$this->$key = $value;
			}
		}
	}

	function set_header($header) {
		
	}

	function send() {
		return mail($this->sender, $this->subject, $this->recepient, explode("\r\n", $headers));
	}
}