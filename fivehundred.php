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


        // Require the shortcodes and functions
        require_once( 'includes/fivehundred-shortcodes.php' );
        require_once( 'fivehundred-functions.php' );

        // Require our admin files
        if ( is_admin() ) {
            // Require the admin functionality
            require_once( 'admin/fivehundred-admin.php' );
        }

        // Create our plugin page
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
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
                // Check if the form has been submitted
                $this->handle_form_submission();
                $key = $this->consumer_key;
            ?>

            <h3 class="title">Authorization</h3>
            <p>Enter your consumer key to authorize with the 500px API. Need a consumer key? <a href="https://500px.com/settings/applications" target="_blank">Register Here</a></p>
            <form  method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'credential-authorization' ); ?>
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
        </div>
    <?php
    }

    /**
     *  Handle the uploading of an SVG
     *
     *  @return  void
     */
    public function handle_form_submission() {
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

        // Check if the consumer key has been entered
        if( isset( $_POST['consumer_key'] ) && ( $key != $_POST['consumer_key'] ) ) {
            // Check for nonce
            check_admin_referer( 'credential-authorization' );

            // Update the options
            $updated = update_option( 'fivehundred_consumer_key', $_POST['consumer_key'] );

            $this->consumer_key = get_option( 'fivehundred_consumer_key' );

            // Check for saving errors
            if( $updated ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Settings have been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the settings.</p>
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

