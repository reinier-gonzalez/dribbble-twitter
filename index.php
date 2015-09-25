<?php
	require 'vendor/autoload.php';
  require 'classes/baseapimodel.class.php';

  require 'config.php';

  // let's do it
  session_start();

  // authenticate on dribbble
  if ( isset( $_GET['code'] ) ) // is there an access code?
  {
    // ok let's check it
    $sCurrentState = $_SESSION['state'];
    if ( isset( $_GET['state'] ) && $_GET['state'] == $sCurrentState )
    {
      // cool, let's exchange it
      $sCode = $_GET['code'];
      //
      https://dribbble.com/oauth/token
      BaseApiModel::post( 'https://dribbble.com/oauth/token',
        array(
          'client_id' => DRIBBBLE_CLIENT_ID,
          'client_secret' => DRIBBB
        )
      ); . DRIBBBLE_CLIENT_ID . '&client_secret=' . DRIBBBLE_CLIENT_ID . '&code=' . $sCode );
      $oDribbbleClient = new Dribbble\Api\Client();
      $oDribbbleClient->

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