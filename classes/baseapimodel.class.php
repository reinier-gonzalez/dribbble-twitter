<?php

  class BaseApiModel
  {
    static function get( $sApiUrl, $aParams = null )
    {
      curl_setopt( $hRequest, CURLOPT_HTTPGET, 1 );

      $sQueryString = self::constructQueryString( $aParams );

      // Do the request
      $hRequest = self::initRequest( "$sApiUrl?$sQueryString" );
      $sResponse =  curl_exec( $hRequest );

      return $sResponse;
    }

    static function post( $sApiUrl, $aParams = null )
    {

      $sQueryString = self::constructQueryString( $aParams );

      // Do the request
      $hRequest = self::initRequest( $sApiUrl );
      curl_setopt( $hRequest, CURLOPT_POST, 1 );
      curl_setopt( $hRequest, CURLOPT_POSTFIELDS, $sQueryString );
      $sResponse =  curl_exec( $hRequest );

      return $sResponse;
    }

    static function delete( $sApiUrl, $aParams = null )
    {
      // not needed at this point...
    }

    static function put( $sApiUrl, $aParams = null )
    {
      // not needed at this point...
    }

    static function patch( $sApiUrl, $aParams = null )
    {
      // not needed at this point...
    }

    static function head( $sApiUrl, $aParams = null )
    {
      // not needed at this point...
    }

    static function options( $sApiUrl, $aParams = null )
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

    static function initRequest( $sUrl )
    {
      $hRequest = curl_init();

      curl_setopt( $hRequest, CURLOPT_HEADER,          0 );
      curl_setopt( $hRequest, CURLOPT_FOLLOWLOCATION,  1 );
      curl_setopt( $hRequest, CURLOPT_RETURNTRANSFER,  1 );
      curl_setopt( $hRequest, CURLOPT_URL, $sUrl );

      return $hRequest;
    }

  }