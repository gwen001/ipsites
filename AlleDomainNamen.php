<?php

class AlleDomainNamen
{
	const HOST = 'alle-domeinnamen.xyz';
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
		$this->url = '/' . $tmp[0] . '/' . $tmp[1] . '/' . $tmp[2] . '/' . $this->ip . '.html';
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

		if( $http->getResultLength() < self::RESULT_MIN_LENGTH )
		{
			preg_match_all( '#[a-z0-9]{40}#', $http->getResult(), $matches );
			//var_dump($matches);

			foreach( $matches[0] as $m )
			{
				$http = new HttpRequest();
				$http->setCookies( 'nocontent='.$m );
				$http->setHost( self::HOST );
				$http->setUrl( $this->url );
				$http->setHeaders( $t_headers );
				$http->request();

				if( $http->getResultLength() > self::RESULT_MIN_LENGTH ) {
					$this->data = $http->getResult();
				}
			}
		}
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
		$query = '//*[@class="col-sm-6"]';
		$entries = $xpath->query( $query );
		//var_dump($entries);

		foreach( $entries as $entry ) {
			$str = trim( $entry->nodeValue );
			$tmp = explode( "\n", $str );

			foreach( $tmp as $h ) {
				$this->addHost( $h );
			}
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
