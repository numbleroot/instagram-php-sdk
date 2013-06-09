<?php

/*
 *  Index
 *  -----
 *  This file shows a good way to integrate this API wrapper into your project after you received your access token.
 *  Include it, insert the required data and do stunning stuff with Instagram's images.
 */


/* Include the wrapper class. They're located in the same directory in this case again. */
require_once('instagram.inc.php');


/*
 *  Do some magic here to get the access token from your database.
 */
$accessTokenFromDatabase = "I AM AN ACCESS TOKEN";


/* Instanciate the Instagram object. This time with an access token from your database. */
$instagram = new Instagram(array(
    "clientID" => "PUT YOUR CLIENT ID HERE",
    "clientSecret" => "PUT YOUR CLIENT SECRET HERE",
    "clientRedirectURI" => "PUT YOUR REDIRECT URI HERE",
    "clientAccessToken" => $accessTokenFromDatabase
));


/* Your own Instagram feed is fetched in this example. */
$ownFeed = $instagram->getOwnFeed();
?>
<!DOCTYPE html>
<html>
    
    <head>
    
        <meta http-equiv = "content-type" content = "text/html; charset=utf-8" />
        <title>Instagram API Usage</title>

    </head>

    <body>

        <?php
        for($i = 0; $i < count($ownFeed->data); $i++)
        { ?>

            <img src = "<?php echo $ownFeed->data[$i]->images->standard_resolution->url; ?>" />

        <?php
        } ?>

    </body>

</html>