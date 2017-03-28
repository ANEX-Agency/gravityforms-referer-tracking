<?php

class Rebits_GF_RefTrack_Engine {

    protected $_plugin;

    protected $_hashAlgos = array(
        'sha1'   => 40,
        'sha256' => 64,
    );

    protected $_algo = 'sha1';

    public function __construct($plugin) {

        $this->_plugin = $plugin;

        //add_action('init', array($this, 'init'));

        $this->init();
    }

    public function init() {
        $this->updateCookie();

        //var_dump($this->getCookieData());
    }

    public function getCookieName() {
        return apply_filters('gf_reftrack_cookie_name', $this->_plugin->getOption('cookie_name'));
    }

    public function getValidUrlKeys() {
        $keyList = $this->_plugin->getOption('params');

        $keys = explode("\n", $keyList);
        $keys = array_map('trim', $keys);

        return apply_filters('gf_reftrack_allowed_keys', $keys);
    }

    public function getUrlData() {

        $data = $_GET;

        $valid = $this->getValidUrlKeys();

        $data = array_intersect_key($data, array_flip($valid));

        $data = array_filter($data, 'is_string');

        return apply_filters('gf_reftrack_url_data', $data);
    }

    public function getCookieData() {

        $name = $this->getCookieName();

        if(!isset($_COOKIE[$name]))
            return false;

        $data = $_COOKIE[$name];

        $hashLength = $this->_hashAlgos[$this->_algo];

        $cookieData = stripslashes(substr($data, $hashLength));
        if(!$cookieData)
            return false;

        $cookieHash = substr($data, 0, $hashLength);

        if($cookieHash != $this->_hash($cookieData))
            return false;

        $data = @json_decode($cookieData, true, 3);

        return $data ? $data : array();
    }

    protected function _hash($str) {
        return hash_hmac($this->_algo, $str, NONCE_KEY);
    }

    public function updateCookie() {

        $strategy = $this->_plugin->getOption('cookie_merge_strategy');

        $cookieData = $this->getCookieData();

        // If the cookie is valid and we should keep it, abort here.
        if($cookieData !== false && $strategy == 'keep')
            return;

        $data = $this->getUrlData();

        if($cookieData !== false && $strategy == 'merge')
            $data = array_merge_recursive($cookieData, $data);

        $data['timestamp'] = time();

        $data = apply_filters('gf_reftrack_set_cookie_data', $data);

        $expiry = time() + (int)$this->_plugin->getOption('cookie_expiry');
        $expiry = apply_filters('gf_reftrack_expiry', $expiry);

        $data = json_encode($data, 0, 3);

        $hmac = $this->_hash($data);

        setcookie($this->getCookieName(), $hmac . $data, $expiry, COOKIEPATH, COOKIE_DOMAIN, false, true);
    }
}
