instagram-php-sdk
=================

A simple PHP wrapper class to bring all the fancy Instagram images into your application.

It's work in progress, so features might be missing.

Please tell me what you think about this little wrapper.


You'll need
-----------
- PHP available on your server
- A registered Instagram application. Simply head over to [instagram.com/developer](http://instagram.com/developer), sign in and register a new application.


Usage
-----
1) Download this wrapper class.

2) Include it in any of your projects.

3) Extract CLIENT ID, CLIENT SECRET and REDIRECT URI from your newly registered Instagram application ([instagram.com/developer/clients/manage](http://instagram.com/developer/clients/manage/))

4) Authenticate your app by clicking on the generated link and signing in.

So far in code:
```php
<?php
require_once('instagram.inc.php');

$instagram = new Instagram(array(
    "clientID" => "INSERT YOUR CLIENT ID HERE",
    "clientSecret" => "INSERT YOUR CLIENT SECRET HERE",
    "clientRedirectURI" => "INSERT YOUR REDIRECT URI HERE"
));

Please <a href = ".$instagram->getAuhtorizationURL().">log in</a> to Instagram in order to allow this app.
?>
``