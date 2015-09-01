<?php
/*
Plugin Name: 500px Connector
Version: 1.0
Description: Allow users to display 500px photo streams.
Author: Kyle Brumm
Author URI: http://kylebrumm.com
Plugin URI: http://kylebrumm.com/fivehundred
Text Domain: fivehundred
Domain Path: /languages
*/

if ( ! class_exists( 'FiveHundred' ) ) :

class FiveHundred {
    var $settings;

    /**
     *  Construct our class
     */
    public function __construct() {
        $this->settings = array(
            'url'  => plugin_dir_url( __FILE__ ),
            'path' => plugin_dir_path( __FILE__ )
        );

        // Set the default layout if one hasn't been choosen
        if( !get_option( 'fivehundred_default_layout' ) ) {
            update_option( 'fivehundred_default_layout', 'image-title' );
        }

        $this->consumer_key = get_option( 'fivehundred_consumer_key' );
        $this->default_layout = get_option( 'fivehundred_default_layout' );
        $this->default_layout_custom = get_option( 'fivehundred_default_layout_custom' );
        $this->default_layout_custom_css = get_option( 'fivehundred_default_layout_custom_css' );
        $this->remove_nsfw = get_option( 'fivehundred_remove_nsfw' );
        $this->default_exclude_categories = get_option( 'fivehundred_default_exclude_categories' );


        // Require the the goods
        require_once( 'includes/fivehundred-shortcodes.php' );
        require_once( 'includes/fivehundred-widget.php' );
        require_once( 'fivehundred-functions.php' );

        // Require our admin files
        if ( is_admin() ) {
            // Require the admin functionality
            require_once( 'admin/fivehundred-admin.php' );
        }

        // Create our plugin page
        add_action( 'widgets_init', array( $this, 'register_feed_widget' ) );

        // Add default custom layout CSS
        if( !empty( $this->default_layout_custom_css ) ) {
            add_action( 'wp_head', array( $this, 'add_custom_css' ) );
        }
    }

    public function register_feed_widget() {
        register_widget( 'FiveHundred_Widget' );
    }

    public function add_custom_css() {
        $output = "<style>$this->default_layout_custom_css</style>";

        echo $output;
    }
}

function fivehundred() {
    global $fivehundred;

    if ( ! isset( $fivehundred ) ) {
        $fivehundred = new FiveHundred();
    }

    return $fivehundred;
}

// Initialize
fivehundred();

endif;

