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
            'url'       => plugin_dir_url( __FILE__ ),
            'path'      => plugin_dir_path( __FILE__ )
        );

        $this->consumer_key = get_option( 'fivehundred_consumer_key' );

        // Set the default layout if one hasn't been choosen
        if( !get_option( 'fivehundred_default_layout' ) ) {
            update_option( 'fivehundred_default_layout', 'image-title' );
        }

        $this->default_layout = get_option( 'fivehundred_default_layout' );


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
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'widgets_init', array( $this, 'register_widget' ) );
    }

    public function register_widget() {
        register_widget( 'FiveHundred_Widget' );
    }

    /**
     * Add plugin page under "Tools"
     *
     *  @return  void
     */
    public function add_plugin_page() {
        add_options_page(
            '500px Connector',
            '500px Connector',
            'manage_options',
            'fivehundred-general',
            array( $this, 'create_general_page' )
        );
    }

    /**
     * Management page callback
     *
     *  @return  void
     */
    public function create_general_page() { ?>
        <div class="wrap">
            <h2>500px Connector</h2>

            <?php
                // Check if the consumer key form has been submitted
                $this->handle_consumer_key_form_submission();
                $key = $this->consumer_key;
            ?>
            <h3 class="title">Authorization</h3>
            <p>Enter your consumer key to authorize with the 500px API. Need a consumer key? <a href="https://500px.com/settings/applications" target="_blank">Register Here</a></p>
            <form  method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'fivehundred-credential-authorization' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="consumer_key">Consumer Key:</label></th>
                            <td>
                                <input type="text" id="consumer_key" name="consumer_key" <?php echo ($key)?'value="'.$key.'"':''; ?> style="width: 350px">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button( 'Connect' ) ?>
            </form>

            <hr>

            <?php
                // Check if the layout form has been submitted
                $this->handle_layout_form_submission();
                $layout = $this->default_layout;
            ?>
            <h3 class="title">Layout</h3>
            <p>Set the default layout to use for widgets and shortcodes.</p>
            <form  method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'fivehundred-default-layout' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <td style="width:25%">
                                <input type="radio" id="default_layout[image-title]" name="default_layout" value="image-title" <?php echo ($layout == 'image-title')?'checked="checked"':''; ?>>
                                <label for="default_layout[image-title]">
                                    Image / Title<br>
                                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/assets/images/layout1.png" alt="layout1">
                                </label>
                            </td>
                            <td style="width:25%">
                                <input type="radio" id="default_layout[image-title-date]" name="default_layout" value="image-title-date"  <?php echo ($layout == 'image-title-date')?'checked="checked"':''; ?>>
                                <label for="default_layout[image-title-date]">
                                    Image / Title / Date<br>
                                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/assets/images/layout2.png" alt="layout2">
                                </label>
                            </td>
                            <td style="width:25%">
                                <input type="radio" id="default_layout[image-title-author]" name="default_layout" value="image-title-author"  <?php echo ($layout == 'image-title-author')?'checked="checked"':''; ?>>
                                <label for="default_layout[image-title-author]">
                                    Image / Title / Author<br>
                                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/assets/images/layout3.png" alt="layout3">
                                </label>
                            </td>
                            <td style="width:25%">
                                <input type="radio" id="default_layout[image-author-date]" name="default_layout" value="image-author-date"  <?php echo ($layout == 'image-author-date')?'checked="checked"':''; ?>>
                                <label for="default_layout[image-author-date]">
                                    Image / Author / Date<br>
                                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/assets/images/layout4.png" alt="layout4">
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <input type="radio" id="default_layout[custom-layout]" name="default_layout" value="custom-layout" <?php echo ($layout == 'custom-layout')?'checked="checked"':''; ?>>
                                <label for="default_layout[custom-layout]">
                                    Custom
                                </label><br>
                                <textarea name="default_layout_custom" id="default_layout_custom"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button( 'Save' ) ?>
            </form>

        </div>
    <?php
    }

    /**
     *  Handle saving the consumer key
     *
     *  @return  void
     */
    public function handle_consumer_key_form_submission() {
        $key = $this->consumer_key;

        // Remove consumer key if empty
        if( !isset( $_POST['consumer_key'] ) && !$key ) {
            if( !empty($key) ) {
                update_option('fivehundred_consumer_key', '' );

                $this->consumer_key = get_option( 'fivehundred_consumer_key' );

                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Consumer key has been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';

                return;
            }
        }

        // Check if a consumer key has been changed
        if( isset( $_POST['consumer_key'] ) && ( $key != $_POST['consumer_key'] ) ) {
            // Check for nonce
            check_admin_referer( 'fivehundred-credential-authorization' );

            // Update the consumer key option
            $updated = update_option( 'fivehundred_consumer_key', $_POST['consumer_key'] );
            $this->consumer_key = get_option( 'fivehundred_consumer_key' );

            // Check for saving errors
            if( $updated ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Consumer key has been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the consumer key.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';

                return;
            }
        }
    }

    /**
     *  Handle saving the default layout
     *
     *  @return  void
     */
    public function handle_layout_form_submission() {
        $layout = $this->default_layout;
        if( isset( $_POST['default_layout'] ) && ( $layout != $_POST['default_layout'] ) ) {
            // Check for nonce
            check_admin_referer( 'fivehundred-default-layout' );

            // Update the default layout option
            $updated = update_option( 'fivehundred_default_layout', $_POST['default_layout'] );
            $this->default_layout = get_option( 'fivehundred_default_layout' );

            // Check for saving errors
            if( $updated ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Default layout has been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the default layout.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';

                return;
            }
        }
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

