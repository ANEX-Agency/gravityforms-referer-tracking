<?php

GFForms::include_addon_framework();

require_once __DIR__ . '/includes/fields/RefTrack.php';

class Rebits_GF_RefTrack extends GFAddOn {

    protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.1';
    protected $_slug = 'gravityforms-referer-tracking';
    protected $_path = 'gravityforms-referer-tracking/gravityforms-referer-tracking.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Referer Tracking';
    protected $_short_title = 'Referer Tracking';

    protected static $_instance;

    protected $_engine;

    public static function get_instance() {
		
        if( !self::$_instance )
            self::$_instance = new self();

        return self::$_instance;
		
    }

    public function init() {

        parent::init();

        require_once __DIR__ . '/includes/engine.php';

        $this->_engine = new Rebits_GF_RefTrack_Engine($this);
    }

    public function getEngine() {
		
        return $this->_engine;
		
    }

    public function plugin_settings_fields() {
		
        return array(
            array(
                'title'  => esc_html__( 'Referer Tracking Settings', 'gravityforms-referer-tracking' ),
                'fields' => array(
                    'params' => array(
                        'name'              => 'params',
                        'tooltip'           => esc_html__( 'White-list one url parameter per line here', 'gravityforms-referer-tracking' ),
                        'label'             => esc_html__( 'URL Parameters', 'gravityforms-referer-tracking' ),
                        'type'              => 'textarea',
                    ),

                   'cookie_name' => array(
                        'name'              => 'cookie_name',
                        'tooltip'           => esc_html__( 'Cookie Name', 'gravityforms-referer-tracking' ),
                        'label'             => esc_html__( 'Cookie Name', 'gravityforms-referer-tracking' ),
                        'type'              => 'text',
                        'default'           => '__gf_reftrack',
                    ),

                    'cookie_expiry' => array(
                        'name'              => 'cookie_expiry',
                        'tooltip'           => esc_html__( 'Cookie Expiration in seconds (default: 60*60*24*60 = 5184000 = 60 days', 'gravityforms-referer-tracking' ),
                        'label'             => esc_html__( 'Cookie Expiry', 'gravityforms-referer-tracking' ),
                        'type'              => 'text',
                        'default'           => 5184000,
                    ),

                    'cookie_merge_strategy' => array(
                        'name'              => 'cookie_merge_strategy',
                        'tooltip'           => esc_html__( 'How to behave if the cookie is already set?', 'gravityforms-referer-tracking' ),
                        'label'             => esc_html__( 'Cookie Merge Strategy', 'gravityforms-referer-tracking' ),
                        'type'              => 'select',
                        'default'           => 'keep',
                        'choices'           => array(
                            array(
                                'value' => 'keep',
                                'label' => __( 'Keep Cookie as is', 'gravityforms-referer-tracking' )
                            ),
                            array(
                                'value' => 'replace',
                                'label' => __( 'Replace with current request parameters', 'gravityforms-referer-tracking' )
                            ),
                            array(
                                'value' => 'merge',
                                'label' => __( 'Merge with current request parameters', 'gravityforms-referer-tracking' )
                            )
                        )
                    )
                    
                )
            )
        );
		
    }

    public function getOption( $key ) {

        $settings = $this->get_plugin_settings();

        if( isset( $settings[$key] ) && !empty( $settings[$key] ) )
            return $settings[$key];

        $fields = $this->plugin_settings_fields();
        $fields = $fields[0]['fields'];

        if( isset( $fields[$key]['default'] ) )
            return $fields[$key]['default'];
			
    }
	
}