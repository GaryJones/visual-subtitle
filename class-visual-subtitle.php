<?php
/**
 * Visual Subtitle plugin.
 *
 * @package GaryJones\VisualSubtitle
 * @author  Gary Jones
 * @license GPL-2.0-or-later
 * @link    https://github.com/GaryJones/visual-subtitle
 */

/**
 * Plugin class for Visual Subtitle plugin.
 *
 * @package GaryJones\VisualSubtitle
 * @author  Gary Jones
 */
class Visual_Subtitle {

	/**
	 * Holds copy of instance, so other plugins can remove our hooks.
	 *
	 * @since 1.0.0
	 *
	 * @link https://core.trac.wordpress.org/attachment/ticket/16149/query-standard-format-posts.php
	 * @link https://twitter.com/markjaquith/status/66862769030438912
	 *
	 * @var Visual_Subtitle
	 */
	public static $instance;

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
	}

	/**
	 * Support localization.
	 *
	 * @since 1.1.0
	 */
	public function localization() {
		// The "plugin_locale" filter is also used in load_plugin_textdomain().
		$locale = apply_filters( 'plugin_locale', get_locale(), 'visual-subtitle' );
		load_textdomain( 'visual-subtitle', WP_LANG_DIR . '/visual-subtitle/visual-subtitle-' . $locale . '.mo' );
		load_plugin_textdomain( 'visual-subtitle', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Hook in action and filter interactions.
	 *
	 * @since 1.1.0
	 *
	 * @see Visual_Subtitle::field()
	 * @see Visual_Subtitle::add_field()
	 * @see Visual_Subtitle::save()
	 * @see Visual_Subtitle::quick_edit()
	 * @see Visual_Subtitle::filter()
	 * @see Visual_Subtitle::doctitle_filter()
	 * @see Visual_Subtitle::script()
	 * @see Visual_Subtitle::style()
	 */
	public function init() {
		$this->localization();

		add_action( 'edit_form_after_title', array( $this, 'field' ) );
		add_action( 'admin_init', array( $this, 'add_field' ) );
		add_action( 'save_post', array( $this, 'save' ), 1, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 10, 2 );
		add_filter( 'the_title', array( $this, 'filter' ), 10, 2 );
		add_filter( 'wp_title', array( $this, 'doctitle_filter' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'style' ) );
	}

	/**
	 * Add visual subtitle field to the index screen for each post type that is
	 * supported.
	 *
	 * @since 1.0.0
	 */
	public function add_field() {
		foreach ( get_post_types() as $post_type ) {
			if ( $this->has_support( $post_type ) ) {
				// Posts.
				add_filter( 'manage_posts_columns', array( $this, 'columns' ) );
				add_action( 'manage_posts_custom_column', array( $this, 'custom_column' ), 10, 2 );
				// Pages.
				add_filter( 'manage_pages_columns', array( $this, 'columns' ) );
				add_action( 'manage_pages_custom_column', array( $this, 'custom_column' ), 10, 2 );
			}
		}
	}

	/**
	 * Echo the visual subtitle field to the Edit screen, after the title field.
	 *
	 * @since 1.0.0
	 */
	public function field() {
		$subtitle = $this->get_subtitle();
		$label    = __( 'Enter visual subtitle here', 'visual-subtitle' );
		$label    = apply_filters( 'visual_subtitle_prompt_text', $label );
		?>
		<div class="visual-subtitle-wrap">
			<?php
			wp_nonce_field( plugin_basename( __FILE__ ), $this->nonce );
			?>
			<label for="visual-subtitle" class="screen-reader-text"><?php echo esc_html( $label ); ?></label>
			<input type="text" id="visual-subtitle" name="<?php echo esc_attr( $this->meta_field ); ?>" class="large-text" value="<?php echo esc_attr( $subtitle ); ?>" />
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
		wp_enqueue_script(
			'visual-subtitle',
			plugin_dir_url( __FILE__ ) . 'js/visual-subtitle.js',
			array( 'jquery', 'post' ),
			'1.2.0',
			true
		);
	}

	/**
	 * Enqueue style file.
	 *
	 * @since 1.1.0
	 *
	 * @todo Limit to just the screens that need styles.
	 */
	public function style() {
		wp_enqueue_style(
			'visual-subtitle',
			plugin_dir_url( __FILE__ ) . 'css/visual-subtitle.css',
			array(),
			'1.2.0'
		);
	}

	/**
	 * Save the visual subtitle value if authorised to.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return integer
	 */
	public function save( $post_id, $post ) {

		// Verify the nonce.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce doesn't need to be sanitized.
		if ( ! isset( $_POST[ $this->nonce ] ) || ! wp_verify_nonce( $_POST[ $this->nonce ], plugin_basename( __FILE__ ) ) ) {
			return $post->ID;
		}

		// Don't try to save the data under autosave, or future post (we do allow ajax, for quick edit).
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post->ID;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return $post->ID;
		}

		// Check permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] && ! current_user_can( 'edit_page', $post->ID ) || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $post->ID;
		}

		// Save (or delete) the value.
		if ( isset( $_POST[ $this->meta_field ] ) ) {
			update_post_meta( $post_id, $this->meta_field, sanitize_text_field( $_POST[ $this->meta_field ] ) );
		} else {
			delete_post_meta( $post_id, $this->meta_field );
		}

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
	 * @return array Amended post / page coulumns.
	 */
	public function columns( $columns ) {
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
	 * @param string $name Column name.
	 * @param int    $id Post ID.
	 * @return null Returns early if the column name is incorrect.
	 */
	public function custom_column( $name, $id ) {
		if ( 'visual-subtitle' !== $name ) {
			return;
		}

		echo '<span id="inline_' . esc_attr( $id ) . '_visual_subtitle">' . esc_html( get_post_meta( $id, $this->meta_field, true ) ) . '</span>';

	}

	/**
	 * Echo output into the Quick Edit feature.
	 *
	 * @since 1.0.0
	 *
	 * @global stdClass $post
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type   Post type.
	 * @return null Returns early if the column name is incorrect
	 */
	public function quick_edit( $column_name, $post_type ) {

		if ( 'visual-subtitle' !== $column_name ) {
			return;
		}

		global $post;

		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<?php wp_nonce_field( plugin_basename( __FILE__ ), $this->nonce ); ?>
				<label>
					<span class="title"><?php esc_html_e( 'Subtitle', 'visual-subtitle' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" id="post_subtitle" name="_visual-subtitle" value="<?php echo esc_attr( $this->get_subtitle() ); ?>" />
					</span>
				</label>
			</div>
		</fieldset>
		<?php

	}

	/**
	 * Filter the post title on the Posts screen, and on the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @global string $current_screen
	 *
	 * @param string  $title Post title.
	 * @param integer $id    Post ID.
	 * @return string
	 */
	public function filter( $title, $id ) {
		global $current_screen;

		$subtitle = $this->get_subtitle( $id );

		if ( ! $subtitle ) {
			return $title;
		}

		if ( is_admin() ) {
			// Can't use <small> here as the title is escaped on post type index
			// and added to the anchor title attribute. Sad panda.
			$title = $title . ' | ' . $subtitle;
		} else { // Front end.
			$title = $title . ' <small class="subtitle">' . $subtitle . '</small>';
		}

		return $title;
	}

	/**
	 * Filter the document title in the head.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title       Document title.
	 * @return string
	 */
	public function doctitle_filter( $title ) {
		$subtitle = $this->get_subtitle();

		if ( ! $subtitle || ! is_singular() ) {
			return $title;
		}

		$title = $title . apply_filters( 'visual_subtitle_doctitle_separator', ': ' ) . $subtitle;

		return $title;
	}

	/**
	 * Get subtitle for current global post.
	 * 
	 * @since 1.1.0
	 * 
	 * @global WP_Post $post Post object.
	 * 
	 * @param int $post_id Optional. Post ID. If not set, uses ID from current
	 *                     global $post.
	 * 
	 * @return string Subtitle value. Empty string if not set.
	 */
	protected function get_subtitle( $post_id = null ) {
		if ( is_null( $post_id ) ) {
			global $post;
			if ( ! $post ) {
				return '';
			}
			$post_id = $post->ID;
		}

		return get_post_meta( $post_id, $this->meta_field, true );
	}
	
	/**
	 * Check if visual subtitle is supported for a post type.
	 * 
	 * @since 1.1.0
	 * 
	 * @uses Visual_Subtitle::get_supported_types() Get list of post types that 
	 * visual subtitle should be used with.
	 * 
	 * @param string $post_type Post type.
	 * @return bool True is visual subtitle should be used, false if not.
	 */
	protected function has_support( $post_type ) {
		if ( in_array( $post_type, $this->get_supported_types() ) ) {
			return true;
		}

		return false;
	}
	
	/**
	 * Get list of post types that visual subtitle should be used with.
	 * 
	 * Default is to display subtitle UI for all post types that support title.
	 * 
	 * @todo If a settings UI is present and saved to options, this is used instead.
	 * 
	 * A filter is then applied, so developers can programatically set support.
	 * 
	 * @since 1.1.0
	 * 
	 * @return array Indexed array of post types.
	 */
	protected function get_supported_types() {
		$supported_types = array();
		$saved_settings  = get_option( 'visual-subtitle' );

		// Check for saved options.
		if ( $saved_settings && isset( $saved_settings['supported_types'] ) ) {
			$supported_types = $saved_settings['supported_types'];
		} else {
			// No options found, so default to post types that support title.
			foreach ( get_post_types() as $post_type ) {
				if ( post_type_supports( $post_type, 'title' ) ) {
					$supported_types[] = $post_type;
				}
			}
		}

		return apply_filters( 'visual_subtitle_supported_types', $supported_types );
	}
}
