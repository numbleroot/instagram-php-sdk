<?php
/*
 *  Instagram PHP SDK
 *  ------------------
 *  A wrapper class to integrate Instagram's data into your application.
 *  Include this class and start with the bootstrap file.
 *  After you received your access token, the index file is a proper way to include everything.
*/

class Instagram
{
    const OAUTH_URI = "https://api.instagram.com/oauth/";
    const API_URI = "https://api.instagram.com/v1/";
    private $clientID = 0;
    private $clientSecret = 0;
    private $clientRedirectURI = "";
    private $accessToken = 0;



    /*
     *  Constructor.
     *  Pass an array of required authentication data.
     */
    public function __construct($initValues)
    {
        if(is_array($initValues))
        {
            $this->setClientID($initValues["clientID"]);
            $this->setClientSecret($initValues["clientSecret"]);
            $this->setClientRedirectURI($initValues["clientRedirectURI"]);

            if(!empty($initValues["clientAccessToken"])) $this->setAccessToken($initValues["clientAccessToken"]);
        }
    }



    /*
     *  Getters and Setters.
     */

    /* Returns the value of 'clientID' */
    public function getClientID()
    {
        return $this->clientID;
    }

    /* Sets the value of 'clientID' */
    public function setClientID($value)
    {
        if($value != "") $this->clientID = $value;
    }


    /* Returns the value of 'clientSecret' */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /* Sets the value of 'clientSecret' */
    public function setClientSecret($value)
    {
        if($value != "") $this->clientSecret = $value;
    }


    /* Returns the value of 'clientRedirectURI' */
    public function getClientRedirectURI()
    {
        return $this->clientRedirectURI;
    }

    /* Sets the value of 'clientRedirectURI' */
    public function setClientRedirectURI($value)
    {
        if($value != "") $this->clientRedirectURI = $value;
    }


    /* Returns the value of 'accessToken' */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /* Sets the value of 'accessToken' */
    public function setAccessToken($value)
    {
        if($value != "") $this->accessToken = $value;
    }



    /*
     *  Helper methods.
     */

    /* Builds the required authentication URL from the delivered data. */
    public function getAuthorizationURL()
    {
        return self::OAUTH_URI."authorize/?client_id=".$this->getClientID()."&redirect_uri=".$this->getClientRedirectURI()."&response_type=code";
    }

    /* Method to ultimately handle all created URLs. Called by every API method. */
    public function getContent($createdURL)
    {
        $jsonData = file_get_contents($createdURL);

        if($jsonData)
        {
            $data = json_decode($jsonData);

            if($data->meta->code == 200) return $data;
            else return false;
        }
        else return false;
    }

    /* Retrieves an access_token in exchange for the authorization code. */
    public function generateAccessToken($code)
    {
        $data = array(
            "client_id" => $this->getClientID(),
            "client_secret" => $this->getClientSecret(),
            "grant_type" => "authorization_code",
            "redirect_uri" => $this->getClientRedirectURI(),
            "code" => $code
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::OAUTH_URI."access_token");
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        return $response;
    }


    /*
     *  API calls.
     */

    /* Requests images from the popular public stream. */
    public function getPopularMedia()
    {
        $request = self::API_URI."media/popular?client_id=".$this->getClientID();

        return $this->getContent($request);
    }

    /* Method to get the images of your own feed. */
    public function getOwnFeed()
    {
        $request = self::API_URI."users/self/feed/?access_token=".$this->getAccessToken();

        return $this->getContent($request);
    }

    /* Method to get the recent images of the submitted user's feed. */
    public function getUserFeed($userID, $amount = false, $earlierThan = false, $laterThan = false, $maxID = false, $minID = false)
    {
        $request = self::API_URI."users/".$userID."/media/recent/?access_token=".$this->getAccessToken();

        if($amount !== false) $request .= "&count=".$amount;
        if($earlierThan !== false) $request .= "&max_timestamp=".$earlierThan;
        if($laterThan !== false) $request .= "&min_timestamp=".$laterThan;
        if($maxID !== false) $request .= "&max_id=".$maxID;
        if($minID !== false) $request .= "&min_id=".$minID;

        return $this->getContent($request);
    }
}

?>