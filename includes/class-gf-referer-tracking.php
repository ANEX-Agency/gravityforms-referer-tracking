<?php

GFForms::include_addon_framework();

require_once __DIR__ . '/fields/class-gf-field-referer-tracking.php';

class Rebits_GF_RefTrack extends GFAddOn {

    protected $_version = '1.2.0';
    protected $_min_gravityforms_version = '2.2';
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

        require_once __DIR__ . '/class-gf-referer-tracking-engine.php';

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

                   'cookie_name' => array(
                        'label'             => esc_html__( 'Cookie Name', 'gravityforms-referer-tracking' ),
                        'tooltip'           => esc_html__( 'Name your Cookie here', 'gravityforms-referer-tracking' ),
                        'name'              => 'cookie_name',
                        'type'              => 'text',
                        'default'           => '__gf_referertrack',
                    ),

                    'cookie_expiry' => array(
                        'label'             => esc_html__( 'Cookie Expiry', 'gravityforms-referer-tracking' ),
                        'tooltip'           => esc_html__( 'Cookie Expiration in seconds (default: 60*60*24*60 = 5184000 = 60 days', 'gravityforms-referer-tracking' ),
                        'name'              => 'cookie_expiry',
                        'type'              => 'text',
                        'default'           => 5184000,
                    ),

                    'cookie_merge_strategy' => array(
                        'label'             => esc_html__( 'Cookie Merge Strategy', 'gravityforms-referer-tracking' ),
                        'tooltip'           => esc_html__( 'How to behave if the cookie is already set?', 'gravityforms-referer-tracking' ),
                        'name'              => 'cookie_merge_strategy',
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
                    ),
					
                    'params' => array(
                        'label'             => esc_html__( 'URL Parameters', 'gravityforms-referer-tracking' ),
                        'tooltip'           => esc_html__( 'White-list one url parameter per line here', 'gravityforms-referer-tracking' ),
                        'name'              => 'params',
						'type'				=> 'textarea',
						'class'				=> 'medium',
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