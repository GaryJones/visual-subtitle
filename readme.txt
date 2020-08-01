=== Plugin Name ===
Contributors: GaryJ
Donate link: http://code.garyjones.co.uk/donate/
Tags: subtitle
Requires at least: 2.9.0
Requires PHP: 5.6
Tested up to: 5.5
Stable tag: 1.2.0

Allows part of a post title to be styled as a subtitle. It is still within the title heading, but is wrapped in a span to be styled differently.

== Description ==

This plugin adds a Visual Subtitle field to all post, page and custom post types that have support for a title, and that use the Classic Editor. It doesn't work with the Block Editor.

It allows you to include a string of text that will still be part of the post title, but be wrapped in a `span` tag, giving something like:

`<h1>Visual Subtitle<span class="subtitle"></span></h1>`

Keeping it as part of the main level 1 or two heading, means it maintains as much keyword SEO importance as the main title, yet can be given a style (in your own theme) of `display: block;` to make it visually appear as a subtitle.

The visual subtitle is appended to to the title on on the Posts screen, separated with a pipe (`|`) character, and appended to the title part of the document title with a colon (`:`) character.

== Installation ==

1. Unzip and upload `visual-subtitle` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Can I change the separators? =

Only currently by amending the plugin code.

= My subtitle isn't appearing on a new line? =

You need to add the `.subtitle { display: block; }` style to your own theme, along with any other styling you want for it.

== Screenshots ==

1. The back-end interface, showing the Visual Subtitle field.
1. Showing the visual subtitle on the front-end, in this case, styled red, smaller, italic and bold.
1. The front-end markup, showing the span inside the existing heading element (may differ for your own theme).
1. Showing the subtitle in the Posts list - the second entry has no subtitle.
1. The subtitle can also be amended from the quick edit feature.

== Changelog ==

= 1.2.0 =
* Refreshed for 2020 (fix code standards).
* Fix line-endings in the class file to avoid parser error in PHP 7.3.
* Slightly refactor how the plugin is initiated.

= 1.1.0 =
* Reposition subtitle field to after title.
* Add ability for developers to choose which post types have visual subtitle support.
* Code tidy-up

= 1.0.1 =
* Fixed display issue on Comments page.
* Added key bit of code that makes the plugin read translation files (props to [Dave](http://deckerweb.de/) for the reminder).
* Added link to German (de_DE) translation file in the readme.

= 1.0 =
* First public version.

== Upgrade Notice ==

= 1.2.0 =
Code refresh for 2020.

= 1.1.0 =
Reposition subtitle field, improvements for developers.

= 1.0.1 =
Minor fix - make plugin translatable, and fix small display issue on Comments page.

= 1.0 =
Update from nothingness. You will feel better for it.

== Translations ==

* [Deutsch](http://deckerweb.de/material/sprachdateien/wordpress-plugins/#visual-subtitle)