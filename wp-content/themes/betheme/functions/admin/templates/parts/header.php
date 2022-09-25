<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
?>

<?php if( WHITE_LABEL ): ?>
	<h1><?php esc_html_e( 'Welcome to WordPress', 'mfn-opts' ); ?></h1>
<?php else: ?>
	<h1><?php esc_html_e( 'Welcome to '.apply_filters('betheme_label', 'Betheme'), 'mfn-opts' ); ?></h1>
<?php endif; ?>

<?php if( ! trim( apply_filters( 'betheme_dashboard_below_welcome', '' ) ) ) : ?>
	<p class="about-text">
		<?php if( mfn_is_registered() ): ?>

			<?php esc_html_e( 'Your copy of theme is registered.', 'mfn-opts' ); ?><br />
			<?php echo sprintf( __( 'Discover <a href="%s">pre-built websites</a>, premium <a href="%s">bundled plugins</a>, auto updates and much more.', 'mfn-opts' ), 'admin.php?page=be-websites', 'admin.php?page=be-plugins' ); ?>

		<?php else: ?>

			Please register this version of theme to get access to pre-built websites,<br />bundled premium plugins and auto updates.

		<?php endif; ?>
	</p>
<?php else: ?>
	<?php echo '<p class="about-text">'.stripslashes_deep( apply_filters('betheme_dashboard_below_welcome', '') ).'</p>'; ?>
<?php endif; ?>

<div class="logo">

	<?php
		$logo = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 139.99 96.27"><defs><style>.cls-1{fill:url(#linear-gradient);}.cls-2{fill:url(#linear-gradient-2);}</style><linearGradient id="linear-gradient" x1="24.89" y1="55.2" x2="97.14" y2="-43.96" gradientUnits="userSpaceOnUse"><stop offset="0.09" stop-color="#0089f7"/><stop offset="0.92" stop-color="#0dc7ff"/></linearGradient><linearGradient id="linear-gradient-2" x1="84" y1="91.61" x2="155.58" y2="15.55" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#0089f7"/><stop offset="1" stop-color="#0dc7ff"/></linearGradient></defs><path class="cls-1" d="M55.29,47a32.17,32.17,0,0,0,7.38-3.7,21.26,21.26,0,0,0,5-4.74,18.31,18.31,0,0,0,2.88-5.58,20.92,20.92,0,0,0,.91-6.18,27.53,27.53,0,0,0-2-10.65A19.68,19.68,0,0,0,63.13,8a32,32,0,0,0-11.3-5.16A66.64,66.64,0,0,0,35,1H0V96.22H38A48.7,48.7,0,0,0,53.26,94,32.38,32.38,0,0,0,64.47,88a25.82,25.82,0,0,0,6.9-9.11,27.24,27.24,0,0,0,2.35-11.31q0-8-4.51-13.17T55.29,47ZM22.09,17.53H35a30.93,30.93,0,0,1,6.44.59,11.24,11.24,0,0,1,4.48,2,8.22,8.22,0,0,1,2.61,3.63,16.32,16.32,0,0,1,.85,5.65,13.52,13.52,0,0,1-1.08,5.76,8.37,8.37,0,0,1-3.14,3.59,13.7,13.7,0,0,1-5,1.83,37.76,37.76,0,0,1-6.6.52H22.09ZM51.31,71.58a9.79,9.79,0,0,1-2.16,4,11.56,11.56,0,0,1-4.35,2.88,19.51,19.51,0,0,1-7.15,1.11H22.09V55.83H37.51a25.41,25.41,0,0,1,6.93.79,11.42,11.42,0,0,1,4.41,2.22,7.73,7.73,0,0,1,2.36,3.53,15.24,15.24,0,0,1,.68,4.7A18.05,18.05,0,0,1,51.31,71.58Z" transform="translate(0 -1)"/><path class="cls-2" d="M137.67,65.64a2.75,2.75,0,0,0,1.41-1.18,5.83,5.83,0,0,0,.72-2.29,28.76,28.76,0,0,0,.19-3.72,36,36,0,0,0-2.25-13.11,27.91,27.91,0,0,0-6.28-9.77,26.83,26.83,0,0,0-9.6-6.07,37.46,37.46,0,0,0-26.28.58A31.34,31.34,0,0,0,78.39,48a36,36,0,0,0-2.32,12.88,42.42,42.42,0,0,0,2.72,15.81,32.54,32.54,0,0,0,7.38,11.37A30.84,30.84,0,0,0,97.09,95a38.17,38.17,0,0,0,13.36,2.32,58.71,58.71,0,0,0,7.29-.49A40.66,40.66,0,0,0,125.39,95a35.08,35.08,0,0,0,7.45-3.53,30.08,30.08,0,0,0,6.76-5.85l-5.88-7.25a4.18,4.18,0,0,0-3.66-1.76,7.57,7.57,0,0,0-3.5.85q-1.66.84-3.66,1.89a30.72,30.72,0,0,1-4.54,1.9,19.54,19.54,0,0,1-6.08.84,15.33,15.33,0,0,1-10.78-3.82Q97.32,74.46,96.33,66h39.09A6.79,6.79,0,0,0,137.67,65.64ZM96.53,54.2c.7-4.14,2.13-7.31,4.32-9.51s5.2-3.3,9.08-3.3a12.59,12.59,0,0,1,5.55,1.11,10.3,10.3,0,0,1,3.7,2.91,11.48,11.48,0,0,1,2,4.08,18.12,18.12,0,0,1,.62,4.71Z" transform="translate(0 -1)"/></svg>';

		echo apply_filters('betheme_logo', $logo);

		$version = apply_filters('betheme_disable_theme_version', MFN_THEME_VERSION);

		if( $version ){
			echo '<span class="version">'. MFN_THEME_VERSION .'</span>';
		}
	?>

	<?php if( mfn_is_registered() && version_compare( $this->version, MFN_THEME_VERSION, '>' )): ?>

		<a href="update-core.php" class="button"><?php esc_html_e( 'Update to', 'mfn-opts' ); ?> <?php echo esc_html( $this->version ); ?></a>

	<?php endif; ?>

