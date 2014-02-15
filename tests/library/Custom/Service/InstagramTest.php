<?php

class InstagramTest extends PHPUnit_Framework_TestCase
{
	protected $instagram;

	protected function setUp()
	{
		$clientId = '123';

		$this->instagram = new Custom_Service_Instagram($clientId);
	}

	public function testInstantiation()
	{
		$this->assertTrue( is_a( $this->instagram, 'Custom_Service_Instagram' ) );
	}

	public function testValidUsernameTranslatesToId()
	{
		$this->assertEquals( '3295411', $this->instagram->usernameToId('glenscott') );
	}

	/**
     * @expectedException Zend_Http_Client_Exception
     */
	public function testNonExistantUsernameCausesException()
	{
		$this->instagram->usernameToId('42384829423749823');
	}

	public function testPhotosReturnList()
	{
		$this->assertTrue(is_array($this->instagram->getRecentPhotos('3295411')));
	}

	public function testPhotosReturnSixItems()
	{
		$this->assertEquals(6, count($this->instagram->getRecentPhotos('3295411')));
	}

	public function testPhotosWhenLessThanSixItems()
	{
		$adapter = new Zend_Http_Client_Adapter_Test();
		$client  = new Zend_Http_Client('http://www.example.com', array(
		    'adapter' => $adapter
		));

		// two photos returned
		$data = array( array( 'images' => array( 'thumbnail' => array( 'url' => 'http://url1' ))),
					   array( 'images' => array( 'thumbnail' => array( 'url' => 'http://url2' ))),
		);

		$responseBody = json_encode( array( 'meta' => array( 'code' => '200' ),
											'data' => $data,
		));

		// Set the expected response
		$adapter->setResponse(
		    "HTTP/1.1 200 OK"        . "\r\n" .
		    "Content-type: text/html; charset=UTF-8" . "\r\n" .
		                               "\r\n" .
		    $responseBody);

		$this->instagram->setHttpClient($client);

		$this->assertEquals(count($data), count($this->instagram->getRecentPhotos('3295411')));
	}
}