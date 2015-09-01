<?php
/**
 * 500px Connector functions
 */

function fivehundred_build_output( $photos ) {
    $layout = get_option( 'fivehundred_default_layout' );
    $output = '';
    switch ($layout) {
        case "image-title":
            $output .= "<div class='fivehundred-container'>";
                $output .= "<div class='fivehundred-items'>";
                    foreach($photos as $photo) {
                        $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred-item' target='_blank'>";
                            $item .= "<img src='{$photo['image_url']}' class='fivehundred-image'>";
                            $item .= "<div class='fivehundred-title'>{$photo['name']}</div>";
                        $item .= '</a>';
                        $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                    }
                $output .= '</div>';
            $output .= '</div>';
            break;
        case "image-title-date":
            $output .= "<div class='fivehundred-container'>";
                $output .= "<div class='fivehundred-items'>";
                    foreach($photos as $photo) {
                        $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred-item' target='_blank'>";
                            $item .= "<img src='{$photo['image_url']}' class='fivehundred-image'>";
                            $item .= "<div class='fivehundred-title'>{$photo['name']}</div>";
                            $item .= "<div class='fivehundred-date'>" . date( 'M d, Y', strtotime( $photo['created_at'] ) ) . "</div>";
                        $item .= '</a>';
                        $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                    }
                $output .= '</div>';
            $output .= '</div>';
            break;
        case "image-title-author":
            $output .= "<div class='fivehundred-container'>";
                $output .= "<div class='fivehundred-items'>";
                    foreach($photos as $photo) {
                        $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred-item' target='_blank'>";
                            $item .= "<img src='{$photo['image_url']}' class='fivehundred-image'>";
                            $item .= "<div class='fivehundred-title'>{$photo['name']}</div>";
                            $item .= "<div class='fivehundred-author'>{$photo['user']['fullname']}</div>";
                        $item .= '</a>';
                        $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                    }
                $output .= '</div>';
            $output .= '</div>';
            break;
        case "image-author-date":
            $output .= "<div class='fivehundred-container'>";
                $output .= "<div class='fivehundred-items'>";
                    foreach($photos as $photo) {
                        $item = "<a href='http://500px.com/{$photo['url']}' class='fivehundred-item' target='_blank'>";
                            $item .= "<img src='{$photo['image_url']}' class='fivehundred-image'>";
                            $item .= "<div class='fivehundred-author'>{$photo['user']['fullname']}</div>";
                            $item .= "<div class='fivehundred-date'>" . date( 'M d, Y', strtotime( $photo['created_at'] ) ) . "</div>";
                        $item .= '</a>';
                        $output .= apply_filters( 'fivehundred_shortcode_item_contents', $item, $photo );
                    }
                $output .= '</div>';
            $output .= '</div>';
            break;
        case "custom-layout":
            $output .= "custom layout here";
            break;
    }

    return $output;
}

function fivehundred_query_photos( $parameters ) {
    $consumer_key = get_option( 'fivehundred_consumer_key' );

    $query = http_build_query( $parameters );
    if( !empty( $parameters['term'] ) ) {
        $url = 'https://api.500px.com/v1/photos/search?' .$query. '&consumer_key=' .$consumer_key;
    } else {
        $url = 'https://api.500px.com/v1/photos?' .$query. '&consumer_key=' .$consumer_key;
    }

    $response = wp_remote_get( $url );

    $data = json_decode( $response['body'], true );

    if( !empty( $data['photos'] ) ) {
        return $data['photos'];
    } else {
        return apply_filters( 'fivehundred_shortcode_no_results', 'No photos meet your criteria.' );
    }
}
