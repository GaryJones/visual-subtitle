<?php
/**
 * Visual Subtitle plugin.
 *
 * @package GaryJones\VisualSubtitle
 * @author  Gary Jones <gary@garyjones.co.uk>
 * @license GPL-2.0+
 * @link    http://code.garyjones.co.uk/plugins/visual-subtitle/
 * @version 1.0.1
 *
 * @wordpress-plugin
 * Plugin Name: Visual Subtitle
 * Plugin URI: http://code.garyjones.co.uk/plugins/visual-subtitle/
 * Description: Allows part of a post title to be styled as a subtitle. The subtitle is still within the title level 1 or 2 heading, but is wrapped in a <code>span</code> to be styled differently.
 * Version: 1.0.1
 * Author: Gary Jones
 * Author URI: http://garyjones.co.uk/
 * License: GPL-2.0+
 * Text Domain: visual-subtitle
 * Domain Path: /languages/
 */

/**
 * Plugin class for Visual Subtitle plugin.
 *
 * @package VisualSubtitle
 * @author  Gary Jones
 */
class Visual_Subtitle {

	/**
	 * Holds copy of instance, so other plugins can remove our hooks.
	 *
	 * @since 1.0.0
	 *
	 * @link http://core.trac.wordpress.org/attachment/ticket/16149/query-standard-format-posts.php
	 * @link http://twitter.com/#!/markjaquith/status/66862769030438912
	 *
	 * @var Visual_Subtitle
	 */
	static $instance;

	/**
	 * The name of the meta field key to which the postmeta is saved.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $meta_field = '_visual-subtitle';

	/**
	 * The name of the nonce.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $nonce = 'visual_subtitle_noncename';

	/**
	 * Adds a reference of this object to $instance, make plugin translatable,
	 * hook in the init() method.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::$instance = $this;

		/** Announce that the class is ready, and pass the object (for advanced use) */
		do_action_ref_array( 'visual_subtitle_init', array( &$this ) );

