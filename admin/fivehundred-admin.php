<?php

/**
 * Admin functions for 500px Connector
 */
class FiveHundred_Admin {

    /**
     *  Construct our class
     */
    public function __construct() {
        $this->settings = array(
            'url'  => plugin_dir_url( __FILE__ ),
            'path' => plugin_dir_path( __FILE__ )
        );

        $this->consumer_key               = get_option( 'fivehundred_consumer_key' );
        $this->default_layout             = get_option( 'fivehundred_default_layout' );
        $this->default_layout_custom      = get_option( 'fivehundred_default_layout_custom' );
        $this->default_layout_custom_css  = get_option( 'fivehundred_default_layout_custom_css' );
        $this->remove_nsfw                = get_option( 'fivehundred_remove_nsfw' );
        $this->default_exclude_categories = get_option( 'fivehundred_default_exclude_categories' );

        // Add TinyMCE button for adding a shortcode
        add_action( 'init', array( $this, 'initialize_editor_buttons' ) );

        // Add styles for the button
        add_filter( 'mce_css', array( $this, 'custom_tinymce_styles' ) );

        // Pass PHP value to our TinyMCE JavaScript file
        // add_action( 'admin_head-post.php', array( $this, 'share_php_values_with_tinymce' ) );
        // add_action( 'admin_head-post-new.php', array( $this, 'share_php_values_with_tinymce' ) );

        // Create our plugin page
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
    }

    function initialize_editor_buttons() {
        add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_javascript') );
        add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons') );
    }

    function custom_tinymce_styles( $mce_css ) {
        $mce_css .= ', ' . $this->settings['url'] . 'assets/css/mce-style.css';
        return $mce_css;
    }

    function register_tinymce_javascript( $plugins ) {
       $plugins['fivehundred'] = $this->settings['url'] . 'assets/js/mce-script.js';
       return $plugins;
    }

    function register_tinymce_buttons( $buttons ) {
       array_push($buttons, 'fivehundred');
       return $buttons;
    }

    function share_php_values_with_tinymce() { ?>
        <script type='text/javascript'>
            var plugin_settings = {
                'url': '<?php echo $this->settings["url"]; ?>',
                'path': '<?php echo $this->settings["path"]; ?>'
            };
        </script>
        <?php
    }

    /**
     * Add plugin page under "Tools"
     *
     *  @return  void
     */
    public function add_settings_page() {
        $options_page = add_options_page(
            '500px Connector Settings',
            '500px Connector',
            'manage_options',
            'fivehundred-settings',
            array( $this, 'create_settings_page' )
        );

        add_action( 'load-'.$options_page, array( $this, 'load_admin_assets' ) );
    }

    /**
     * Enqueue plugin admin styles
     *
     *  @return  void
     */
    public function load_admin_assets() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * Register plugin admin styles
     *
     *  @return  void
     */
    public function enqueue_admin_assets() {
        // Enqueue the admin css
        wp_enqueue_style( 'fivehundred-admin-style', $this->settings['url'].'/assets/css/admin-style.css' );

        // Enqueue the admin js
        wp_enqueue_script( 'fivehundred-admin-js', $this->settings['url'].'/assets/js/admin-script.js', null, null, true );
    }

