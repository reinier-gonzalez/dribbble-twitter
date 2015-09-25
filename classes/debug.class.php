<?php

  class Debug
  {
    static function dumpFormatted( $var, $bHTML = true )
    {
      if ( $bHTML )
      {
        echo "<pre>";
        print_r( $var );
        echo "</pre>";
      }
      else
      {
        print_r( $var );
      }
    }

    static function getFormatted( $var, $bHTML = true )
    {
      ob_start();

      self::dumpFormatted( $var, $bHTML );

      return ob_get_clean();
    }
  }

?>