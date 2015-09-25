<?php

  class BaseApiModel
  {
    static function get( $sApiUrl, $aParams = null, $aHeaders = [] )
    {
      $sQueryString = self::constructQueryString( $aParams );

      // Do the request
      $hRequest = self::initRequest( "$sApiUrl?$sQueryString", $aHeaders );
      curl_setopt( $hRequest, CURLOPT_HTTPGET, 1 );

      return curl_exec( $hRequest );;
    }

    static function post( $sApiUrl, $aParams = null, $aHeaders = [] )
    {

      $sQueryString = self::constructQueryString( $aParams );

      // Do the request
      $hRequest = self::initRequest( $sApiUrl, $aHeaders );
      curl_setopt( $hRequest, CURLOPT_POST, 1 );
      curl_setopt( $hRequest, CURLOPT_POSTFIELDS, $sQueryString );

      return curl_exec( $hRequest );
    }

    static function delete( $sApiUrl, $aParams = null, $aHeaders = [] )
    {
      // not needed at this point...
    }

    static function put( $sApiUrl, $aParams = null, $aHeaders = [] )
    {
      // not needed at this point...
    }

    static function patch( $sApiUrl, $aParams = null, $aHeaders = [] )
    {
      // not needed at this point...
    }

    static function head( $sApiUrl, $aParams = null, $aHeaders = [] )
    {
      // not needed at this point...
    }

    static function options( $sApiUrl, $aParams = null, $aHeaders = [] )
    {
      // not needed at this point...
    }


    // helpers
    static function constructQueryString( $aParams )
    {
      // construct query string
      $aQuery = [];
      if ( $aParams )
      {
        foreach( $aParams as $sKey => $sValue )
        {
          $sValue = urlencode( $sValue );
          $aQuery[] = "$sKey=$sValue";
        }
      }
      $sQueryString = implode( '&', $aQuery );

      return $sQueryString;
    }

    static function initRequest( $sUrl, $aHeaders = [] )
    {
      $hRequest = curl_init();

      curl_setopt( $hRequest, CURLOPT_HEADER,          0 );
      curl_setopt( $hRequest, CURLOPT_FOLLOWLOCATION,  1 );
      curl_setopt( $hRequest, CURLOPT_RETURNTRANSFER,  1 );
      curl_setopt( $hRequest, CURLOPT_SSL_VERIFYHOST,  0 ); // TODO: are you sure? :D
      curl_setopt( $hRequest, CURLOPT_SSL_VERIFYPEER,  0 ); // TODO: are you sure? :D
      curl_setopt( $hRequest, CURLOPT_URL, $sUrl );
      if ( $aHeaders )
      {
        curl_setopt( $hRequest, CURLOPT_HTTPHEADER, $aHeaders );
      }


      return $hRequest;
    }

  }