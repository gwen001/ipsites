<?php

class UserExp
{
	const HOST = 'userexp.io';
	const BASE_URL = '/ip';
	const RESULT_MIN_LENGTH = 1000;

	private $url = '';

	private $ip = null;

	private $data = null;

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


	public function getHosts() {
		return $this->t_host;
	}
	private function addHost( $v ) {
		$this->t_host[] = trim( $v );
		return true;
	}

	private function constructUrl()
	{
		$tmp = explode( '.', $this->ip );
		$this->url = self::BASE_URL . '/' . $this->ip;
	}


	private function getData()
	{
		$this->constructUrl();

		$t_headers = array(
			'User-Agent' => 'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0 Iceweasel/38.7.1',
		);

		$http = new HttpRequest();
		$http->setHost( self::HOST );
		$http->setUrl( $this->url );
		$http->setHeaders( $t_headers );
		$http->request();

		$this->data = $http->getResult();
	}


	private function extractHost()
	{
		if( !$this->data ) {
			return false;
		}

		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		@$doc->loadHTML( $this->data );

		$xpath = new DOMXPath( $doc );
		$query = '//*[@class="iplista"]';
		$entries = $xpath->query( $query );
		//var_dump($entries);

		foreach( $entries as $entry ) {
			$this->addHost( $entry->nodeValue );
		}
	}


	public function findHost()
	{
		$this->getData();
		if( !$this->data ) {
			return false;
		}

		$this->extractHost();

		$this->t_host = array_unique( $this->t_host );
		sort( $this->t_host );

		return count( $this->t_host );
	}
}

?>
