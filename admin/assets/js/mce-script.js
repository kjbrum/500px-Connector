(function() {
    tinymce.create('tinymce.plugins.fivehundred', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            var feature = [
                {text: 'Fresh Today (default)', value: ''},
                {text: 'Fresh Yesterday', value: 'fresh_yesterday'},
                {text: 'Fresh Week', value: 'fresh_week'},
                {text: 'Popular', value: 'popular'},
                {text: 'Highest Rated', value: 'highest_rated'},
                {text: 'Upcoming', value: 'upcoming'},
                {text: 'Editors', value: 'editors'}
            ];

            var categories = [
                {text: '', value: ''},
                {text: 'Uncategorized', value: 'Uncategorized'},
                {text: 'Abstract', value: 'Abstract'},
                {text: 'Animals', value: 'Animals'},
                {text: 'Black and White', value: 'Black and White'},
                {text: 'Celebrities', value: 'Celebrities'},
                {text: 'City and Architecture', value: 'City and Architecture'},
                {text: 'Commercial', value: 'Commercial'},
                {text: 'Concert', value: 'Concert'},
                {text: 'Family', value: 'Family'},
                {text: 'Fashion', value: 'Fashion'},
                {text: 'Film', value: 'Film'},
                {text: 'Fine Art', value: 'Fine Art'},
                {text: 'Food', value: 'Food'},
                {text: 'Journalism', value: 'Journalism'},
                {text: 'Landscapes', value: 'Landscapes'},
                {text: 'Macro', value: 'Macro'},
                {text: 'Nature', value: 'Nature'},
                {text: 'Nude', value: 'Nude'},
                {text: 'People', value: 'People'},
                {text: 'Performing Arts', value: 'Performing Arts'},
                {text: 'Sport', value: 'Sport'},
                {text: 'Still Life', value: 'Still Life'},
                {text: 'Street', value: 'Street'},
                {text: 'Transportation', value: 'Transportation'},
                {text: 'Travel', value: 'Travel'},
                {text: 'Underwater', value: 'Underwater'},
                {text: 'Urban Exploration', value: 'Urban Exploration'},
                {text: 'Wedding', value: 'Wedding'}
            ];

            var sort = [
                {text: '', value: ''},
                {text: 'Created At', value: 'created_at'},
                {text: 'Rating', value: 'rating'},
                {text: 'Highest Rating', value: 'highest_rating'},
                {text: 'Times Viewed', value: 'times_viewed'},
                {text: 'Votes Count', value: 'votes_count'},
                {text: 'Favorites Count', value: 'favorites_count'},
                {text: 'Comments Count', value: 'comments_count'},
                {text: 'Taken At', value: 'taken_at'}
            ];

            var popupBody = [
                {
                    type: 'label', text: 'Easily add a 500px photo stream shortcode.'
                },
                {
                    type: 'textbox', name: 'heading', label: 'Heading:'
                },
                {
                    type: 'listbox',
                    name: 'feature',
                    label: 'Feature:',
                    'values': feature
                },
                {
                    type: 'listbox',
                    name: 'categories',
                    label: 'Only include photos in:',
                    'values': categories
                },
                {
                    type: 'listbox',
                    name: 'exclude_categories',
                    label: 'Exclude photos in:',
                    'values': categories
                },
                {
                    type: 'textbox', name: 'search', label: 'Search:'
                },
                {
                    type: 'textbox', name: 'username', label: 'Username:'
                },
                {
                    type: 'listbox',
                    name: 'sort',
                    label: 'Sort By:',
                    'values': sort
                },
                {
                    type: 'listbox',
                    name: 'sort_direction',
                    label: 'Sort Direction:',
                    'values': [
                        {text: 'Descending (default)', value: ''},
                        {text: 'Ascending', value: 'asc'}
                    ]
                },
                {
                    type: 'textbox', name: 'count', label: 'Count:'
                }
            ];

            // Add a button that opens a window
            ed.addButton('fivehundred', {
                text: '500px',
                // image: plugin_settings.url + 'assets/images/500px.png',
                // icon: true,
                tooltip: 'Add a 500px photo stream shortcode',
                cmd: 'btnPopup'
            });

            ed.addCommand('btnPopup', function() {
                ed.windowManager.open({
                    title: '500px Photo Feed',
                    body: popupBody,
                    onsubmit: function(e) {
                        var formData = e.data;
                        var shortcode = '[fivehundred';

                        for(var key in formData) {
                            if(formData.hasOwnProperty(key)) {
                                var value = formData[key];
                                if (value !== undefined && value !== null && value) {
                                    shortcode += ' '+key+'="'+value+'"';
                                }
                            }
                        }

                        shortcode += ']';

                        selection = tinyMCE.activeEditor.selection.getContent();
                        tinyMCE.activeEditor.selection.setContent(shortcode);
                    }
                });
            });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : '500px Connector Button',
                author : 'Kyle Brumm',
                authorurl : 'http://kylebrumm.com',
                infourl : 'https://github.com/kjbrum/500px-Connector',
                version : "0.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'fivehundred', tinymce.plugins.fivehundred );
})();