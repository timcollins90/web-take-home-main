<?php
/**
 * Plugin Name:       Widget Company CPT
 * Plugin URI:        https://example.com/
 * Description:       Manages a directory of widget companies and their data.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wcd
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the "Company" Custom Post Type.
 */
function wcd_register_company_cpt() {

    $labels = [
        'name'                  => _x( 'Companies', 'Post Type General Name', 'wcd' ),
        'singular_name'         => _x( 'Company', 'Post Type Singular Name', 'wcd' ),
        'menu_name'             => __( 'Companies', 'wcd' ),
        'name_admin_bar'        => __( 'Company', 'wcd' ),
        'archives'              => __( 'Company Archives', 'wcd' ),
        'attributes'            => __( 'Company Attributes', 'wcd' ),
        'parent_item_colon'     => __( 'Parent Company:', 'wcd' ),
        'all_items'             => __( 'All Companies', 'wcd' ),
        'add_new_item'          => __( 'Add New Company', 'wcd' ),
        'add_new'               => __( 'Add New', 'wcd' ),
        'new_item'              => __( 'New Company', 'wcd' ),
        'edit_item'             => __( 'Edit Company', 'wcd' ),
        'update_item'           => __( 'Update Company', 'wcd' ),
        'view_item'             => __( 'View Company', 'wcd' ),
        'view_items'            => __( 'View Companies', 'wcd' ),
        'search_items'          => __( 'Search Company', 'wcd' ),
        'not_found'             => __( 'Not found', 'wcd' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'wcd' ),
        'featured_image'        => __( 'Company Logo', 'wcd' ),
        'set_featured_image'    => __( 'Set company logo', 'wcd' ),
        'remove_featured_image' => __( 'Remove company logo', 'wcd' ),
        'use_featured_image'    => __( 'Use as company logo', 'wcd' ),
        'insert_into_item'      => __( 'Insert into company', 'wcd' ),
        'uploaded_to_this_item' => __( 'Uploaded to this company', 'wcd' ),
        'items_list'            => __( 'Companies list', 'wcd' ),
        'items_list_navigation' => __( 'Companies list navigation', 'wcd' ),
        'filter_items_list'     => __( 'Filter companies list', 'wcd' ),
    ];
    $args   = [
        'label'               => __( 'Company', 'wcd' ),
        'description'         => __( 'A directory of widget companies', 'wcd' ),
        'labels'              => $labels,
        'supports'            => [ 'title', 'editor', 'thumbnail' ], // title = Name, editor = Summary, thumbnail = Logo
        'taxonomies'          => [],
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-building',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true, // CRITICAL: Allows CPT to be added to Appearance > Menus
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true, // Enable Gutenberg editor and REST API
    ];
    register_post_type( 'company', $args );

}
add_action( 'init', 'wcd_register_company_cpt', 0 );


/**
 * Add the Meta Box for Company Details.
 */
function wcd_add_company_meta_box() {
    add_meta_box(
        'wcd_company_details_meta_box', // ID
        __( 'Company Details', 'wcd' ), // Title
        'wcd_render_company_meta_box',  // Callback function
        'company',                      // Post type
        'normal',                       // Context ('normal', 'side')
        'high'                          // Priority ('high', 'low')
    );
}
add_action( 'add_meta_boxes', 'wcd_add_company_meta_box' );

/**
 * Render the HTML for the Company Details Meta Box.
 *
 * @param WP_Post $post The post object.
 */
