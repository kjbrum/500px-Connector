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
            'sort_direction' => '',
            'page'           => '',
            'rpp'            => '10',
            'image_size'     => '3',
            'include_store'  => '0',
            'include_states' => '0',
            'tags'           => '1',
            'heading'        => ''
        ), $atts );

        $photos = $this->get_photos( array_filter( $atts ) );

        $feed = "<div class='fivehundred-container'>
                    <ul class='fivehundred-items'>";
                        foreach($photos as $photo) {
                            $feed .= "<li class='fivehundred-item'>";
                                        $item_info = "<a href='http://500px.com/{$photo['url']}' target='_blank'>{$photo['name']}<br><img src='{$photo['image_url']}'></a>";
                                        $feed .= apply_filters( 'fivehundred_shortcode_item_contents', $item_info, $photo );
                                    $feed .= '</li>';
                        }
            $feed .= '</ul>';
        $feed .= '</div>';

        return apply_filters( 'fivehundred_shortcode_contents', $feed, $photos );
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
            return apply_filters( 'fivehundred_shortcode_no_results', 'No photos meet your criteria.' );
        }
    }
}

new FiveHundred_Shortcodes();