<?php
/**
 * Plugin Name:       Onygo Admin Dark Mode
 * Plugin URI:        https://github.com/ma6/onygo.26
 * Description:       An accessible dark colour scheme for wp-admin, the login screen and the block editor. Colours only — no layout changes, no options page. Per-user Auto / Light / Dark, switchable from the toolbar and your profile. Every text/background pair meets WCAG 2.2 AA; control borders meet SC 1.4.11.
 * Version:           0.1.3
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Martin Gude
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       onygo-admin-dark
 * Update URI:        false
 *
 * Self-contained by design — no dependency on the Onygo theme or Neon, so the
 * whole `plugins/onygo-admin-dark/` folder can be lifted into its own
 * repository unchanged. wp-admin is not a Neon surface: Neon's token layer is
 * not loaded here and pulling it in would fight core's own reset. The palette
 * in assets/admin-dark.css is therefore raw hex, each value picked against a
 * calculated WCAG 2.2 contrast target — the figures are in readme.txt.
 *
 * @package OnygoAdminDark
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OAD_VERSION', '0.1.3' );
define( 'OAD_FILE', __FILE__ );

/**
 * The three valid preference values.
 *
 * @return string[]
 */
function oad_schemes() {
	return array( 'auto', 'light', 'dark' );
}

/**
 * Current user's colour-scheme preference: 'auto' | 'light' | 'dark'.
 * Falls back to 'auto' (follow the operating system) for logged-out
 * requests such as the login screen, and for anyone who never chose.
 *
 * @return string
 */
function oad_pref() {
	$stored = get_user_meta( get_current_user_id(), 'oad_scheme', true );
	return in_array( $stored, oad_schemes(), true ) ? $stored : 'auto';
}

/**
 * The stylesheet `media` attribute that switches the dark sheet on for the
 * current preference — 'all' forces it, a prefers-color-scheme query defers
 * the decision to the OS. Returns '' when the preference is 'light', i.e.
 * the sheet must not be enqueued at all.
 *
 * @return string
 */
function oad_media() {
	$pref = oad_pref();
	if ( 'light' === $pref ) {
		return '';
	}
	return 'dark' === $pref ? 'all' : '(prefers-color-scheme: dark)';
}

/**
 * Load the dark sheet for wp-admin and the login screen. No JavaScript, no
 * body class: the `media` attribute alone decides whether the browser
 * applies it, so there is no flash of the light theme and an OS change is
 * picked up live in 'auto'.
 */
function oad_enqueue() {
	$media = oad_media();
	if ( '' === $media ) {
		return;
	}
	$rel  = 'assets/admin-dark.css';
	$path = plugin_dir_path( OAD_FILE ) . $rel;

	wp_enqueue_style(
		'oad-admin-dark',
		plugins_url( $rel, OAD_FILE ),
		array(),
		file_exists( $path ) ? filemtime( $path ) : OAD_VERSION,
		$media
	);
}
add_action( 'admin_enqueue_scripts', 'oad_enqueue' );
add_action( 'login_enqueue_scripts', 'oad_enqueue' );

/**
 * The block-editor canvas is an iframe with its own document that never sees
 * an enqueued admin stylesheet, so its rules go in through the editor
 * settings instead. Wrapped in a prefers-color-scheme query for 'auto';
 * injected raw for 'dark' so it forces regardless of the OS.
 *
 * @param array $settings Block editor settings.
 * @return array
 */
function oad_editor_canvas( $settings ) {
	$pref = oad_pref();
	if ( 'light' === $pref ) {
		return $settings;
	}
	$path = plugin_dir_path( OAD_FILE ) . 'assets/editor-canvas-dark.css';
	if ( ! is_readable( $path ) ) {
		return $settings;
	}
	$css = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- bundled local asset, not a remote fetch.
	if ( false === $css ) {
		return $settings;
	}
	if ( 'auto' === $pref ) {
		$css = '@media (prefers-color-scheme: dark){' . $css . '}';
	}
	if ( empty( $settings['styles'] ) || ! is_array( $settings['styles'] ) ) {
		$settings['styles'] = array();
	}
	$settings['styles'][] = array( 'css' => $css );
	return $settings;
}
add_filter( 'block_editor_settings_all', 'oad_editor_canvas' );