function wcd_render_company_meta_box( $post ) {
    // Add a nonce field for security
    wp_nonce_field( 'wcd_save_company_meta', 'wcd_company_meta_nonce' );

    // Get existing meta values
    $rating      = get_post_meta( $post->ID, '_company_rating', true );
    $free_trial  = get_post_meta( $post->ID, '_company_has_free_trial', true );
    $benefits_arr = get_post_meta( $post->ID, '_company_benefits', true );
    $cons_arr     = get_post_meta( $post->ID, '_company_cons', true );

    // Format arrays for textareas (one item per line)
    $benefits_str = is_array($benefits_arr) ? implode( "\n", $benefits_arr ) : '';
    $cons_str     = is_array($cons_arr) ? implode( "\n", $cons_arr ) : '';

    // Meta Box HTML
    ?>
    <style>
        .wcd-meta-box-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        .wcd-meta-box-field { display: flex; flex-direction: column; }
        .wcd-meta-box-field label { font-weight: 600; margin-bottom: 5px; }
        .wcd-meta-box-field input[type="number"], .wcd-meta-box-field textarea { width: 100%; max-width: 600px; }
        .wcd-meta-box-field textarea { min-height: 100px; }
        .wcd-meta-box-field p.description { margin-top: 5px; color: #666; }
        .wcd-meta-box-field-checkbox { flex-direction: row; align-items: center; gap: 10px; }
    </style>

    <div class="wcd-meta-box-grid">

        <!-- Rating -->
        <div class="wcd-meta-box-field">
            <label for="wcd_company_rating"><?php _e( 'Rating (1-10)', 'wcd' ); ?></label>
            <input type="number" id="wcd_company_rating" name="wcd_company_rating" min="1" max="10"
                   value="<?php echo esc_attr( $rating ); ?>">
        </div>

        <!-- Has Free Trial -->
        <div class="wcd-meta-box-field wcd-meta-box-field-checkbox">
            <input type="checkbox" id="wcd_company_has_free_trial" name="wcd_company_has_free_trial" value="1" <?php checked( $free_trial, '1' ); ?>>
            <label for="wcd_company_has_free_trial"><?php _e( 'Has Free Trial', 'wcd' ); ?></label>
        </div>

        <!-- Benefits -->
        <div class="wcd-meta-box-field">
            <label for="wcd_company_benefits"><?php _e( 'Benefits', 'wcd' ); ?></label>
            <textarea id="wcd_company_benefits" name="wcd_company_benefits" rows="4"><?php echo esc_textarea( $benefits_str ); ?></textarea>
            <p class="description"><?php _e( 'Enter each benefit on a new line.', 'wcd' ); ?></p>
        </div>

        <!-- Cons -->
        <div class="wcd-meta-box-field">
            <label for="wcd_company_cons"><?php _e( 'Cons', 'wcd' ); ?></label>
            <textarea id="wcd_company_cons" name="wcd_company_cons" rows="4"><?php echo esc_textarea( $cons_str ); ?></textarea>
            <p class="description"><?php _e( 'Enter each con on a new line.', 'wcd' ); ?></p>
        </div>

    </div>
    <?php
}

/**
 * Save the meta box data when the post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wcd_save_company_meta( $post_id ) {
    // --- Security Checks ---

    // Check if our nonce is set.
    if ( ! isset( $_POST['wcd_company_meta_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['wcd_company_meta_nonce'], 'wcd_save_company_meta' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check if the post type is 'company'.
    if ( 'company' !== get_post_type( $post_id ) ) {
        return;
    }

    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // --- Process and Save Data ---

    // Rating (Integer)
    if ( isset( $_POST['wcd_company_rating'] ) ) {
        $rating = absint( $_POST['wcd_company_rating'] ); // Ensures it's a positive integer
        if ( $rating < 1 ) $rating = 1;
        if ( $rating > 10 ) $rating = 10;
        update_post_meta( $post_id, '_company_rating', $rating );
    }

    // Has Free Trial (Boolean)
    // If the box is checked, '1' is sent. If not, nothing is sent.
    $free_trial = isset( $_POST['wcd_company_has_free_trial'] ) ? '1' : '0';
    update_post_meta( $post_id, '_company_has_free_trial', $free_trial );

    // Benefits (Array)
    if ( isset( $_POST['wcd_company_benefits'] ) ) {
        $benefits_str = sanitize_textarea_field( $_POST['wcd_company_benefits'] );
        $benefits_arr = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $benefits_str ) ) );
        update_post_meta( $post_id, '_company_benefits', $benefits_arr ); // WordPress auto-serializes the array
    }

    // Cons (Array)
    if ( isset( $_POST['wcd_company_cons'] ) ) {
        $cons_str = sanitize_textarea_field( $_POST['wcd_company_cons'] );
        $cons_arr = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $cons_str ) ) );
        update_post_meta( $post_id, '_company_cons', $cons_arr ); // WordPress auto-serializes the array
    }
}
add_action( 'save_post', 'wcd_save_company_meta' );
