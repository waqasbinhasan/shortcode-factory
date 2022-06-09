<?php
/*
 * File: /core/functions.php
 *
 * Core functions and handling
 */

function scf_enqueue_scripts() {
	wp_enqueue_style(SCF_SHORTNAME.'-colorbox', SCF_CSSURL.'/colorbox.css', null, SCF_VERSION);
	wp_enqueue_style(SCF_SHORTNAME.'-core', SCF_CSSURL.'/core.css', null, SCF_VERSION);

	wp_enqueue_script('jquery');
	wp_enqueue_script(SCF_SHORTNAME.'-colorbox', SCF_JSURL . '/jquery.colorbox-min.js', array('jquery'), null, true);
	wp_enqueue_script(SCF_SHORTNAME.'-isotope', SCF_JSURL . '/isotope.pkgd.min.js', array('jquery'), null, true);
	wp_enqueue_script(SCF_SHORTNAME.'-core', SCF_JSURL . '/core.js', array('jquery'), '1.0.0', true);

	$current_screen = get_current_screen();
	$screen_id = $current_screen->id;

	wp_localize_script(
		SCF_SHORTNAME.'-core',
		SCF_INITIALS.'ajax',
		array(
			'url' => admin_url('admin-ajax.php' ),
			'tag' => SCF_FULLNAME,
			'i' => SCF_INITIALS,
			'help' => ' - <a href="http://shortcodefactory.com/users-guide/" target="_blank">User Guide</a>',
			'screen_id' => $screen_id
		)
	);
}
add_action('admin_enqueue_scripts', 'scf_enqueue_scripts');

/*
 * Registers all short codes provided in the $shortcodesArray register.
 *
 * $shortcodesArray must comply with the standards as mentioned in /core/shortcodes.php
 */
function scf_register_shortcodes($shortcodesArray) {
	foreach($shortcodesArray as $group => $shortcodes) {
		foreach($shortcodes as $shortcode) {
			if (function_exists($shortcode[1])) {
				add_shortcode($shortcode[0], $shortcode[1]);
			}
		}
	}
}

function scf_register_builtin_shortcodes() {
	global $scf_builtin_shortcodes;

	scf_register_shortcodes($scf_builtin_shortcodes);
}

/*
 * Register custom button(s) for WP Editor
 */
function scf_custom_buttons($context) {
	$options = get_option('scf_options');
	$button_appearance = esc_attr($options["general"]["button_appearance"]);

	$img = SCF_IMGURL . '/icon-scfactory.png';
	$title = SCF_FULLNAME;
	$context .= "<a title='{$title}' class='button' id='".SCF_INITIALS."InsertShortcode' href='#'>";

	$icon = "<img src='{$img}' /> ";
	$text = "$title";

	switch($button_appearance) {
		case "icon":
			$context .= $icon;
			break;

		case "text":
			$context .= $text;
			break;

		default:
			$context .= $icon.$text;
			break;
	}

	$context .= "</a>";

  return $context;
}
add_action('media_buttons_context',  'scf_custom_buttons');

/*
 * Loads main UI for short codes
 */
function scf_load_shortcodes_ui() {
	global $scf_builtin_shortcodes, $scf_shortcode_groups;

	include(SCF_UI."/header.php");
	include(SCF_UI."/main.php");
	include(SCF_UI."/footer.php");
	wp_die();
}
add_action( 'wp_ajax_scf_load_shortcodes_ui', 'scf_load_shortcodes_ui' );

/*
 * Loads UI for Selected Short Code (if any)
 */
function scf_load_shortcode_ui() {
	include(SCF_UI."/header.php");

	$ui = sanitize_file_name($_REQUEST['ui']);

	if(
		//isset($_REQUEST["ui"]) && 
		!empty($ui) && 
		check_ajax_referer('scf-ajax-shortcode-ui', 'security', false) &&
		!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' &&
		scf_is_valid_shortcode($ui)
	) {
		include(SCF_UI."/".$ui.".php");
	} else {
		echo "Invalid request.";
	}

	include(SCF_UI."/footer.php");
	wp_die();
}
add_action( 'wp_ajax_scf_load_shortcode_ui', 'scf_load_shortcode_ui');

/**
 * Short code support in Text Widget
 *
 * @since 2.5
 */
$options = get_option('scf_options');
$txtwid_support = isset($options["general"]["txtwid_support"])?esc_attr($options["general"]["txtwid_support"]):"";

if(!empty($txtwid_support)) {
	add_filter('widget_text', 'do_shortcode');
}

/**
 * Enqueue scripts and styles on front-end
 *
 * @since 2.5
 */
function scf_frontend_enqueue_scripts() {
	$options = get_option('scf_options');
	$bootstrap_support = isset($options["general"]["bootstrap_support"])?esc_attr($options["general"]["bootstrap_support"]):"N";

	if("Y" == $bootstrap_support) {
		// CSS
		wp_enqueue_style(SCF_SHORTNAME.'-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', null, '3.3.7');

		// JS
		wp_enqueue_script(SCF_SHORTNAME.'-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', null, '3.3.7', true);
	}

}
add_action('wp_enqueue_scripts', 'scf_frontend_enqueue_scripts');

/**
 * Checks if the requested short code is valid and registered.
 * This is also used in AJAX requests, while loading Shortcode UI,
 * to eliminate local file inclusion (LFI) vulnerabilities.
 * 
 * @see https://www.pluginvulnerabilities.com/2019/01/14/our-proactive-monitoring-caught-an-authenticated-local-file-inclusion-lfi-vulnerability-in-shortcode-factory/
 * @since 2.8
 */
function scf_is_valid_shortcode($shortcode) {
	global $scf_builtin_shortcodes;

	foreach($scf_builtin_shortcodes as $group => $shortcodes) {
		if(key_exists($shortcode, $shortcodes)) {
			return true;
		}
	}

	return false;
}

/**
 * Register templates post type
 *
 * @since 2.5
 */
include(SCF_TEMPLATES."/scf-templates-cpt.php");

/**
 * Gutenberg blocks
 * 
 * @since 2.7
 */
include(SCF_BLOCKS."/blocks.php");
