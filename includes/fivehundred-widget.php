<?php
/**
 * Widget for displaying 500px Connector
 */
class FiveHundred_Widget {
    function __construct() {
        $this->consumer_key = get_option( 'fivehundred_consumer_key' );
    }
}

new FiveHundred_Widget();