    /**
     * Management page callback
     *
     *  @return  void
     */
    public function create_settings_page() { ?>
        <div class="wrap">
            <h2>500px Connector</h2>

            <?php
                // Check if the consumer key form has been submitted
                $this->handle_consumer_key_form_submission();
                $consumer_key = $this->consumer_key;
            ?>
            <h3 class="title">Authorization</h3>
            <p>Enter your consumer key to authorize with the 500px API. Need a consumer key? <a href="https://500px.com/settings/applications" target="_blank">Register Here</a></p>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'fivehundred-credential-authorization' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="consumer_key">Consumer Key:</label></th>
                            <td>
                                <input type="text" id="consumer_key" name="consumer_key" <?php echo ($consumer_key)?'value="'.$consumer_key.'"':''; ?> style="width: 350px">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button( 'Connect' ) ?>
            </form>

            <br><hr><br>

            <?php
                // Check if the layout form has been submitted
                $this->handle_default_layout_form_submission();
                $layout = $this->default_layout;
                $layout_custom = $this->default_layout_custom;
                $layout_custom_css = $this->default_layout_custom_css;
            ?>
            <h3 class="title">Layout</h3>
            <p>Set the default layout to use for widgets and shortcodes.</p>
            <form method="post" enctype="multipart/form-data" class="default-layout-form">
                <?php wp_nonce_field( 'fivehundred-default-layout' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="default_layout">Default Layout:</label></th>
                            <td>
                                <?php
                                    $layouts = array(
                                        'image-title'        => 'Image / Title',
                                        'image-title-date'   => 'Image / Title / Date',
                                        'image-title-author' => 'Image / Title / Author',
                                        'image-author-date'  => 'Image / Author / Date',
                                        'custom'             => 'Custom',
                                    );
                                ?>
                                <select name="default_layout" id="default_layout">
                                    <?php foreach( $layouts as $key => $val ) : ?>
                                        <option id="default_layout[<?php echo $key; ?>]" name="default_layout" value="<?php echo $key; ?>" <?php echo ($layout == $key)?'selected="selected"':''; ?>>
                                            <?php echo $val; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="form-table">
                    <tbody>
                        <tr class="custom-layout">
                            <td>
                                <label for="default_layout_custom">
                                    <p>Custom Layout</p>
                                    <textarea name="default_layout_custom" id="default_layout_custom"><?php echo stripslashes( $layout_custom ); ?></textarea>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for="default_layout_custom_css">
                                    <p>Custom CSS</p>
                                    <textarea name="default_layout_custom_css" id="default_layout_custom_css"><?php echo stripslashes( $layout_custom_css ); ?></textarea>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button( 'Save' ) ?>
            </form>

            <br><hr><br>

            <?php
                // Check if the settings form has been submitted
                $this->handle_default_settings_form_submission();
                $remove_nsfw = $this->remove_nsfw;
                $default_exclude_categories = $this->default_exclude_categories;
            ?>
            <h3 class="title">Settings</h3>
            <p>Default settings to use for widgets and shortcodes.</p>
            <form method="post" enctype="multipart/form-data" class="default-settings-form">
                <?php wp_nonce_field( 'fivehundred-default-settings' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="remove_nsfw">NSFW</label></th>
                            <td>
                                <input type="checkbox" id="remove_nsfw" name="remove_nsfw" <?php echo ($remove_nsfw)?'checked="checked"':''; ?>> Remove material that isn't safe for work (nudity)
                            </td>
                        </tr>
                        <?php
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
                        ?>

                        <?php /*
                        <tr>
                            <th scope="row"><label for="default_exclude_categories">Exclude Categories</label></th>
                            <td>
                                <select multiple id="default_exclude_categories" name="default_exclude_categories[]">
                                    <?php foreach( $categories as $category ) : ?>
                                        <option value="<?php echo $category; ?>" <?php echo (!empty($default_exclude_categories) && in_array($category, $default_exclude_categories))?'selected="selected"':''; ?>><?php echo $category; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        */ ?>
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
        $consumer_key = $this->consumer_key;

        // Remove consumer key if empty
        if( !isset( $_POST['consumer_key'] ) && !$consumer_key ) {
            if( !empty( $consumer_key ) ) {

                // Update the consumer key option
                $updated_key = update_option( 'fivehundred_consumer_key', '' );
                $this->consumer_key = get_option( 'fivehundred_consumer_key' );

                // Check for saving errors
                if( $updated_key ) {
                    echo '<div id="message" class="updated notice is-dismissible">
                        <p>Consumer key has been updated.</p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>';
                } else {
                    echo '<div id="message" class="error notice is-dismissible">
                        <p>There was an error while updating the consumer key.</p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>';
                }

                return;
            }
        }

        // Check if a consumer key has been changed
        if( isset( $_POST['consumer_key'] ) && ( $consumer_key != $_POST['consumer_key'] ) ) {
            // Check for nonce
            check_admin_referer( 'fivehundred-credential-authorization' );

            // Update the consumer key option
            $updated_key = update_option( 'fivehundred_consumer_key', $_POST['consumer_key'] );
            $this->consumer_key = get_option( 'fivehundred_consumer_key' );

            // Check for saving errors
            if( $updated_key ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Consumer key has been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the consumer key.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            }

            return;
        }
    }

    /**
     *  Handle saving the default layout
     *
     *  @return  void
     */
    public function handle_default_layout_form_submission() {
        $layout = $this->default_layout;
        $layout_custom = $this->default_layout_custom;
        $layout_custom_css = $this->default_layout_custom_css;

        // Update default_layout value
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
            }
        }

        // Update default_layout_custom value
        if( isset( $_POST['default_layout_custom'] ) && ( $layout_custom != $_POST['default_layout_custom'] ) ) {
            // Check for nonce
            check_admin_referer( 'fivehundred-default-layout' );

            // Update the default layout option
            $updated_custom = update_option( 'fivehundred_default_layout_custom', $_POST['default_layout_custom'] );
            $this->default_layout_custom = get_option( 'fivehundred_default_layout_custom' );

            // Check for saving errors
            if( $updated_custom ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Custom default layout has been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the custom default layout.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            }
        }

        // Update default_layout_custom_css value
        if( isset( $_POST['default_layout_custom_css'] ) && ( $layout_custom_css != $_POST['default_layout_custom_css'] ) ) {
            // Check for nonce
            check_admin_referer( 'fivehundred-default-layout' );

            // Update the default layout option
            $updated_custom = update_option( 'fivehundred_default_layout_custom_css', $_POST['default_layout_custom_css'] );
            $this->default_layout_custom_css = get_option( 'fivehundred_default_layout_custom_css' );

            // Check for saving errors
            if( $updated_custom ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Custom default layout CSS has been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the custom default layout CSS.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            }
        }
    }

