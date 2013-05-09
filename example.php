<?php

include 'lib/Cmd.php';

class SubCmdTest extends Cmd {

	public function do_subcmd($args) {
		$args = func_get_args();

		print_r($args);
	}

}

class CmdTest extends Cmd {

	public function do_hello($args) {
		$args = func_get_args();

		print "World!\n";
	}

	public function help_hello()
	{
		print "Print 'World!'\n";
	}

	public function do_testcmd($args) {
		$args = func_get_args();

		$subc = new SubCmdTest;
		$subc->set_prompt($this->get_prompt() . ":testcmd");
		$subc->commandloop();
	}

}

$c = new CmdTest;
$c->commandloop();
?>
