<?php

class IpSites
{
	private $ip = null;

	private $t_wrapper = array();

	private $t_host = array();


	public function setIp( $v )
	{
		if( Utils::isIp($v) ) {
			$this->ip = $v;
			return true;
		} else {
			return false;
		}
	}


	public function getWrappers() {
		return $this->t_wrapper;
	}
	public function addWrapper( $v )
	{
		if( is_file($v.'.php') ) {
			echo "Wrapper ".$v." added\n";
			$this->t_wrapper[] = $v;
			return true;
		} else {
			echo "Wrapper ".$v." not found!\n";
			return false;
		}
	}


	public function run()
	{
		echo "\n";

		foreach( $this->t_wrapper as $w )
		{
			if( !method_exists($w,'findHost') ) {
				echo $w." skipped\n";
				continue;
			}

			$c = new $w();
			$c->setIp( $this->ip );
			$c->findHost();
			$this->t_host = array_merge( $this->t_host, $c->getHosts() );
		}

		$this->t_host = array_unique( $this->t_host );
		sort( $this->t_host );
		//var_dump($this->t_host);

		$this->verifyHost();

		$this->printResult();
	}


	public function verifyHost()
	{
		$t_host = array();

		foreach( $this->t_host as $h )
		{
			$ping = null;
			exec( 'host '.$h.' | grep "has address"', $ping );

			if( is_array($ping) && count($ping) && strstr($ping[0],$this->ip) ) {
				$t_host[$h] = true;
			} else {
				$t_host[$h] = false;
			}
		}

		$this->t_host = $t_host;
	}


	private function printResult()
	{
		foreach( $this->t_host as $h=>$ping )
		{
			if( $ping ) {
				Utils::_print( $h.' found', 'green' );
			} else {
				Utils::_print( $h, 'dark_grey' );
			}

			echo "\n";
		}
	}
}

?>
