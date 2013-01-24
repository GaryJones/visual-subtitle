<?php
/**
 * Visual Subtitle plugin.
 *
 * @package GaryJones\VisualSubtitle
 * @author  Gary Jones <gary@garyjones.co.uk>
 * @license GPL-2.0+
 * @link    http://code.garyjones.co.uk/plugins/visual-subtitle/
 * @version 1.1.0
 *
 * @wordpress-plugin
 * Plugin Name: Visual Subtitle
 * Plugin URI: http://code.garyjones.co.uk/plugins/visual-subtitle/
 * Description: Allows part of a post title to be styled as a subtitle. The subtitle is still within the title level 1 or 2 heading, but is wrapped in a <code>span</code> to be styled differently.
 * Version: 1.1.0
 * Author: Gary Jones
 * Author URI: http://garyjones.co.uk/
 * License: GPL-2.0+
 * Text Domain: visual-subtitle
 * Domain Path: /languages/
 */

require plugin_dir_path( __FILE__ ) . 'class-visual-subtitle.php';
 
$visual_subtitle = new Visual_Subtitle;