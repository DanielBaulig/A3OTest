<?php

class *TestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new *TestSuite( '* Test Cases' );		
		$suite->addTestSuite( '*Test' );
		return $suite;
	}
}

class *Test extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
}