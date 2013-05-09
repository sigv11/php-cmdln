<?php

/**
 * PHP CLI Class
 *
 * @author    Vinod VM <vinod@segfault.in>
 * @copyright 2013 Vinod VM (http://segfault.in)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
Class Cmd {

	private $STOP = false;
	private $PROMPT = 'cmd';
	private $curr_input;
	private $curr_cmd;
	private $curr_cmd_args;

	private function do_help() {
		if(!isset($this->curr_cmd_args[0])) {
			$this->cmd_print("help <command>");
			return;
		}
		$cmd_name = $this->curr_cmd_args[0];

		$func = "help_$cmd_name";

		if (!method_exists($this, $func)) {
			$this->cmd_print("*** No help on '$cmd_name' ***");
			return;
		}

		$this->$func($this->curr_cmd_args);
	}

	private function exec() {

		$func = "do_" . $this->curr_cmd;

		if (!method_exists($this, $func)) {
			$this->cmd_print("'{$this->curr_cmd}' not implemented");
			return;
		}

		$this->$func($this->curr_cmd_args);
	}

	public function get_prompt() {
		return $this->PROMPT;
	}

	public function set_prompt($prompt) {
		$this->PROMPT = $prompt;
	}

	private function cmd_print($msg) {
		print $msg . "\n";
	}

	private function parse_cmd() {
		$this->curr_input = preg_replace('/\s+/', ' ', $this->curr_input);

		$tmp = explode(' ', $this->curr_input);

		$this->curr_cmd = $tmp[0];
		$this->curr_cmd_args = array_slice($tmp, 1);
	}

	public function commandloop() {
		while (!$this->STOP) {
			$this->curr_input = readline($this->PROMPT . '>>> ');
			if ($this->curr_input === false) {
				$this->STOP = true;
				$this->cmd_print('exit');
				continue;
			}

			if (empty($this->curr_input))
				continue;

			$this->curr_input = trim($this->curr_input);

			$this->parse_cmd();
			$this->exec();

			readline_add_history($this->curr_input);
		}
	}

}

?>
