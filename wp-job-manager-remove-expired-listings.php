<?php
/**
 * Plugin Name: WP Job Manager - Auto Delete Expired Listings
 * Plugin URI: https://github.com/rokkitpress/wp-job-manager-remove-expired-listings
 * Description: Lets you set a specific number of days to automatically delete listings from the database.
 * Version: 0.2
 * Author: Craig Watson
 * Author URI: https://github.com/rokkitpress
 * Requires at least: 4.1
 * Tested up to: 4.6
 * Text Domain: auto-delete-experied-listings
 * Domain Path: /languages/
 * License: GPL2+
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if WP Job Manager is active
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'wp-job-manager/wp-job-manager.php' ) ) {

	//* Create admin submenu page under joblistings.
	add_action('admin_menu', 'wpjm_adel_menu');
	function wpjm_adel_menu() {
		add_submenu_page(
		  'edit.php?post_type=job_listing',
			'Auto Delete Expired Job Listings',
		  'Auto Delete Expired Job Listings',
			'administrator',
			'wpjm-adel-settings',
			'wpjm_adel_settings_page'
		);
	}

	//* Callback function to build settings page content.
	function wpjm_adel_settings_page() {
		?>

			<div class="wrap">
			<h1>Auto Delete Job Listings</h1>
			<p>By default WP Job Manager does not delete expired jobs from the server.</p>
			<p>By setting a specificnumber of days bellow, the job listings will run for the set listings duration and then contiue to be stored in the users account for the set number of days. Once that is up the listings will be permenantly deleted from the database.</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'wpjm-adel-settings-group' ); ?>
				<?php do_settings_sections( 'wpjm-adel-settings-group' ); ?>
				<table class="form-table">
						<tr valign="top">
						<th scope="row">Delete after this many days.</th>
						<td><input type="number" name="wpjm_adel_num_days" value="<?php echo esc_attr( get_option('wpjm_adel_num_days') ); ?>" /></td>
						</tr>
				</table>

				<?php submit_button(); ?>

			</form>
			</div>

		<?php
	}

	//* Register the settings.
	add_action( 'admin_init', 'wpjm_adel_settings' );
	function wpjm_adel_settings() {
		register_setting( 'wpjm-adel-settings-group', 'wpjm_adel_num_days' );
	}

	//* This will make sure expired jobs are deleted after XX days.
	add_filter( 'job_manager_delete_expired_jobs', '__return_true' );

	//* The default is 30 days, but you can change this with the following.
	add_filter( 'job_manager_delete_expired_jobs_days', 'change_job_manager_delete_expired_jobs_days' );
	function change_job_manager_delete_expired_jobs_days() {

		$wpjm_adel_num_days = esc_attr( get_option( 'wpjm_adel_num_days' ) );

	  return $wpjm_adel_num_days;

	}

} else {

	function wpjm_adel_admin_notice_error() {
		$class = 'notice notice-error';
		$message = __( 'Ooops! <strong>WP Job Manager - Auto Delete Expired Listings</strong> Can Only Be Activated Once The Core WP Job Manager Plugin Is Active!', 'auto-delete-experied-listings' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
	add_action( 'admin_notices', 'wpjm_adel_admin_notice_error' );

}
