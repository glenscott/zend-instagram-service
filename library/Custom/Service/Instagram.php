<?php

/**
 * @see Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Simple wrapper around Instagram API
 *
 * @author 	   Glen Scott <glen@glenscott.co.uk>
 * @category   Custom
 * @package    Zend_Service
 * @subpackage Instagram
 */
class Custom_Service_Instagram 
{
    /**
     * Zend_Http_Client Object
     *
     * @var     Zend_Http_Client
     * @access  protected
     */
    protected $_client;

    /**
     * Array that contains parameters being used by the webservice
     *
     * @var     array
     * @access  protected
     */
    protected $_params;

    /**
     * Holds error information (e.g., for handling simplexml_load_string() warnings)
     *
     * @var     array
     * @access  protected
     */
    protected $_error = null;

    /*
     * Instagram API client ID
     *
     * @var string
     */
    protected $_clientId;

    public function __construct( $clientId )
    {
    	$this->_clientId = $clientId;
    }

    /**
     * Set Http Client
     *
     * @param Zend_Http_Client $client
     */
    public function setHttpClient(Zend_Http_Client $client)
    {
        $this->_client = $client;
    }

    /**
     * Get current http client.
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if($this->_client == null) {
            $this->lazyLoadHttpClient();
        }
        return $this->_client;
    }

    /**
     * Lazy load Http Client if none is instantiated yet.
     *
     * @return void
     */
    protected function lazyLoadHttpClient()
    {
        $this->_client = new Zend_Http_Client();
    }


    public function usernameToId($username)
    {
        $this->getHttpClient()->setUri("https://api.instagram.com/v1/users/search?q=" . urlencode($username) . 
        	"&client_id=" . urlencode($this->_clientId) . "&count=1");

        $response     = $this->getHttpClient()->request();
        $responseBody = json_decode($response->getBody());

        if ($responseBody->meta->code == 200)
        {
        	if ( count($responseBody->data) )
        	{
	        	return $responseBody->data[0]->id;
	        }
	        else
	        {
	            require_once 'Zend/Http/Client/Exception.php';
	            throw new Zend_Http_Client_Exception('Instagram username not found');
	        }
        }
        else
        {
            require_once 'Zend/Http/Client/Exception.php';
            throw new Zend_Http_Client_Exception('API return error code' . $responseBody->meta->code);
        }

    }

    /**
     * Get most recent photo URLs for a particular user
     *
     * @param int $userId Instagram User ID
     * @param int $count Number of photos to return
     * @return array A list of photo URLs
     */
    public function getRecentPhotos($userId, $count = 6)
    {
        $this->getHttpClient()->setUri("https://api.instagram.com/v1/users/" . urlencode($userId) . 
        	"/media/recent/?client_id=" . urlencode($this->_clientId));

        $response     = $this->getHttpClient()->request();
        $responseBody = json_decode($response->getBody());

        if ($responseBody->meta->code == 200)
        {
            $photoCount = count($responseBody->data);

           	if ( $photoCount )
        	{
        		$photoUrls = array();

                if ( $photoCount < $count ) {
                    $count = $photoCount;
                }
                
        		for ( $i = 0; $i < $count; $i++ )
        		{
        			$photoUrls[] = $responseBody->data[$i]->images->thumbnail->url;
        		}

        		return $photoUrls;
	        }
	        else
	        {
	            require_once 'Zend/Http/Client/Exception.php';
	            throw new Zend_Http_Client_Exception('No photos for user found');
	        }
        }
        else
        {
            require_once 'Zend/Http/Client/Exception.php';
            throw new Zend_Http_Client_Exception('API return error code' . $responseBody->meta->code);
        }

    }
}
