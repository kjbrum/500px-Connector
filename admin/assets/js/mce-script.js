(function() {
    tinymce.PluginManager.add('fivehundred', function(editor) {

        var popupBody = [
            {
                type: 'label', text: 'This form allows you to easily add a 500px photo feed shortcode.'
            },
            {
                type: 'textbox', name: 'heading', label: 'Heading:'
            },
            {
                type: 'listbox',
                name: 'feature',
                label: 'Feature:',
                'values': [
                    {text: 'Fresh Today (default)', value: ''},
                    {text: 'Fresh Yesterday', value: 'fresh_yesterday'},
                    {text: 'Fresh Week', value: 'fresh_week'},
                    {text: 'Popular', value: 'popular'},
                    {text: 'Highest Rated', value: 'highest_rated'},
                    {text: 'Upcoming', value: 'upcoming'},
                    {text: 'Editors', value: 'editors'}
                ]
            },
            {
                type: 'listbox',
                name: 'categories',
                label: 'Only include photos in:',
                'values': [
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
                ]
            },
            {
                type: 'listbox',
                name: 'exclude_categories',
                label: 'Exclude photos in:',
                'values': [
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
                ]
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
                'values': [
                    {text: '', value: ''},
                    {text: 'Created At', value: 'created_at'},
                    {text: 'Rating', value: 'rating'},
                    {text: 'Highest Rating', value: 'highest_rating'},
                    {text: 'Times Viewed', value: 'times_viewed'},
                    {text: 'Votes Count', value: 'votes_count'},
                    {text: 'Favorites Count', value: 'favorites_count'},
                    {text: 'Comments Count', value: 'comments_count'},
                    {text: 'Taken At', value: 'taken_at'}
                ]
            },
            {
                type: 'listbox',
                name: 'sort_direction',
                label: 'Sort Direction:',
                'values': [
                    {text: 'Descending (default)', value: ''},
                    {text: 'Ascending', value: 'ascending'}
                ]
            },
            {
                type: 'textbox', name: 'count', label: 'Count:'
            }
            // {
            //     type: 'textbox', minHeight: 100, minWidth: 250, multiline: true, name: 'note', label: 'Note:'
            // }
        ];

        // Add a button that opens a window
        editor.addButton('fivehundred', {
            text: '500px',
            // image: plugin_settings.url + 'assets/images/500px.png',
            // icon: true,
            tooltip: 'Add a 500px photo feed shortcode',
            onclick: function() {
                // Open window
                editor.windowManager.open({
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
                        // tinyMCE.activeEditor.selection.setContent('[fivehundred feature="'+e.data.feature+'" term="'+e.data.term+'" username="'+e.data.username+'" only="'+e.data.only+'" exclude="'+e.data.exclude+'" sort="'+e.data.sort+'" sort_direction="'+e.data.sort_direction+'" rpp="'+e.data.rpp+'" heading="'+e.data.heading+'"]');
                        tinyMCE.activeEditor.selection.setContent(shortcode);
                    }
                });
            }
        });

        // Adds a menu item to the tools menu
        // editor.addMenuItem('fivehundred', {
        //     text: 'Add a shortcode to display a 500px photo feed',
        //     context: 'tools',
        //     onclick: function() {
        //         // Open window with a specific url
        //         editor.windowManager.open({
        //             title: 'TinyMCE site',
        //             url: 'http://www.tinymce.com',
        //             width: 800,
        //             height: 600,
        //             buttons: [{
        //                 text: 'Close',
        //                 onclick: 'close'
        //             }]
        //         });
        //     }
        // });
    });
})();