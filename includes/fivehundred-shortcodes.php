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
        // Extract the attributes
        extract( shortcode_atts( array(
            'search' => ''
        ), $atts ) );

        $photos = $this->get_photos($search);

        $feed = '';
        $feed .= "<h4>500px Connector Feed - {$search}</h4>";

        foreach($photos as $photo) {
            $feed .= "<div style='width: 50%; float: left; margin-bottom: 2rem;'><a href='{$photo['url']}' target='_blank'>{$photo['title']}<br><img src='{$photo['thumbnail']}'></a></div>";
        }

        return $feed;
    }

    function get_photos( $search ) {
        $url = 'https://api.500px.com/v1/photos/search?term=' .$search. '&image_size=3&consumer_key=' .$this->consumer_key;

        $response = wp_remote_get( $url );

        $data = json_decode( $response['body'], true );

        if( $data['photos'] ) {
            $photo_data = array();
            foreach( $data['photos'] as $key => $val ) {
                $photo_data[] = array(
                    'id'            => $val['id'],
                    'url'           => 'http://500px.com'.$val['url'],
                    'title'         => $val['name'],
                    'description'   => $val['description'],
                    'author'        => $val['user']['fullname'],
                    'thumbnail'     => $val['image_url'],
                    'camera'        => $val['camera'],
                    'lens'          => $val['lens'],
                    'focal_length'  => $val['focal_length'],
                    'iso'           => $val['iso'],
                    'shutter_speed' => $val['shutter_speed'],
                    'aperture'      => $val['aperture']
                );
            }

            return $photo_data;
        }
        else {
            return 'No '.$search.' photos found. Please try a new search term.';
        }
    }
}

new FiveHundred_Shortcodes();