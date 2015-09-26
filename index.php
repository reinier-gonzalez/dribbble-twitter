<?php
  require 'bootstrap.php';

  // TODO: create a dribble API wrapper class that works...

  $sStartService = 'dribbble'; // okeeeyy.....
  $sService = isset( $_GET['service'] ) ? $_GET['service'] : $sStartService; // makes sense

  switch ( $sService ) // so, where are you coming from? .... sorry, what about HTTP_REFERRER? ... fuck off!
  {
    case 'dribbble':
      // get followees from dribbble
      if ( isset( $_GET['code'] ) ) // got an access code? else GOTO: 83
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
            $iPage = 1;
            $aDribbbleFollowees = [];
            do
            {
              $sResponse = BaseApiModel::get( "https://api.dribbble.com/v1/user/following",
                ['page' => $iPage],
                ["Authorization: Bearer $sAccessToken"]
              );
              $aFollowees = json_decode( $sResponse ); // TODO: try aaaaaaaaaaaaand... catch!

              if ( $aFollowees ) // json-formatted with followees data?
              {
                // uuuuuiiiuoww...
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

                // oh shit! what I'm I doing here? GOTO: 91

                $iPage++;
              } // look... no followee no biz.

            } while( $aFollowees );

            if ( !$aDribbbleFollowees )
            {
              die( "So sad, no Dribbble followees..." );
            }

          } // so sad, no access token :(
          else die( "Oooops, didn't get the access token" );

        } else die( "Damn Hackers!" ); // gotcha!

      } // TA-TA-TAAAAAAAAAN:
      else // 1st movement: OAUTH dance
      {
        // let's get authorized on dribbble ...
        $sState = $_SESSION['dribbble_state'] = uniqid( "", true );
        header( 'Location: https://dribbble.com/oauth/authorize?client_id=' . DRIBBBLE_CLIENT_ID . '&state=' . $sState );
      }
    break;

    case 'twitter': // I copied some of this from https://github.com/jublonet/codebird-php and pimped it!
      if ( isset( $_GET['oauth_verifier'] ) && isset( $_SESSION['oauth_verify'] ) ) // shit, that hurts...
      {
        // verify the token
        $oTwitter->setToken( $_SESSION['oauth_token'], $_SESSION['oauth_token_secret'] ); // if you say so....
        unset( $_SESSION['oauth_verify'] );

        // get the access token
        $oResponse = $oTwitter->oauth_accessToken([
          'oauth_verifier' => $_GET['oauth_verifier']
        ]);

        // set the access tokens!
        $oTwitter->setToken( $oResponse->oauth_token, $oResponse->oauth_token_secret );

        // now guys, we can finally follow!
        $aDribbbleFollowees = $_SESSION['dribbble_followees']; // told you i got them all
        foreach( $aDribbbleFollowees as $sTwitterScreenName ) // you rememeber these screen names right?
        {
          echo "Following $sTwitterScreenName ..."; // oh, yes, oooh yes!

          $oResponse = $oTwitter->friendships_create( [ // bam!
            'screen_name' => $sTwitterScreenName,
            'follow' => true
            ]
          );

          if ( !isset( $oResponse->errors ) ) // no errors?
          {
            echo "done! <br/>";
          }
          else // :( got errors, show them
          {
            echo "ooops, got the following errors:<br/>";
            foreach( $oResponse->errors as $oError )
            {
              echo "[$oError->code] $oError->message <br/>";
            }
          }

          echo "<br/>";
        }

        die( "DONE!" ); // pimped!
      }
    break;
  }

?>