<?php
/**
 * Shortcodes for displaying 500px Connector
 */
class FiveHundred_Shortcodes {
    function __construct() {
        $this->consumer_key = get_option( 'fivehundred_consumer_key' );

        /**
         * Add the SVG shortcode
         */
        add_shortcode( 'fivehundred', array( $this, 'display_feed' ) );
    }

    /**
     * Display the correct 500px feed
     *
     * @param   array   $atts     Attributes
     * @param   string  $content  Content between the brackets
     * @return  string  $feed     Feed HTML
     */
    function display_feed( $atts, $content ) {

        if($atts['search']) {
            $atts['term'] = $atts['search'];
            unset($atts['search']);
        }

        if($atts['count']) {
            $atts['rpp'] = $atts['count'];
            unset($atts['count']);
        }

        if($atts['categories']) {
            $atts['only'] = $atts['categories'];
            unset($atts['categories']);
        }

        if($atts['exclude_categories']) {
            $atts['exclude'] = $atts['exclude_categories'];
            unset($atts['exclude_categories']);
        }

        if($atts['username']) {
            $atts['feature'] = 'user';
        }

        // Check for global options

        // Extract the attributes
        $atts = shortcode_atts( array(
            'feature'        => 'fresh_today',
            'term'           => '',
            'username'       => '',
            'only'           => '',
            'exclude'        => '',
            'sort'           => 'created_at',
            'sort_direction' => 'desc',
            'page'           => '1',
            'rpp'            => '10',
            'image_size'     => '3',
            'include_store'  => '0',
            'include_states' => '0',
            'tags'           => '1'
        ), $atts );

        $photos = $this->get_photos( array_filter( $atts ) );

        $feed = '';
        $feed .= "<h4>500px Connector Feed</h4>";

        foreach($photos as $photo) {
            $feed .= "<div style='width: 50%; float: left; margin-bottom: 2rem;'><a href='http://500px.com/{$photo['url']}' target='_blank'>{$photo['name']}<br><img src='{$photo['image_url']}'></a></div>";
        }

        return $feed;
    }

    function get_photos( $data ) {

        $query = http_build_query( $data );
        if( $data['term'] ) {
            $url = 'https://api.500px.com/v1/photos/search?' .$query. '&consumer_key=' .$this->consumer_key;
        } else {
            $url = 'https://api.500px.com/v1/photos?' .$query. '&consumer_key=' .$this->consumer_key;
        }

        $response = wp_remote_get( $url );

        $data = json_decode( $response['body'], true );

        if( $data['photos'] ) {
            return $data['photos'];
        } else {
            return 'No photos meet your criteria.';
        }
    }
}

new FiveHundred_Shortcodes();