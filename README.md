# Visual Subtitle

Allows part of a post title to be styled as a subtitle. It is still within the title heading, but is wrapped in a span to be styled differently.

## Installation

### Upload

1. Download the latest tagged archive (choose the "zip" option).
2. Go to the __Plugins -> Add New__ screen and click the __Upload__ tab.
3. Upload the zipped archive directly.
4. Go to the Plugins screen and click __Activate__.

### Manual

1. Download the latest tagged archive (choose the "zip" option).
2. Unzip the archive.
3. Copy the folder to your `/wp-content/plugins/` directory.
4. Go to the Plugins screen and click __Activate__.

Check out the Codex for more information about [installing plugins manually](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

### Git

Using git, browse to your `/wp-content/plugins/` directory and clone this repository:

`git clone git@github.com:GaryJones/Visual-Subtitle.git`

Then go to your Plugins screen and click __Activate__.

## Description 

This plugin adds a Visual Subtitle field to all post, page and custom post types that have support for a title.

In a future version, this plugin may include a user interface with a list of checkboxes, so users can deselect which post types visual subtitle is applied to.

Those comfortable with code can filter the indexed array of post types via the `visual_subtitle_supported_types` filter.

The plugin allows you to include a string of text that will still be part of the post title, but be wrapped in a `small` tag, giving something like:

`<h1>Main Title<small class="subtitle">This is a subtitle</small></h1>`

Keeping it as part of the main level 1 or two heading, means it maintains as much keyword SEO importance as the main title, yet can be given a style (in your own theme) of `display: block;` to make it visually appear as a subtitle.

The visual subtitle is appended to to the title on on the Posts screen, with a ` | ` separator, and appended to the title part of the document title with a filterable colon and space (`: `) separator.

## Screenshots

![The back-end interface, showing the Visual Subtitle field.](https://raw.github.com/GaryJones/Visual-Subtitle/master/assets-wp-repo/screenshot-1.png)  
_The back-end interface, showing the Visual Subtitle field._

---

![Showing the visual subtitle on the front-end, in this case, styled red, smaller, italic and bold.](https://raw.github.com/GaryJones/Visual-Subtitle/master/assets-wp-repo/screenshot-2.png)  
_Showing the visual subtitle on the front-end, in this case, styled red, smaller, italic and bold._

---

![The front-end markup, showing the span inside the existing heading element (may differ for your own theme).](https://raw.github.com/GaryJones/Visual-Subtitle/master/assets-wp-repo/screenshot-3.png)  
_The front-end markup, showing the span inside the existing heading element (may differ for your own theme)._

---

![Showing the subtitle in the Posts list - the second entry has no subtitle.](https://raw.github.com/GaryJones/Visual-Subtitle/master/assets-wp-repo/screenshot-4.png)  
_Showing the subtitle in the Posts list - the second entry has no subtitle._

---

![The subtitle can also be amended from the quick edit feature.](https://raw.github.com/GaryJones/Visual-Subtitle/master/assets-wp-repo/screenshot-5.png)  
_The subtitle can also be amended from the quick edit feature._

## Credits

Built by [Gary Jones](https://twitter.com/GaryJ)  
Copyright 2013 [Gamajo Tech](http://gamajo.com/)
