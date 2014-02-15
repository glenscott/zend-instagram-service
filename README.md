zend-instagram-service
======================

A service for Zend Framework applications that gives access to the most recent photos from an Instagram account.

To use this service, you'll need to register your application with Instagram to obtain a client ID:  http://instagram.com/developer/clients/register/

Example for retriving 5 most recent photos from user glenscott:

```
	$this->instagram = new Custom_Service_Instagram($clientId);
	$photos = $this->instagram->getRecentPhotos($this->instagram->usernameToId('glenscott'), 5);
```
