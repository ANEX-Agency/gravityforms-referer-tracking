<?php

class GF_Referer_Tracking_Engine {
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    protected $_plugin;

	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    protected $_hash_algos = array(
        'sha1'   => 40,
        'sha256' => 64,
    );
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    protected $_algo = 'sha1';
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function __construct( $plugin ) {
        $this->_plugin = $plugin;
        $this->init();
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function init() {
        $this->update_cookie();
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function get_cookie_name() {
        return apply_filters( 'gform_referer_tracking_cookie_name', $this->_plugin->get_option( 'cookie_name' ) );
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function get_valid_url_keys() {
		
        $keyList = $this->_plugin->get_option( 'params' );

        $keys = explode( "\n", $keyList );
        $keys = array_map( 'trim', $keys );

        return apply_filters( 'gform_referer_tracking_allowed_keys', $keys );
		
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function get_url_data() {

        $data = $_GET;

        $valid = $this->get_valid_url_keys();

        $data = array_intersect_key( $data, array_flip( $valid ) );

        $data = array_filter( $data, 'is_string' );

        return apply_filters( 'gform_referer_tracking_url_data', $data );
		
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function get_cookie_data() {

        $name = $this->get_cookie_name();

        if( ! isset( $_COOKIE[$name] ) )
            return false;

        $data = $_COOKIE[$name];

        $hashLength = $this->_hash_algos[$this->_algo];

        $cookieData = stripslashes( substr( $data, $hashLength ) );
        if( ! $cookieData )
            return false;

        $cookieHash = substr( $data, 0, $hashLength );

        if( $cookieHash != $this->_hash( $cookieData ) )
            return false;

        $data = @json_decode( $cookieData, true, 3 );

        return $data ? $data : array();
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    protected function _hash( $string ) {
        return hash_hmac( $this->_algo, $string, NONCE_KEY );
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    protected function _make_unique( array $array ) {
		
        foreach( $array as $k => &$v ) {

            if( is_array( $v ) ) {
                $v = array_unique( $v );

                if( count( $v ) == 1 ) {
                    $v = $v[0];
                }
            }

        }

        return $array;
		
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function update_cookie() {

        $strategy = $this->_plugin->get_option( 'cookie_mode' );

        $cookieData = $this->get_cookie_data();

        // If the cookie is valid and we should keep it, abort here.
        if( $cookieData !== false && $strategy == 'keep' )
            return;

        $data = $this->get_url_data();

        if( $cookieData !== false && $strategy == 'merge' )
            $data = $this->_make_unique( array_merge_recursive( $cookieData, $data ) );

        $data['timestamp'] = time();

        $data = apply_filters( 'gform_referer_tracking_set_cookie_data', $data );

        $expiry = time() + ( int )$this->_plugin->get_option( 'cookie_expiry' );
        $expiry = apply_filters( 'gform_referer_tracking_cookie_expiry', $expiry );

        $data = json_encode( $data, 0, 3 );

        $hmac = $this->_hash( $data );

        setcookie( $this->get_cookie_name(), $hmac . $data, $expiry, COOKIEPATH, COOKIE_DOMAIN, false, true );
		
    }
	
}
