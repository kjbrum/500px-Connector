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

        $shortcode = "[fivehundred";
        foreach( $instance as $key => $val ) {
            if( $key != 'title' && !empty( $val ) ) {
                if( $key == 'only' || $key == 'exclude' ) {
                    $shortcode .= " $key='";
                    foreach( $val as $key => $category ) {
                        if( $key == count( $val ) - 1 ) {
                            $shortcode .= "$category";
                        } else {
                            $shortcode .= "$category,";
                        }
                    }
                    $shortcode .= "'";
                } else {
                    $shortcode .= " $key='$val'";
                }
            }
        }
        $shortcode .= "]";

        echo do_shortcode( $shortcode );

		echo $args['after_widget'];
	}

    public function form( $instance ) {
        $feature_options = array(
            'fresh_today'     => 'Fresh Today (default)',
            'fresh_yesterday' => 'Fresh Yesterday',
            'fresh_week'      => 'Fresh Week',
            'popular'         => 'Popular',
            'highest_rated'   => 'Highest Rated',
            'upcoming'        => 'Upcoming',
            'editors'         => 'Editors'
        );

        $sort_options = array(
            'created_at'      => 'Created At',
            'rating'          => 'Rating',
            'highest_rating'  => 'Highest Rating',
            'times_viewed'    => 'Times Viewed',
            'votes_count'     => 'Votes Count',
            'favorites_count' => 'Favorites Count',
            'comments_count'  => 'Comments Count',
            'taken_at'        => 'Taken At'
        );

        $categories = array(
            'Uncategorized',
            'Abstract',
            'Animals',
            'Black and White',
            'Celebrities',
            'City and Architecture',
            'Commercial',
            'Concert',
            'Family',
            'Fashion',
            'Film',
            'Fine Art',
            'Food',
            'Journalism',
            'Landscapes',
            'Macro',
            'Nature',
            'Nude',
            'People',
            'Performing Arts',
            'Sport',
            'Still Life',
            'Street',
            'Transportation',
            'Travel',
            'Underwater',
            'Urban Exploration',
            'Wedding'
        );

        $title = ( !empty( $instance['title'] ) ) ? $instance['title'] : '';
        $feature = ( !empty( $instance['feature'] ) ) ? $instance['feature'] : 'fresh_today';
        $only = ( !empty( $instance['only'] ) ) ? $instance['only'] : '';
        $exclude = ( !empty( $instance['exclude'] ) ) ? $instance['exclude'] : '';
        $search = ( !empty( $instance['search'] ) ) ? $instance['search'] : '';
        $username = ( !empty( $instance['username'] ) ) ? $instance['username'] : '';
        $sort = ( !empty( $instance['sort'] ) ) ? $instance['sort'] : '';
        $sort_direction = ( !empty( $instance['sort_direction'] ) ) ? $instance['sort_direction'] : 'desc';
		$count = ( !empty( $instance['count'] ) ) ? $instance['count'] : '10';
		?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'feature' ); ?>">Feature:</label>
            <select id="<?php echo $this->get_field_name( 'feature' ); ?>" name="<?php echo $this->get_field_name( 'feature' ); ?>">
                <?php foreach( $feature_options as $key => $val ) : ?>
                    <option value="<?php echo $key; ?>" <?php echo (esc_attr($feature) == $key)?'selected="selected"':''; ?>><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'only' ); ?>">Only include photos in:</label>
            <select multiple id="<?php echo $this->get_field_name( 'only' ); ?>" name="<?php echo $this->get_field_name( 'only' ); ?>[]">
                <?php foreach( $categories as $category ) : ?>
                    <option value="<?php echo $category; ?>" <?php echo (!empty($only) && in_array($category, $only))?'selected="selected"':''; ?>><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'exclude' ); ?>">Exclude photos in:</label>
            <select multiple id="<?php echo $this->get_field_name( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>[]">
                <?php foreach( $categories as $category ) : ?>
                    <option value="<?php echo $category; ?>" <?php echo (!empty($exclude) && in_array($category, $exclude))?'selected="selected"':''; ?>><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'search' ); ?>">Search:</label>
            <input id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" type="text" value="<?php echo esc_attr( $search ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'username' ); ?>">Username:</label>
            <input id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'sort' ); ?>">Sort by:</label>
            <select id="<?php echo $this->get_field_name( 'sort' ); ?>" name="<?php echo $this->get_field_name( 'sort' ); ?>">
                <option value="">Default</option>
                <?php foreach( $sort_options as $key => $val ) : ?>
                    <option value="<?php echo $key; ?>" <?php echo (esc_attr($sort) == $key)?'selected="selected"':''; ?>><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'sort_direction' ); ?>">Sort Direction:</label>
            <select id="<?php echo $this->get_field_name( 'sort_direction' ); ?>" name="<?php echo $this->get_field_name( 'sort_direction' ); ?>">
                <option value="desc" <?php echo (esc_attr($sort_direction) == 'desc')?'selected="selected"':''; ?>>Descending (default)</option>
                <option value="asc" <?php echo (esc_attr($sort_direction) == 'asc')?'selected="selected"':''; ?>>Ascending</option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>">Count:</label>
            <input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" size="4">
		</p>
		<?php
	}

    public function update( $new_instance, $old_instance ) {
		$instance = array();

        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['feature'] = ( !empty( $new_instance['feature'] ) ) ? strip_tags( $new_instance['feature'] ) : 'fresh_today';
        $instance['only'] = ( !empty( $new_instance['only'] ) ) ? $new_instance['only'] : '';
        $instance['exclude'] = ( !empty( $new_instance['exclude'] ) ) ? $new_instance['exclude'] : '';
        $instance['search'] = ( !empty( $new_instance['search'] ) ) ? strip_tags( $new_instance['search'] ) : '';
        $instance['username'] = ( !empty( $new_instance['username'] ) ) ? strip_tags( $new_instance['username'] ) : '';
        $instance['sort'] = ( !empty( $new_instance['sort'] ) ) ? strip_tags( $new_instance['sort'] ) : '';
        $instance['sort_direction'] = ( !empty( $new_instance['sort_direction'] ) ) ? strip_tags( $new_instance['sort_direction'] ) : 'desc';
		$instance['count'] = ( !empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '10';

		return $instance;
	}

}

new FiveHundred_Widget();