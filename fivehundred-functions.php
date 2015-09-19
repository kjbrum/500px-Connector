<?php
/**
 * 500px Connector functions
 */

/**
 *  Build the HTML string for the photo feed
 *
 *  @param   array   $photos  The array of photos from 500px
 *
 *  @return  string           The HTML string for displaying the photo feed
 */
function fivehundred_build_output( $photos, $heading='' ) {
    $layout = get_option( 'fivehundred_default_layout' );
    $layout_custom = get_option( 'fivehundred_default_layout_custom' );
    $layout_custom = stripslashes( $layout_custom );

    $output = '';

    $output .= "<div class='fivehundred--items'>";

        if( $heading ) {
            $output .= "<div class='fivehundred--heading'>".$heading."</div>";
        }

        switch ($layout) {
            case "image-title":
                foreach($photos as $photo) {
                    $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred--item' target='_blank'>";
                        $item .= "<img src='{$photo['image_url']}' class='fivehundred--item__image'>";
                        $item .= "<div class='fivehundred--item__title'>{$photo['name']}</div>";
                    $item .= '</a>';
                    $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                }
                break;
            case "image-title-date":
                foreach($photos as $photo) {
                    $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred--item' target='_blank'>";
                        $item .= "<img src='{$photo['image_url']}' class='fivehundred--item__image'>";
                        $item .= "<div class='fivehundred--item__title'>{$photo['name']}</div>";
                        $item .= "<div class='fivehundred--item__date'>" . date( 'M d, Y', strtotime( $photo['created_at'] ) ) . "</div>";
                    $item .= '</a>';
                    $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                }
                break;
            case "image-title-author":
                foreach($photos as $photo) {
                    $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred--item' target='_blank'>";
                        $item .= "<img src='{$photo['image_url']}' class='fivehundred--item__image'>";
                        $item .= "<div class='fivehundred--item__title'>{$photo['name']}</div>";
                        $item .= "<div class='fivehundred--item__author'>{$photo['user']['fullname']}</div>";
                    $item .= '</a>';
                    $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                }
                break;
            case "image-author-date":
                foreach($photos as $photo) {
                    $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred--item' target='_blank'>";
                        $item .= "<img src='{$photo['image_url']}' class='fivehundred--item__image'>";
                        $item .= "<div class='fivehundred--item__author'>{$photo['user']['fullname']}</div>";
                        $item .= "<div class='fivehundred--item__date'>" . date( 'M d, Y', strtotime( $photo['created_at'] ) ) . "</div>";
                    $item .= '</a>';
                    $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                }
                break;
            case "custom":
                foreach( $photos as $photo ) {
                    $replace = array(
                        '{{name}}'             => $photo['name'],
                        '{{description}}'      => $photo['description'],
                        '{{camera}}'           => $photo['camera'],
                        '{{lens}}'             => $photo['lens'],
                        '{{focal_length}}'     => $photo['focal_length'],
                        '{{iso}}'              => $photo['iso'],
                        '{{shutter_speed}}'    => $photo['shutter_speed'],
                        '{{aperture}}'         => $photo['aperture'],
                        '{{times_viewed}}'     => $photo['times_viewed'],
                        '{{date}}'             => date( 'M d, Y', strtotime( $photo['created_at'] ) ),
                        '{{rating}}'           => $photo['rating'],
                        '{{votes_count}}'      => $photo['votes_count'],
                        '{{favorites_count}}'  => $photo['favorites_count'],
                        '{{comments_count}}'   => $photo['comments_count'],
                        '{{highest_rating}}'   => $photo['highest_rating'],
                        '{{image}}'            => '<img src="'.$photo['image_url'].'" alt="'.$photo['name'].'">',
                        '{{image_url}}'        => $photo['image_url'],
                        '{{url}}'              => 'http://500px.com/'.$photo['url'],
                        '{{tags}}'             => implode( ', ', $photo['tags'] ),
                        '{{author}}'           => $photo['user']['fullname'],
                        '{{author.url}}'       => 'http://500px.com/'.$photo['user']['username'],
                        '{{author.username}}'  => $photo['user']['username'],
                        '{{author.image}}'     => $photo['user']['userpic_url']
                    );

                    if( !empty( $photo['user']['followers_count'] ) ) {
                        $replace['{{author.followers}}'] = $photo['user']['followers_count'];
                    } else {
                        $replace['{{author.followers}}'] = '';
                    }

                    // Add in the needed content
                    $item = strtr( $layout_custom, $replace );

                    // Add the item to $output
                    $output .= $item;
                }
                break;
        }

    $output .= '</div>';

    return $output;
}

/**
 *  Query the desired photos from 500px
 *
 *  @param   array  $parameters  An associative array for the request query
 *
 *  @return  array               The array of photos from 500px
 */
function fivehundred_query_photos( $parameters ) {
    $consumer_key = get_option( 'fivehundred_consumer_key' );

    // Make sure the user has entered their consumer key
    if( !$consumer_key ) {
        return '<p>Error: You need to add your <a href="'.admin_url( 'options-general.php?page=fivehundred-settings' ).'">500px consumer key</a>.</p>';
    }

    $query = http_build_query( $parameters );
    if( !empty( $parameters['term'] ) ) {
        $url = 'https://api.500px.com/v1/photos/search?' .$query. '&consumer_key=' .$consumer_key;
    } else {
        $url = 'https://api.500px.com/v1/photos?' .$query. '&consumer_key=' .$consumer_key;
    }

    $response = wp_remote_get( $url );

    $data = json_decode( $response['body'], true );

    if( !empty( $data['error'] ) ) {
        return '<p>Error: '.$data['error'].'</p>';
    }

    if( !empty( $data['photos'] ) ) {
        return $data['photos'];
    } else {
        return apply_filters( 'fivehundred_shortcode_no_results', 'No photos meet your criteria.' );
    }
}
