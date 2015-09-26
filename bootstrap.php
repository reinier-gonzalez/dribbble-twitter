<?php
  // DEPENDENCIES ////////////////////////////////////////////////////////
	require 'vendor/autoload.php';
  
  require 'classes/baseapimodel.class.php';

  require 'config.php';
  ////////////////////////////////////////////////////////////////////////

  session_start(); // let's do it

  // twitter
  \Codebird\Codebird::setConsumerKey( TWITTER_API_KEY, TWITTER_API_SECRET ); // static, see 'Using multiple Codebird instances'
  $oTwitter = \Codebird\Codebird::getInstance();