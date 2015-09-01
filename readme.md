# 500px Connector (WIP)

> A WordPress plugin to display 500px photo streams.


### Shortcode


### Filters/Actions

`fivehundred_shortcode_item_contents`

`fivehundred_shortcode_contents`

`fivehundred_shortcode_no_results`


### To-Do

- Add in some filters and actions to make it more extensible
- Allow the user to set default options for widgets and shortcodes
   - Exclude certain categories ("Nude" & per photo "nsfw": true)
- Make a list of what options can be used, and what can be used together (i.e. feature="user", username="kjbrum")
- Add some JS for showing/hiding certain fields when certain fields are filled, as well as setting values (i.e. username="kjbrum" => feature="user", hide "search" field)
- Find a better way to use a single array of values throughout the plugin
- Go through and make sure the plugin is secure (should write a blog post on this)


### Notes

- Username and search can't be used together
- Username makes "feature" = "user"

### Changelog