<?php
/**
 * Shortcodes for displaying 500px Connector
 */
class FiveHundred_Shortcodes {
    function __construct() {
        $this->consumer_key               = get_option( 'fivehundred_consumer_key' );
        $this->default_layout             = get_option( 'fivehundred_default_layout' );
        $this->default_layout_custom      = get_option( 'fivehundred_default_layout_custom' );
        $this->default_layout_custom_css  = get_option( 'fivehundred_default_layout_custom_css' );
        $this->remove_nsfw                = get_option( 'fivehundred_remove_nsfw' );
        $this->default_exclude_categories = get_option( 'fivehundred_default_exclude_categories' );

        /**
         * Add the SVG shortcode
         */
        add_shortcode( 'fivehundred', array( $this, 'fivehundred_display_feed' ) );
    }

    /**
     * Display the correct 500px feed
     *
     * @param   array   $atts     Attributes
     * @param   string  $content  Content between the brackets
     * @return  string  $feed     Feed HTML
     */
    function fivehundred_display_feed( $atts, $content ) {

        if( !empty( $atts['search'] ) ) {
            $atts['term'] = $atts['search'];
            unset( $atts['search'] );
        }

        if( !empty( $atts['count'] ) ) {
            $atts['rpp'] = $atts['count'];
            unset( $atts['count'] );
        }

        if( !empty( $atts['categories'] ) ) {
            $atts['only'] = $atts['categories'];
            unset( $atts['categories'] );
        }

        if( !empty( $atts['exclude_categories'] ) ) {
            $atts['exclude'] = $atts['exclude_categories'];
            unset( $atts['exclude_categories'] );
        }

        // Check if nsfw content is removed by default
        if( get_option( 'fivehundred_remove_nsfw' ) ) {
            if( !empty( $atts['exclude'] ) ) {
                $atts['exclude'] = $atts['exclude'].',Nude';
            } else {
                $atts['exclude'] = 'Nude';
            }
        }

        if( !empty( $atts['username'] ) ) {
            $atts['feature'] = 'user';
        }

        // Extract the attributes
        $atts = shortcode_atts( array(
            'feature'        => 'fresh_today',
            'term'           => '',
            'username'       => '',
            'only'           => '',
            'exclude'        => '',
            'sort'           => '',
            'sort_direction' => 'desc',
            'page'           => '',
            'rpp'            => '10',
            'image_size'     => '3',
            'include_store'  => '0',
            'include_states' => '0',
            'tags'           => '1',
            'heading'        => ''
        ), $atts );

        $photos = fivehundred_query_photos( array_filter( $atts ) );

        // Check for any errors
        if( is_string( $photos ) ) {
            return $photos;
        } else {
            $output = fivehundred_build_output( $photos );
            return $output;
        }
    }
}

new FiveHundred_Shortcodes();