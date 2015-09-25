<?php
  require 'bootstrap.php';

  // let's do it
  session_start();

  // authenticate on dribbble
  if ( isset( $_GET['code'] ) ) // got an access code?
  {
    // ok let's check it
    $sCurrentState = $_SESSION['state'];
    if ( isset( $_GET['state'] ) && $_GET['state'] == $sCurrentState )
    {
      // cool, let's exchange it
      $sCode = $_GET['code'];
      $sResponse = BaseApiModel::post( 'https://dribbble.com/oauth/token',
        array(
          'client_id'     => DRIBBBLE_CLIENT_ID,
          'client_secret' => DRIBBBLE_CLIENT_SECRET,
          'redirect_uri'  => DRIBBBLE_REDIRECT_URL,
          'code'          => $sCode
        )
      );

      $oResponse = json_decode( $sResponse ); // TODO: try catch
      if ( $oResponse && isset( $oResponse->access_token ) ) // json formatted response with access_token?
      {
        // awesome, let's do get the 'following' user
        $sAccessToken = $oResponse->access_token;

        $oDribbbleClient = new Dribbble\Api\Client();

        // TODO: uff, all this shit is too much trouble just to be able to use this wrapper, add auth and do pull request to dribble-php
        $oDribbbleClient::$CURL_OPTS[CURLOPT_HTTPHEADER] = ["Authorization: Bearer $sAccessToken"];

        $oFollowers = $oDribbbleClient->getPlayerFollowing( 'baymonsters' );

      }

    }
    else die( "Damn Hackers!" );
  }
  else
  {
    $sState = $_SESSION['state'] = uniqid( "", true );
    header( 'https://dribbble.com/oauth/authorize?client_id=' . DRIBBBLE_CLIENT_ID . '&state=' . $sState );
  }


  $oDribbbleClient = new Dribbble\Api\Client();

  try {
    $oFollowers = $oDribbbleClient->getPlayerFollowing( 'baymonsters' );
    echo "<pre>";
    print_r( $oFollowers );
    echo "</pre>";
  }
  catch (Dribbble\Exception $e) {
    printf('%d: %s', $e->getCode(), $e->getMessage());
  }
?>