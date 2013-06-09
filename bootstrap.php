<?php

/*
 *  Bootstrap
 *  ---------
 *  Use this file to initially create the access token for your application.
 *  Include the wrapper class, provide the required information, handle the different cases
 *  and after all store the successfully generated access token in your database.
 */

/* Include the wrapper class. They're located in the same directory in this case. */
require_once('instagram.inc.php');


/* Instanciate the Instagram object. */
$instagram = new Instagram(array(
    "clientID" => "PUT YOUR CLIENT ID HERE",
    "clientSecret" => "PUT YOUR CLIENT SECRET HERE",
    "clientRedirectURI" => "PUT YOUR REDIRECT URI HERE"
));


/* Do different things based on the current state. */
if(!empty($_GET["code"]))
{
    /* You got the code. Now generate a valid access token. */
    $response = $instagram->generateAccessToken($_GET["code"]);
    $generatedAccessToken = $response->access_token;

    /*
     *  At this point you can do whatever you like to store your access token.
     *  Maybe the user table in your database is a good place.
     *  Or something like a 'bootstrap' table.
     */

    $status = "Hooray! Everything went fine and there's a shiny access token lying in your database now.";
}
else if(!empty($_GET["error"]) && !empty($_GET["error_reason"]) && !empty($_GET["error_description"]))
{
    /* What to say when an error occurs. */
    $status = "Oh snap! An error occurred.<br />Instagram's server say: \"".urldecode($_GET["error_description"])."\"";
}
else
{
    /* Initially put out the link to sign in. */
    $status = "Please <a href = \"".$instagram->getAuthorizationURL()."\">log in</a> to Instagram in order to allow this app.";
}
?>
<!DOCTYPE html>
<html>
    
    <head>
    
        <meta http-equiv = "content-type" content = "text/html; charset=utf-8" />
        <title>Instagram API Bootstrap</title>
    
    </head>

    <body>

        <?php echo $status; ?>

    </body>

</html>