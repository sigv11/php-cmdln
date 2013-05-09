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
	private $home_dir;

	function __construct() {
		$this->home_dir = getenv("HOME");
		pcntl_sigprocmask(SIG_BLOCK, array(SIGINT, SIGUSR1, SIGUSR2));
	}

	private function do_help() {
		if (!isset($this->curr_cmd_args[0])) {
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

	private function read_history() {
		readline_read_history($this->home_dir . "/.php-cmdln.history");
	}

	private function write_history() {
		readline_write_history($this->home_dir . "/.php-cmdln.history");
	}

	private function print_prompt() {
		print "{$this->PROMPT}>>> ";
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

	private function do_complete($input, $index) {
		//$rl_info = readline_info();
		//$partial_input = substr($rl_info['line_buffer'], 0, $rl_info['point']);

		if (method_exists($this, "get_command_list")) {
			return $this->get_command_list();
		}

		return null;
	}

	private function complete() {
		readline_completion_function(array($this, 'do_complete'));
	}

	public function commandloop() {
		$this->read_history();

		while (!$this->STOP) {
			$this->complete();

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

		$this->write_history();
	}

}

?>