</div>

<?php
	settings_errors('betheme_registration');
	if( ! empty($this->error) ){
		echo '<div id="setting-error-registration_error" class="error inline mfn-dashboard-error settings-error notice">';
			echo '<p><strong>'. esc_html($this->error) .'</strong></p>';
		echo '</div>';
	}
?>

<?php
	$current_screen = get_current_screen();
	$current_screen = $current_screen->base;

	$is_support_disabled = apply_filters('betheme_disable_support', false);
	$is_changelog_disabled = apply_filters('betheme_disable_changelog', false);
?>

<?php do_action('mfn-admin-before-tabs'); ?>

<h2 class="nav-tab-wrapper wp-clearfix">
	<a href="admin.php?page=<?php echo apply_filters('betheme_dynamic_slug', 'betheme'); ?>" class="nav-tab<?php if( $current_screen == 'toplevel_page_'. apply_filters('betheme_dynamic_slug', 'betheme') ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Welcome', 'mfn-opts' ); ?></a>

	<?php if( ! mfn_is_hosted() ): ?>
		<a href="admin.php?page=<?php echo apply_filters('betheme_slug', 'be'); ?>-status" class="nav-tab<?php if( $current_screen == apply_filters('betheme_label', 'betheme').'_page_'. apply_filters('betheme_slug', 'be') .'-status' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'System status', 'mfn-opts' ); ?></a>
	<?php endif; ?>

	<?php if( ! $is_support_disabled ): ?>
		<a href="admin.php?page=<?php echo apply_filters('betheme_slug', 'be'); ?>-support" class="nav-tab<?php if( $current_screen == apply_filters('betheme_label', 'betheme').'_page_'. apply_filters('betheme_slug', 'be') .'-support' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Manual & Support', 'mfn-opts' ); ?></a>
	<?php endif; ?>

	<?php if( ! $is_changelog_disabled ): ?>
		<a href="admin.php?page=<?php echo apply_filters('betheme_slug', 'be'); ?>-changelog" class="nav-tab<?php if( $current_screen == apply_filters('betheme_label', 'betheme').'_page_'. apply_filters('betheme_slug', 'be') .'-changelog' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Changelog', 'mfn-opts' ); ?></a>
	<?php endif; ?>
	<a href="admin.php?page=be-tools" class="nav-tab<?php if( $current_screen == 'betheme_page_be-tools' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Tools', 'mfn-opts' ); ?></a>
</h2>
