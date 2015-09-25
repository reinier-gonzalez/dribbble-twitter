<?php
	require 'vendor/autoload.php';
  require 'config.php';

  session_start();

  // authenticate on dribbble
  if ( isset($_GET['code'] ) ) // is there an access code?
  {
    // ok let's check it
    $sCSRFToken =
    if ( iss)

  }
  else
  {
    $sCSRFToken = $_SESSION['state'] = uniqid( "", true );
    header( 'https://dribbble.com/oauth/authorize?client_id=' . DRIBBBLE_CLIENT_ID . '&state=' . $sCSRFToken );
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