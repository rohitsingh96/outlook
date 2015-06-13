<?php
 

  class oAuthService {
    private static $clientId = "7e502d0d-76a1-4aca-80ed-bf42f261d269";
    private static $clientSecret = "UCzyUpnWvLw4Y2BKksDGrWrgEbgbKg080C3c2Jv2tZo=";
    private static $authority = "https://login.microsoftonline.com";
    private static $authorizeUrl = '/common/oauth2/authorize?client_id=%1$s&redirect_uri=%2$s&response_type=code';
    private static $tokenUrl = "/common/oauth2/token";

    public static function getLoginUrl($redirectUri) {
      $loginUrl = self::$authority.sprintf(self::$authorizeUrl, self::$clientId, urlencode($redirectUri));
      error_log("Generated login URL: ".$loginUrl);
      return $loginUrl;
    }
	
	 public static function getTokenFromAuthCode($authCode, $redirectUri) {
  // Build the form data to post to the OAuth2 token endpoint
  $token_request_data = array(
    "grant_type" => "authorization_code",
    "code" => $authCode,
    "redirect_uri" => $redirectUri,
    "resource" => "https://outlook.office365.com/",
    "client_id" => self::$clientId,
    "client_secret" => self::$clientSecret
  );

  // Calling http_build_query is important to get the data
  // formatted as Azure expects.
  $token_request_body = http_build_query($token_request_data);
  error_log("Request body: ".$token_request_body);

  $curl = curl_init(self::$authority.self::$tokenUrl);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);

  $response = curl_exec($curl);
  error_log("curl_exec done.");

  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  error_log("Request returned status ".$httpCode);
  if ($httpCode >= 400) {
    return array('errorNumber' => $httpCode,
                 'error' => 'Token request returned HTTP error '.$httpCode);
  }

  // Check error
  $curl_errno = curl_errno($curl);
  $curl_err = curl_error($curl);
  if ($curl_errno) {
    $msg = $curl_errno.": ".$curl_err;
    error_log("CURL returned an error: ".$msg);
    return array('errorNumber' => $curl_errno,
                 'error' => $msg);
  }

  curl_close($curl);

  // The response is a JSON payload, so decode it into
  // an array.
  $json_vals = json_decode($response, true);
  error_log("TOKEN RESPONSE:");
  foreach ($json_vals as $key=>$value) {
    error_log("  ".$key.": ".$value);
  }

  return $json_vals;
}
	
  }
  
 
  
  
  
  
?>