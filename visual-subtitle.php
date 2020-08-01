<?php
/**
 * Visual Subtitle plugin.
 *
 * @package GaryJones\VisualSubtitle
 * @author  Gary Jones
 * @license GPL-2.0-or-later
 * @link    https://github.com/GaryJones/visual-subtitle
 * @version 1.2.0
 *
 * @wordpress-plugin
 * Plugin Name: Visual Subtitle
 * Plugin URI: https://github.com/GaryJones/visual-subtitle
 * Description: Allows part of a post title to be styled as a subtitle. The subtitle is still within the title level 1 or 2 heading, but is wrapped in a <code>span</code> to be styled differently.
 * Version: 1.2.0
 * Author: Gary Jones
 * Author URI: https://garyjones.io/
 * License: GPL-2.0-or-later
 * Text Domain: visual-subtitle
 */

/**
 * Require the main class file.
 */
require plugin_dir_path( __FILE__ ) . 'class-visual-subtitle.php';

add_action(
	'init',
	function () {
		$visual_subtitle = new Visual_Subtitle();
		$visual_subtitle->init();
	}
);
