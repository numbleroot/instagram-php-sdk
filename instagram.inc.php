<?php
/**
 *  Instagram PHP SDK
 *  ------------------
 *  A wrapper class to integrate Instagram's data into your application.
 *  Include this class and start with the bootstrap file.
 *  After you received your access token, the index file is a proper way to include everything.
*/

class Instagram
{
    /**
     *  Defines Instagram's authentication base URI
     */
    const OAUTH_URI = "https://api.instagram.com/oauth/";

    /**
     *  Defines Instagram's API base URI
     */
    const API_URI = "https://api.instagram.com/v1/";

    /**
     *  The user's/script's client ID
     */
    private $clientID = 0;

    /**
     *  The user's/script's client secret
     */
    private $clientSecret = 0;

    /**
     *  The user's/script's client redirect URI
     */
    private $clientRedirectURI = "";

    /**
     *  The user's/script's access token
     */
    private $accessToken = 0;



    /**
     *  Constructor
     *
     *  @param array $initValues Required initialization information
     *  @return object Initialized object
     */
    public function __construct(array $initValues)
    {
        if(is_array($initValues))
        {
            $this->setClientID($initValues["clientID"]);
            $this->setClientSecret($initValues["clientSecret"]);
            $this->setClientRedirectURI($initValues["clientRedirectURI"]);

            if(!empty($initValues["clientAccessToken"])) $this->setAccessToken($initValues["clientAccessToken"]);
        }
    }



    // Getters and Setters.

    /**
     *  Returns the value of 'clientID'
     *
     *  @return string The client ID
     */
    public function getClientID()
    {
        return $this->clientID;
    }

    /**
     *  Sets the value of 'clientID'
     *
     *  @param string $value Value to be assigned to the variable
     *  @return boolean
     */
    public function setClientID(string $value)
    {
        if($value != "")
        {
            $this->clientID = $value;

            return true;
        }
        else return false;
    }


    /**
     *  Returns the value of 'clientSecret'
     *
     *  @return string The client secret
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     *  Sets the value of 'clientSecret'
     *
     *  @param string $value Value to be assigned to the variable
     *  @return boolean
     */
    public function setClientSecret(string $value)
    {
        if($value != "")
        {
            $this->clientSecret = $value;

            return true;
        }
        else return false;
    }


    /**
     *  Returns the value of 'clientRedirectURI'
     *
     *  @return string The client redirect URI
     */
    public function getClientRedirectURI()
    {
        return $this->clientRedirectURI;
    }

    /**
     *  Sets the value of 'clientRedirectURI'
     *
     *  @param string $value Value to be assigned to the variable
     *  @return boolean
     */
    public function setClientRedirectURI(string $value)
    {
        if($value != "")
        {
            $this->clientRedirectURI = $value;

            return true;
        }
        else return false;
    }


    /**
     *  Returns the value of 'accessToken'
     *
     *  @return string The access token
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     *  Sets the value of 'accessToken'
     *
     *  @param string $value Value to be assigned to the variable
     *  @return boolean
     */
    public function setAccessToken(string $value)
    {
        if($value != "")
        {
            $this->accessToken = $value;

            return true;
        }
        else return false;
    }



    // Helper methods.

    /**
     *  Builds the required authentication URL from the delivered data.
     *
     *  @return string Authorization URL
     */
    public function getAuthorizationURL()
    {
        $data = array(
            "client_id" => $this->getClientID(),
            "redirect_uri" => $this->getClientRedirectURI(),
            "response_type" => "code"
        );

        return self::OAUTH_URI."authorize/?".http_build_query($data);
    }

    /**
     *  Method to ultimately handle all created URLs. Called by every API method.
     *
     *  @param string $createdURL Valid API call
     *  @return array or boolean
     */
    public function getContent(string $createdURL)
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

    /**
     *  Retrieves an access_token in exchange for the authorization code.
     *
     *  @param string $code Received code from authorization call
     *  @return string
     */
    public function generateAccessToken(string $code)
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


    // API calls.

    /**
     *  Requests images from the popular public stream.
     *
     *  @return array or boolean
     */
    public function getPopularMedia()
    {
        $request = self::API_URI."media/popular?client_id=".$this->getClientID();

        return $this->getContent($request);
    }

    /**
     *  Method to get the images of your own feed.
     *
     *  @return array or boolean
     */
    public function getOwnFeed()
    {
        $request = self::API_URI."users/self/feed/?access_token=".$this->getAccessToken();

        return $this->getContent($request);
    }

    /**
     *  Method to get the recent images of the submitted user's feed.
     *
     *  @param integer $userID The searched user's ID
     *  @param integer $amount How many items you want to have back
     *  @param integer $earlierThan Only media before this UNIX timestamp
     *  @param integer $laterThan Only media after this UNIX timestamp
     *  @param integer $maxID Media before this image ID
     *  @param integer $minID Media after this image ID
     *  @return array or boolean
     */
    public function getUserFeed(integer $userID, integer $amount = false, integer $earlierThan = false, integer $laterThan = false, integer $maxID = false, integer $minID = false)
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