<?php
namespace CustomBookingManager;

class FormManager {
	public function init() {
		add_action( 'init', [ $this, 'register_form_cpt' ] );
		add_filter( 'manage_cbm_form_posts_columns', [ $this, 'set_custom_columns' ] );
		add_action( 'manage_cbm_form_posts_custom_column', [ $this, 'custom_column_data' ], 10, 2 );
		add_action( 'add_meta_boxes', [ $this, 'add_builder_meta_box' ] );
		add_action( 'save_post', [ $this, 'save_builder_meta_box' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
	}

	public function register_form_cpt() {
		$labels = [
			'name'               => 'Booking Forms',
			'singular_name'      => 'Booking Form',
			'menu_name'          => 'Form Builder',
			'name_admin_bar'     => 'Booking Form',
			'add_new'            => 'Add New Form',
			'add_new_item'       => 'Add New Booking Form',
			'new_item'           => 'New Form',
			'edit_item'          => 'Edit Form',
			'view_item'          => 'View Form',
			'all_items'          => 'Form Builder',
			'search_items'       => 'Search Forms',
			'not_found'          => 'No forms found.',
		];

		$args = [
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'cbm-bookings', // Show under main Bookings menu
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => [ 'title' ], // We only need the title
		];

		register_post_type( 'cbm_form', $args );
	}

	public function set_custom_columns( $columns ) {
		$columns['shortcode'] = 'Shortcode';
		return $columns;
	}

	public function custom_column_data( $column, $post_id ) {
		if ( $column === 'shortcode' ) {
			echo '<code>[cbm_form id="' . esc_attr( $post_id ) . '"]</code>';
		}
	}

	public function add_builder_meta_box() {
		add_meta_box(
			'cbm_form_builder_box',
			'Form Builder',
			[ $this, 'render_builder_meta_box' ],
			'cbm_form',
			'normal',
			'high'
		);
	}

	public function render_builder_meta_box( $post ) {
		wp_nonce_field( 'cbm_save_form_builder', 'cbm_form_builder_nonce' );
		$schema = get_post_meta( $post->ID, '_cbm_form_schema', true );
		include CBM_PLUGIN_DIR . 'admin/views/builder-view.php';
	}

	public function save_builder_meta_box( $post_id ) {
		if ( ! isset( $_POST['cbm_form_builder_nonce'] ) || ! wp_verify_nonce( $_POST['cbm_form_builder_nonce'], 'cbm_save_form_builder' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['cbm_form_schema'] ) ) {
			$schema = sanitize_text_field( wp_unslash( $_POST['cbm_form_schema'] ) );
			update_post_meta( $post_id, '_cbm_form_schema', $schema );
		}
	}

	public function enqueue_admin_scripts( $hook ) {
		global $post_type;
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( 'cbm_form' === $post_type ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'cbm-form-builder-js', CBM_PLUGIN_URL . 'admin/js/form-builder.js', ['jquery', 'jquery-ui-sortable'], CBM_VERSION, true );
			}
		}
	}
}
