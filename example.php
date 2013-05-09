<?php

include 'lib/Cmd.php';

class SubCmdTest extends Cmd {

	private $command_list = array('subcmd');

	public function get_command_list() {
		return $this->command_list;
	}

	public function do_subcmd() {
		$args = func_get_args();

		print_r($args);
	}

}

class CmdTest extends Cmd {

	private $command_list = array('testcmd', 'hello');

	public function get_command_list() {
		return $this->command_list;
	}

	public function do_hello() {
		$args = func_get_args();

		print "World!\n";
	}

	public function help_hello() {
		print "Print 'World!'\n";
	}

	public function do_testcmd() {
		$args = func_get_args();

		$subc = new SubCmdTest;
		$subc->set_prompt($this->get_prompt() . ":testcmd");
		$subc->commandloop();
	}

}

$c = new CmdTest;
$c->commandloop();
?>