/**
 * Toolbar switcher: a parent node showing the current scheme, with the three
 * choices as real links to a nonce-checked handler. No JavaScript — each
 * choice is a normal navigation that saves and returns you to where you were.
 *
 * @param WP_Admin_Bar $bar The toolbar.
 */
function oad_toolbar( $bar ) {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$current = oad_pref();
	$labels  = array(
		'auto'  => __( 'Auto (match system)', 'onygo-admin-dark' ),
		'light' => __( 'Light', 'onygo-admin-dark' ),
		'dark'  => __( 'Dark', 'onygo-admin-dark' ),
	);

	$bar->add_node(
		array(
			'id'     => 'oad',
			'parent' => 'top-secondary',
			'title'  => sprintf(
				/* translators: %s: name of the active admin colour scheme. */
				__( 'Appearance: %s', 'onygo-admin-dark' ),
				$labels[ $current ]
			),
			'meta'   => array( 'title' => __( 'Admin colour scheme', 'onygo-admin-dark' ) ),
		)
	);

	foreach ( $labels as $key => $label ) {
		$title = $label;
		if ( $key === $current ) {
			/* translators: %s: colour scheme name. */
			$title = sprintf( __( '%s (current)', 'onygo-admin-dark' ), $label );
		}
		$bar->add_node(
			array(
				'id'     => 'oad-' . $key,
				'parent' => 'oad',
				'title'  => $title,
				'href'   => wp_nonce_url(
					admin_url( 'admin-post.php?action=oad_set&scheme=' . $key ),
					'oad_set'
				),
				'meta'   => array( 'aria-current' => $key === $current ? 'true' : 'false' ),
			)
		);
	}
}
add_action( 'admin_bar_menu', 'oad_toolbar', 500 );

/**
 * Persist a choice made from the toolbar, then bounce back.
 */
function oad_handle_set() {
	check_admin_referer( 'oad_set' );

	$scheme = isset( $_GET['scheme'] ) ? sanitize_key( wp_unslash( $_GET['scheme'] ) ) : 'auto';
	if ( ! in_array( $scheme, oad_schemes(), true ) ) {
		$scheme = 'auto';
	}
	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), 'oad_scheme', $scheme );
	}

	$back = wp_get_referer();
	wp_safe_redirect( $back ? $back : admin_url() );
	exit;
}
add_action( 'admin_post_oad_set', 'oad_handle_set' );

/**
 * The same choice as a proper fieldset on your own profile screen — the
 * keyboard/AT-first control, and the one that works with JavaScript off.
 *
 * @param WP_User $user The user being edited (always the current user here).
 */
function oad_profile_field( $user ) {
	$stored  = get_user_meta( $user->ID, 'oad_scheme', true );
	$current = in_array( $stored, oad_schemes(), true ) ? $stored : 'auto';
	$labels  = array(
		'auto'  => __( 'Auto — follow my operating system', 'onygo-admin-dark' ),
		'light' => __( 'Light', 'onygo-admin-dark' ),
		'dark'  => __( 'Dark', 'onygo-admin-dark' ),
	);
	?>
	<h2><?php esc_html_e( 'Admin colour scheme', 'onygo-admin-dark' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'Colour scheme', 'onygo-admin-dark' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php esc_html_e( 'Admin colour scheme', 'onygo-admin-dark' ); ?></span>
					</legend>
					<?php foreach ( $labels as $key => $label ) : ?>
						<label>
							<input type="radio" name="oad_scheme" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $current ); ?> />
							<?php echo esc_html( $label ); ?>
						</label>
						<br />
					<?php endforeach; ?>
					<p class="description">
						<?php esc_html_e( 'Colours only. Nothing else about the admin changes.', 'onygo-admin-dark' ); ?>
					</p>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'oad_profile_field' );

/**
 * Save the profile choice. Core has already verified the update-user nonce
 * by the time this fires; the capability check guards against a filtered
 * hook firing for another user id.
 *
 * @param int $user_id The user being saved.
 */
function oad_profile_save( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	$scheme = isset( $_POST['oad_scheme'] ) ? sanitize_key( wp_unslash( $_POST['oad_scheme'] ) ) : 'auto';
	if ( ! in_array( $scheme, oad_schemes(), true ) ) {
		$scheme = 'auto';
	}
	update_user_meta( $user_id, 'oad_scheme', $scheme );
}
add_action( 'personal_options_update', 'oad_profile_save' );
