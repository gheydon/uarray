<?php

spl_autoload_register(function( $class ) {
  $classFile = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
  $classPI = pathinfo( $classFile );
  $classPath = strtolower( $classPI[ 'dirname' ] );

  if (file_exists( __DIR__ . '/../lib/' . $classPath . DIRECTORY_SEPARATOR . $classPI[ 'filename' ] . '.php')) {
    include_once( __DIR__ . '/../lib/' . $classPath . DIRECTORY_SEPARATOR . $classPI[ 'filename' ] . '.php' );
  }
});