    /**
     *  Handle saving the default settings
     *
     *  @return  void
     */
    public function handle_default_settings_form_submission() {
        $remove_nsfw = $this->remove_nsfw;
        $default_exclude_categories = $this->default_exclude_categories;

        // Update nsfw value
        if( isset( $_POST['remove_nsfw'] ) && ( $remove_nsfw != $_POST['remove_nsfw'] ) ) {
            // Check for nonce
            check_admin_referer( 'fivehundred-default-settings' );

            // Update the remove_nsfw option
            $updated_nsfw = update_option( 'fivehundred_remove_nsfw', $_POST['remove_nsfw'] );
            $this->remove_nsfw = get_option( 'fivehundred_remove_nsfw' );

            // Check for saving errors
            if( $updated_nsfw ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Default settings have been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the nsfw setting.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            }
        }

        // Update nsfw value
        if( isset( $_POST['default_exclude_categories'] ) && ( $default_exclude_categories != $_POST['default_exclude_categories'] ) ) {
            // Check for nonce
            check_admin_referer( 'fivehundred-default-settings' );

            // Update the default_exclude_categories option
            $updated = update_option( 'fivehundred_default_exclude_categories', $_POST['default_exclude_categories'] );
            $this->default_exclude_categories = get_option( 'fivehundred_default_exclude_categories' );

            // Check for saving errors
            if( $updated ) {
                echo '<div id="message" class="updated notice is-dismissible">
                    <p>Default settings have been updated.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            } else {
                echo '<div id="message" class="error notice is-dismissible">
                    <p>There was an error while updating the default exclude categories.</p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>';
            }
        }
    }
}

new FiveHundred_Admin();