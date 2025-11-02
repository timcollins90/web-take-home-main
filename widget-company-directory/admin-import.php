<?php
/**
 * Plugin Name:       Widget Company Import (Admin Page)
 * Description:       Adds an admin page under "Tools" to import companies from data/companies.json.
 * Version:           1.0.0
 * Author:            Your Name
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add the admin menu page under "Tools".
 */
function wcd_add_import_admin_page() {
    add_submenu_page(
        'tools.php',                             // Parent slug (Tools)
        __( 'Import Companies', 'wcd-import' ),    // Page title
        __( 'Import Companies', 'wcd-import' ),    // Menu title
        'manage_options',                        // Capability
        'wcd-company-import',                    // Menu slug
        'wcd_render_import_page'                 // Callback function
    );
}
add_action( 'admin_menu', 'wcd_add_import_admin_page' );

/**
 * Render the HTML for the import admin page.
 */
function wcd_render_import_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p><?php _e( 'Click the button below to import companies from the <code>data/companies_json.json</code> file in the plugin directory.', 'wcd-import' ); ?></p>
        <p><?php _e( 'This process will check for existing companies by title and skip any duplicates.', 'wcd-import' ); ?></p>

        <!-- Display admin notices (success/error messages) -->
        <?php settings_errors( 'wcd_import_notices' ); ?>

        <form method="POST">
            <!-- Add a nonce for security -->
            <?php wp_nonce_field( 'wcd_import_nonce', 'wcd_import_nonce_field' ); ?>
            
            <input type="hidden" name="wcd_start_import" value="1">
            
            <?php
            // Show the import button
            submit_button(
                __( 'Start Import', 'wcd-import' ),
                'primary',
                'wcd-import-submit'
            );
            ?>
        </form>
    </div>
    <?php
}

/**
 * Handle the import action when the form is submitted.
 * Hooked to 'admin_init' to run before headers are sent.
 */
function wcd_handle_import_action() {
    
    // Check if our form was submitted
    if ( ! isset( $_POST['wcd_start_import'] ) ) {
        return;
    }

    // Check if the current user has permission
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have permission to perform this action.', 'wcd-import' ) );
    }

    // Verify the nonce
    if ( ! isset( $_POST['wcd_import_nonce_field'] ) || ! wp_verify_nonce( $_POST['wcd_import_nonce_field'], 'wcd_import_nonce' ) ) {
        wp_die( __( 'Security check failed. Please try again.', 'wcd-import' ) );
    }

    // Run the import logic
    $results = wcd_run_import_logic();

    // Store the results in "settings errors" to be displayed as admin notices
    if ( $results['imported'] > 0 || $results['skipped'] > 0 ) {
        add_settings_error(
            'wcd_import_notices',
            'import_success',
            sprintf(
                __( 'Import complete! %d companies imported, %d companies skipped (already exist).', 'wcd-import' ),
                $results['imported'],
                $results['skipped']
            ),
            'success'
        );
    } else {
        add_settings_error(
            'wcd_import_notices',
            'import_nothing',
            __( 'No new companies were imported.', 'wcd-import' ),
            'warning'
        );
    }

    // Add any errors that occurred
    foreach ( $results['errors'] as $error ) {
        add_settings_error(
            'wcd_import_notices',
            'import_error',
            $error,
            'error'
        );
    }

    // Persist the notices to be shown after the redirect
    set_transient( 'settings_errors', get_settings_errors(), 30 );

    // Redirect back to the import page to prevent re-submission on refresh
    wp_safe_redirect( admin_url( 'tools.php?page=wcd-company-import' ) );
    exit;
}
add_action( 'admin_init', 'wcd_handle_import_action' );


/**
 * The core import logic.
 * Reads the JSON file and creates posts.
 *
 * @return array Results of the import.
 */
function wcd_run_import_logic() {
    $results = [
        'imported' => 0,
        'skipped'  => 0,
        'errors'   => [],
    ];

    $json_file = plugin_dir_path( __FILE__ ) . 'data/companies_data.json';

    if ( ! file_exists( $json_file ) ) {
        $results['errors'][] = __( 'Error: <code>data/companies_data.json</code> file not found.', 'wcd-import' );
        return $results;
    }

    $json_data = file_get_contents( $json_file );
    $companies = json_decode( $json_data, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        $results['errors'][] = __( 'Error: Invalid JSON file. Could not parse data.', 'wcd-import' );
        return $results;
    }

    if ( empty( $companies ) ) {
        $results['errors'][] = __( 'Warning: JSON file is empty or contains no companies.', 'wcd-import' );
        return $results;
    }

    foreach ( $companies as $company ) {
        if ( empty( $company['name'] ) ) {
            $results['errors'][] = __( 'Skipped an entry with no name.', 'wcd-import' );
            continue;
        }
        
        $name = $company['name'];

        // Check if a company with this title already exists
        $existing_post = get_page_by_title( $name, OBJECT, 'company' );

        if ( $existing_post ) {
            $results['skipped']++;
            continue;
        }

        // --- Create New Company Post ---
        $post_data = [
            'post_title'   => wp_strip_all_tags( $name ),
            'post_content' => wp_kses_post( $company['summary'] ?? '' ),
            'post_type'    => 'company',
            'post_status'  => 'publish',
        ];

        $post_id = wp_insert_post( $post_data, true );

        if ( is_wp_error( $post_id ) ) {
            $results['errors'][] = sprintf(
                __( 'Failed to insert "%s". Error: %s', 'wcd-import' ),
                $name,
                $post_id->get_error_message()
            );
            continue;
        }

        // --- Save Custom Meta Fields ---

        // Rating
        if ( isset( $company['rating'] ) ) {
            update_post_meta( $post_id, '_company_rating', absint( $company['rating'] ) );
        }

        // Has Free Trial (Boolean, saved as '1' or '0')
        $free_trial = ! empty( $company['has_free_trial'] ) ? '1' : '0';
        update_post_meta( $post_id, '_company_has_free_trial', $free_trial );

        // Benefits (Array)
        if ( ! empty( $company['benefits'] ) && is_array( $company['benefits'] ) ) {
            update_post_meta( $post_id, '_company_benefits', $company['benefits'] );
        }

        // Cons (Array)
        if ( ! empty( $company['cons'] ) && is_array( $company['cons'] ) ) {
            update_post_meta( $post_id, '_company_cons', $company['cons'] );
        }

        $results['imported']++;
    }

    return $results;
}
