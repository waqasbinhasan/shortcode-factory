<?php
/*
 * File: /core/blocks/blocks.php
 *
 * Blocks registration 
 */

/**
 * @since 2.7
 */
function scf_enqueue_block_scripts() {
    if ( ! function_exists( 'register_block_type' ) ) {
		// Gutenberg is not active.
		return;
    }

    $block_js = 'scf-toolbar-control.min.js';
    wp_enqueue_script(
		'advanced-rich-text-tools',
		SCF_BLOCKSJS_URL.'/'.$block_js,
		array(
			'wp-element',
			'wp-rich-text',
			'wp-i18n',
			'wp-editor',
		),
		filemtime(SCF_BLOCKSJS.'/'.$block_js),
		true
    );
    
    $block_js = 'scf-inspector-control.min.js';
    wp_enqueue_script(
		'custom-control',
		SCF_BLOCKSJS_URL.'/'.$block_js,
		array(
			'wp-element',
			'wp-editor',
			'wp-compose',
            'wp-components',
            'wp-i18n',
		),
		filemtime(SCF_BLOCKSJS.'/'.$block_js),
		true
	);
}

add_action('admin_enqueue_scripts', 'scf_enqueue_block_scripts');
