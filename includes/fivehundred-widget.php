<?php
/**
 * Widget for displaying 500px Connector
 */
class FiveHundred_Widget extends WP_Widget {
    function __construct() {
        $this->consumer_key = get_option( 'fivehundred_consumer_key' );
        parent::__construct(
			'fivehundred_widget',
			'500px Connector',
			array(
                'description' => 'A widget for displaying 500px photo streams.'
            )
		);
    }
    
    public function widget( $args, $instance ) {
		echo $args['before_widget'];
        
		if( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
        
        echo do_shortcode( "[fivehundred term='startrails' category='Landscapes' count='{$instance['count']}']" );

		echo $args['after_widget'];
	}

    public function form( $instance ) {
        $title = ( !empty( $instance['title'] ) ) ? $instance['title'] : '';
		$count = ( !empty( $instance['count'] ) ) ? $instance['count'] : '10';
		?>
		<p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>">Count:</label>
            <input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" size="4">
		</p>
		<?php
	}

    public function update( $new_instance, $old_instance ) {
		$instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( !empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';

		return $instance;
	}

}

new FiveHundred_Widget();