<?php
  require 'bootstrap.php';

  // TODO: create a dribble API wrapper class that works.... not like dribbble-php :p ... thank you guys.

  session_start(); // let's do it

  $sStartService = 'dribbble'; // okeeeyy.....
  $sService = isset( $_GET['service'] ) ? $_GET['service'] : $sStartService; // makes sense

  switch ( $sService ) // so, where are you coming from? .... sorry, what about HTTP_REFERRER? ... fuck off!
  {
    case 'dribbble':
      // get followees from dribbble
      if ( isset( $_GET['code'] ) ) // got an access code? LOOKAT: XXX
      {
        // ok let's check it
        $sCurrentState = $_SESSION['dribbble_state'];
        if ( isset( $_GET['state'] ) && $_GET['state'] == $sCurrentState ) // no wormy-trojin-viris-crackis-hackis going on?
        {
          // cool, let's exchange the oauth code
          $sCode = $_GET['code'];
          $sResponse = BaseApiModel::post( 'https://dribbble.com/oauth/token', [
            'client_id'     => DRIBBBLE_CLIENT_ID,
            'client_secret' => DRIBBBLE_CLIENT_SECRET,
            //'redirect_uri'  => DRIBBBLE_REDIRECT_URL,
            'code'          => $sCode
          ]);

          $oResponse = json_decode( $sResponse ); // TODO: try aaaaaaaaaaaaand... catch!
          if ( $oResponse && isset( $oResponse->access_token ) ) // alrighty formatted json response with access_token and everything?
          {
            $sAccessToken = $oResponse->access_token; // oh! look! an access token!!!

            // awesome, let's finally get the followees...
            $sResponse = BaseApiModel::get( "https://api.dribbble.com/v1/user/following", [], ["Authorization: Bearer $sAccessToken"] ); // :o
            $aFollowees = json_decode( $sResponse ); // TODO: try aaaaaaaaaaaaand... catch!

            if ( $aFollowees ) // json-formatted with followers data? you better work....
            {
              // uuuuuiiiuoww...
              $aDribbbleFollowees = [];
              foreach( $aFollowees as $oFollowee ) // yes sir, yes sir...
              {
                // now this is boring...
                $sTwitterUrl = isset( $oFollowee->followee->links->twitter ) ? $oFollowee->followee->links->twitter : '';
                $sTwitterScreenName = preg_replace( '#.*\/(.*)#s', "\\1" , $sTwitterUrl ); // oh, oh, oh... @Dribble: it's a bitch I have to be regexping for this

                if ( $sTwitterScreenName ) // has a twitter URL?
                {
                  $aDribbbleFollowees[] = $sTwitterScreenName; // more and more power, power, poweeeeer ha ha haaaa
                }
              }
              $_SESSION['dribbble_followees'] = $aDribbbleFollowees; // yup, got them all!

              // finally!! Let's follow him/her/it on twitter!!! ..........
              // Fuck! I forgot! oauth again :(

              // let's get authorized on twitter... here we go again:
              // get the request token
              $oResponse = $oTwitter->oauth_requestToken([
                'oauth_callback' => TWITTER_REDIRECT_URL // hey, get back to me!
              ]);

              // got it? store it, the token, the tokeeeeeeeeeeeen
              $oTwitter->setToken( $oResponse->oauth_token, $oResponse->oauth_token_secret );

              $_SESSION['oauth_token']        = $oResponse->oauth_token;
              $_SESSION['oauth_token_secret'] = $oResponse->oauth_token_secret;
              $_SESSION['oauth_verify']       = true; // yeah bitch, just do it!!

              // redirect to auth website
              $sAuthUrl = $oTwitter->oauth_authorize();
              header( "Location: $sAuthUrl" ); // wait, wait, wait, i forgot to tell you... doesn't matter

              // oh shit! what I'm I doing here? GOTO: XXX

            } // look... no followee no biz.
          } // so sad, no access token :(
        } else die( "Damn Hackers!" ); // gotcha!
      } // TA-TA-TAAAAAAAAAN:
      else // 1st movement: OAUTH dance
      {
        // let's get authorized on dribbble ...
        $sState = $_SESSION['dribbble_state'] = uniqid( "", true );
        header( 'Location: https://dribbble.com/oauth/authorize?client_id=' . DRIBBBLE_CLIENT_ID . '&state=' . $sState );
      }
    break;

    case 'twitter': // I copied some of this from https://github.com/jublonet/codebird-php and bitched it!

// ... yes sir, yes sir, nickle bag full
// is it me or theres an oauth_token around? ... baa, baa, black sheep, have u any wool?

      if ( !isset( $_SESSION['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) && isset( $_SESSION['oauth_verify'] ) ) // shit, that hurts...
      {
        // verify the token
        $oTwitter->setToken( $_SESSION['oauth_token'], $_SESSION['oauth_token_secret'] ); // if you say so....
        unset( $_SESSION['oauth_verify'] );

        // get the access token
        $oResponse = $oTwitter->oauth_accessToken([
          'oauth_verifier' => $_GET['oauth_verifier']
        ]);

        // store the token (which is different from the request token!) .... naaaaah, so, why are they called the same?
        $_SESSION['oauth_token']        = $oResponse->oauth_token;
        $_SESSION['oauth_token_secret'] = $oResponse->oauth_token_secret;

        // now guys, we can finally follow!
        $aDribbbleFollowees = $_SESSION['dribbble_followees']; // told you i got them all
        foreach( $aDribbbleFollowees as $sTwitterScreenName ) // you rememeber these were screen names right?
        {
          Debug::dumpFormatted( $oTwitterApi->friendships_create( ['screen_name' => $sTwitterScreenName], true ) );
        }

        die( "DONE!" ); // bitched!
      }
    break;
  }

?>