		add_action( 'init', array( $this, 'localization' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Support localization.
	 *
	 * @since 1.1.0
	 */
	public function localization() {
		// The "plugin_locale" filter is also used in load_plugin_textdomain()
		$locale = apply_filters( 'plugin_locale', get_locale(), 'visual-subtitle' );
		load_textdomain( 'visual-subtitle', WP_LANG_DIR . '/visual-subtitle/visual-subtitle-' . $locale . '.mo' );
		load_plugin_textdomain( 'visual-subtitle', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Hook in action and filter interactions.
	 *
	 * @since 1.1.0
	 *
	 * @see Visual_Subtitle::add()
	 * @see Visual_Subtitle::save()
	 * @see Visual_Subtitle::quick_edit()
	 * @see Visual_Subtitle::script()
	 * @see Visual_Subtitle::style()
	 * @see Visual_Subtitle::quick_edit_javascript()
	 * @see Visual_Subtitle::filter()
	 * @see Visual_Subtitle::doctitle_filter()
	 */
	public function init() {
		//if( post_type_supports( get_post_type(), 'title' ) ) {
			add_action( 'edit_form_after_title', array( $this, 'field' ) );
		//}
		add_action( 'admin_init',             array( $this, 'add_field' ) );
		add_action( 'save_post',              array( $this, 'save' ), 1, 2 );
		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit' ), 10, 2 );
		add_filter( 'the_title',              array( $this, 'filter' ), 10, 2 );
		add_filter( 'wp_title',               array( $this, 'doctitle_filter' ), 10, 3 );
		add_action( 'admin_enqueue_scripts',  array( $this, 'script' ) );
		add_action( 'admin_enqueue_scripts',  array( $this, 'style' ) );
	}

	/**
	 * Adds visual subtitle field for each post type that supports the title.
	 *
	 * Also adds a filter reference for each post type, since we're looping through them.
	 *
	 * @since 1.0.0
	 */
	public function add_field() {
		foreach ( get_post_types() as $post_type ) {
			if( post_type_supports( $post_type, 'title' ) ) {
				// Posts
				add_filter( 'manage_posts_columns',       array( $this, 'columns' ) );
				add_filter( 'manage_posts_custom_column', array( $this, 'custom_column' ), 10, 2 );
				// Pages
				add_filter( 'manage_pages_columns',       array( $this, 'columns' ) );
				add_filter( 'manage_pages_custom_column', array( $this, 'custom_column' ), 10, 2 );
			}
		}
	}

	/**
	 * Echos the contents of the Visual Subtitle meta box.
	 *
	 * @since 1.0.0
	 *
	 * @global WP_Post $post
	 */
	public function field() {
		global $post;

		$value = get_post_meta( $post->ID, $this->meta_field, true );
		?>
		<div class="visual-subtitle-wrap">
			<?php
			wp_nonce_field( plugin_basename( __FILE__ ), $this->nonce );
			?>
			<label class="screen-reader-text" for="visual-subtitle" id="visual-subtitle-prompt-text"><?php _e( 'Enter visual subtitle here', 'visual-subtitle' ); ?></label>
			<input type="text" id="visual-subtitle" name="<?php echo $this->meta_field; ?>" class="large-text" value="<?php esc_attr_e( $value ); ?>" aria-labelledby="visual-subtitle-prompt-text" />
			<span class="description"><?php sprintf( __( 'This is still part of the post heading, but may be wrapped in %s tags for you to style.', 'visual-subtitle' ), '<code>&lt;span&gt;</code>'); ?></span>
		</div>
		<?php
	}

	/**
	 * Enqueue script file.
	 *
	 * @since 1.1.0
	 *
	 * @todo Limit to Edit screen, or index screen
	 */
	public function script() {
//		global $current_screen;
//
//		if ( ($current_screen->base != 'edit') )
//			return;

		wp_enqueue_script( 'visual-subtitle', plugin_dir_url(__FILE__) .'js/visual-subtitle.js', array( 'jquery', 'post' ), '1.1.0', true );
	}

	/**
	 * Enqueue style file.
	 *
	 * @since 1.1.0
	 *
	 * @todo Limit to just the screens that need styles.
	 */
	public function style() {
		wp_enqueue_style( 'visual-subtitle', plugin_dir_url(__FILE__) .'css/visual-subtitle.css', array(), '1.1.0' );
	}

	/**
	 * Save the visual subtitle value if authorised to.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $post_id
	 * @param WP_Post $post
	 *
	 * @return integer
	 */
	function save( $post_id, $post ) {

		//	Verify the nonce
		if ( ! isset( $_POST[$this->nonce] ) || ! wp_verify_nonce( $_POST[$this->nonce], plugin_basename( __FILE__ ) ) )
			return $post->ID;

		//	Don't try to save the data under autosave, or future post (we do allow ajax, for quick edit)
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post->ID;
		if ( defined( 'DOING_CRON' ) && DOING_CRON )
			return $post->ID;

		// Check permissions
		if ( ( 'page' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post->ID ) ) || ! current_user_can( 'edit_post', $post->ID ) )
			return $post->ID;

		// Save (or delete) the value.
		if ( isset( $_POST[$this->meta_field] ) )
			update_post_meta( $post_id, $this->meta_field, $_POST[$this->meta_field] );
		else
			delete_post_meta( $post_id, $this->meta_field );

	}

	/**
	 * Add a custom column.
	 *
	 * Quick Edit Custom Box action only works if there are custom columns, so
	 * this is needed, even though we visually hide the tabular column with CSS.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Existing post / page columns.
	 *
	 * @return array Amended post / page coulumns.
	 */
	function columns( $columns ) {

		$columns['visual-subtitle'] = __( 'Subtitle', 'visual-subtitle' );
		return $columns;

	}

	/**
	 * Populate the custom column.
	 *
	 * We echo out the subtitle value, wrapped in an element so it can be grabbed
	 * by JavaScript later on. The column which would show this value is visually
	 * hidden.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $name Column name.
	 * @param integer $id Post ID
	 *
	 * @return null Returns early if the column name is incorrect.
	 */
	function custom_column( $name, $id ) {

		if ( 'visual-subtitle' != $name )
			return;

		echo '<span id="inline_' . $id . '_visual_subtitle">' . esc_html( get_post_meta( $id, $this->meta_field, true ) ) . '</span>';

	}

	/**
	 * Echo output into the Quick Edit feature.
	 *
	 * @since 1.0.0
	 *
	 * @global stdClass $post
	 *
	 * @param string $column_name
	 * @param string $post_type
	 *
	 * @return null Returns early if the column name is incorrect
	 */
	function quick_edit( $column_name, $post_type ) {

		if( 'visual-subtitle' != $column_name )
			return;

		global $post;

		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<?php wp_nonce_field( plugin_basename( __FILE__ ), $this->nonce ); ?>
				<label>
					<span class="title"><?php _e( 'Subtitle', 'visual-subtitle' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" id="post_subtitle" name="_visual-subtitle" value="<?php esc_attr_e( get_post_meta( $post->ID, $this->meta_field, true ) ); ?>" />
					</span>
				</label>
			</div>
		</fieldset>
		<?php

	}

	/**
	 * Add JavaScript that populates the quick edit subtitle box from the hidden
	 * subtitle column value.
	 *
	 * @since 1.0.0
	 *
	 * @global string $current_screen
	 * @return null Returns null if the screen is not an edit page.
	 */
	function quick_edit_javascript() {

		global $current_screen;

		if ( ($current_screen->base != 'edit') )
			return;

		?>

		<script type="text/javascript">
			jQuery(function($) {
				$('a.editinline').live('click', function() {
					var id = inlineEditPost.getId(this);
					inlineEditPost.revert();
					$('#post_subtitle').val($('#inline_' + id + '_visual_subtitle').text());
				});
			});
		</script>
		<?php

	}

	/**
	 * Filter the post title on the Posts screen, and on the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @global string $current_screen
	 * @param string $title
	 * @param integer $id
	 * @return string
	 */
	function filter( $title, $id ) {

		global $current_screen;

		$subtitle = get_post_meta( $id, $this->meta_field, true );

		if ( ! $subtitle )
			return $title;

		if ( is_admin() && ( 'edit' == $current_screen->base || defined( 'DOING_AJAX' ) && DOING_AJAX ) )
			$title = $title . ' | ' . $subtitle;
		elseif ( is_admin() && 'edit-comments' == $current_screen->base )
			/* Don't add the subtitle in - also, don't split by |, as it looks bad with the underlined link */
			$title = $title; /* Yes, redundant code - bite me */
		else
			$title = $title . ' <span class="subtitle">' . $subtitle . '</span>';

		return $title;

	}

	/**
	 * Filter the document title in the head.
	 *
	 * @since 1.0.0
	 *
	 * @global stdClass $post
	 * @param string $title
	 * @return string
	 */
	function doctitle_filter( $title, $sep, $seplocation ) {

		global $post;

		$subtitle = get_post_meta( $post->ID, $this->meta_field, true );

		if ( ! $subtitle )
			return $title;

		$title = $title . ': ' . $subtitle;

		return $title;

	}

}

new Visual_Subtitle;