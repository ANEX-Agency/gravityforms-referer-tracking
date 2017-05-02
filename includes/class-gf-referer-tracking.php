<?php

GFForms::include_addon_framework();

/**
 * Gravity Forms Referer Tracking Add-On.
 *
 * @since     1.0.0
 * @package   GravityForms
 * @author    ANEX
 * @copyright Copyright (c) 2017, ANEX
 */
class GF_Referer_Tracking extends GFAddOn {
	
	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Referer Tracking Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_version Contains the version, defined from gravityforms-referer-tracking.php
	 */
    protected $_version = GF_REFERER_TRACKING_VERSION;
	
	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
    protected $_min_gravityforms_version = '2.2';
	
	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
    protected $_slug = 'gravityforms-referer-tracking';
	
	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
    protected $_path = 'gravityforms-referer-tracking/gravityforms-referer-tracking.php';
	
	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
    protected $_full_path = __FILE__;
	
	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'http://www.anex.at';
	
	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
    protected $_title = 'Gravity Forms Referer Tracking';
	
	/**
	 * Defines the short title of the Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
    protected $_short_title = 'Referer Tracking';

	/**
	 * Contains an instance of the Referer Tracking Engine, if available.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object $_engine If available, contains an instance of the Referer Tracking Engine.
	 */
    protected $_engine;

	/**
	 * Get an instance of this class.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return GF_Referer_Tracking
	 */
	public static function get_instance() {

		if ( null === self::$_instance )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	/**
	 * Autoload the required libraries.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses GFAddOn::is_gravityforms_supported()
	 */
	public function pre_init() {

		parent::pre_init();

		if ( $this->is_gravityforms_supported() ) {

			// Load the Referer Tracking Engine Class.
			if ( ! class_exists( 'GF_Referer_Tracking_Engine' ) )
				require_once 'class-gf-referer-tracking-engine.php';
				
			require_once 'fields/class-gf-field-referer-tracking.php';
			
			
		}
	}
	
	/**
	 * Plugin starting point. Handles hooks and loading of language files.
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function init() {

        parent::init();

        $this->_engine = new GF_Referer_Tracking_Engine( $this );
		
		require_once 'template-functions.php';
		
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function get_engine() {
		
        return $this->_engine;
		
    }
	
	// # PLUGIN SETTINGS -----------------------------------------------------------------------------------------------

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
    public function plugin_settings_fields() {
		
        return array(
            array(
                'fields' => array(

                   'cookie_name' => array(
                        'label'             => esc_html__( 'Cookie Name', 'gravityforms-referer-tracking' ),
						'tooltip'			=> sprintf( '<h6>%s</h6>%s', esc_html__( 'Cookie Name', 'gravityforms-referer-tracking' ), esc_html__( 'Name your Cookie here.', 'gravityforms-referer-tracking' ) ),
                        'name'              => 'cookie_name',
                        'type'              => 'text',
                        'default'           => '_gform_referer_tracking',
                    ),

                    'cookie_expiry' => array(
                        'label'             => esc_html__( 'Cookie Expiry', 'gravityforms-referer-tracking' ),
						'tooltip'			=> sprintf( '<h6>%s</h6>%s', esc_html__( 'Cookie Expiry', 'gravityforms-referer-tracking' ), esc_html__( 'Cookie Expiration in seconds (default: 60*60*24*60 = 5184000 = 60 days', 'gravityforms-referer-tracking' ) ),
                        'name'              => 'cookie_expiry',
                        'type'              => 'text',
                        'default'           => 5184000,
                    ),

                    'cookie_mode' => array(
                        'label'             => esc_html__( 'Cookie Mode', 'gravityforms-referer-tracking' ),
						'tooltip'			=> sprintf( '<h6>%s</h6>%s', esc_html__( 'Cookie Mode', 'gravityforms-referer-tracking' ), esc_html__( 'How to behave if the cookie is already set? Keep Cookie as is? Replace with current request parameters? Or Merge with current request parameters?', 'gravityforms-referer-tracking' ) ),
                        'name'              => 'cookie_mode',
                        'type'              => 'radio',
                        'default'           => 'keep',
                        'choices'           => array(
                            array(
                                'value' => 'keep',
                                'label' => esc_html__( 'Keep', 'gravityforms-referer-tracking' )
                            ),
                            array(
                                'value' => 'replace',
                                'label' => esc_html__( 'Replace', 'gravityforms-referer-tracking' )
                            ),
                            array(
                                'value' => 'merge',
                                'label' => esc_html__( 'Merge', 'gravityforms-referer-tracking' )
                            )
                        )
                    ),
					
                    'cookie_params' => array(
                        'label'             => esc_html__( 'URL Parameters', 'gravityforms-referer-tracking' ),
						'tooltip'			=> sprintf( '<h6>%s</h6>%s', esc_html__( 'URL Parameters', 'gravityforms-referer-tracking' ), esc_html__( 'White-list one url parameter per line here.', 'gravityforms-referer-tracking' ) ),
                        'name'              => 'params',
						'type'				=> 'textarea',
						'class'				=> 'medium',
                    )
					
                )
				
            )
			
        );
		
    }
	
	/**
	 * -
	 *
	 * @since  1.0.0
	 * @access public
	 */
    public function get_option( $key ) {

        $settings = $this->get_plugin_settings();

        if( isset( $settings[$key] ) && ! empty( $settings[$key] ) )
            return $settings[$key];

        $fields = $this->plugin_settings_fields();
        $fields = $fields[0]['fields'];

        if( isset( $fields[$key]['default'] ) )
            return $fields[$key]['default'];
			
    }
	
}