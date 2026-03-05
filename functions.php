<?php
/**
 * Sheet Music Database – functions.php
 */

/* ==================================================
   THEME SETUP
   ================================================== */
add_action('after_setup_theme', function () {

    // Titel i <title>
    add_theme_support('title-tag');

    // Logo i Customizer
    add_theme_support('custom-logo', [
        'height'      => 120,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // Menu-support
    register_nav_menus([
        'primary' => 'Hovedmenu',
    ]);
});






/* ==================================================
   ENQUEUE STYLES & SCRIPTS
   ================================================== */
add_action('wp_enqueue_scripts', function () {

    // Tema CSS
    wp_enqueue_style(
        'sheetmusic-db-style',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get('Version')
    );

    // Font Awesome
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        [],
        '6.5.1'
    );

    // Dashicons (WordPress ikoner)
    wp_enqueue_style('dashicons');

    // Multitrack test JS
    wp_enqueue_script(
        'cc-multitrack-test',
        get_stylesheet_directory_uri() . '/js/multitrack-test.js',
        [],
        '0.1',
        true
    );
});


/* ==================================================
   CUSTOMIZER – ACCENT FARVE
   ================================================== */
add_action('customize_register', function ($wp_customize) {

    $wp_customize->add_setting('cc_accent_color', [
        'default'   => '#8b1d3d',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'cc_accent_color',
            [
                'label'   => 'Accentfarve',
                'section' => 'colors',
            ]
        )
    );
});

// Inject accent color som CSS-variabel
add_action('wp_enqueue_scripts', function () {
    $accent = get_theme_mod('cc_accent_color', '#8b1d3d');
    wp_add_inline_style(
        'sheetmusic-db-style',
        ":root { --cc-accent: {$accent}; }"
    );
});


/* ==================================================
   RELATIVE DATOER
   ================================================== */
function cc_relative_modified_date() {
    $modified = get_the_modified_time('U');
    $now = current_time('U');
    $days = floor(($now - $modified) / DAY_IN_SECONDS);

    if ($days < 1) {
        return 'i dag';
    } elseif ($days === 1) {
        return 'i går';
    } elseif ($days <= 3) {
        return 'for ' . $days . ' dage siden';
    } else {
        return get_the_modified_date('j. F Y');
    }
}

function cc_relative_modified_archive() {
    $modified = get_the_modified_time('U');
    $now = current_time('U');
    $days = floor(($now - $modified) / DAY_IN_SECONDS);

    if ($days < 1) {
        return 'i dag';
    } elseif ($days === 1) {
        return 'i går';
    } else {
        return get_the_modified_date('d.m.Y');
    }
}


/* ==================================================
   FILE UPLOADS – SIBELIUS
   ================================================== */
add_filter('upload_mimes', function ($mimes) {
    $mimes['sib'] = 'application/octet-stream';
    return $mimes;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    if (str_ends_with($filename, '.sib')) {
        return [
            'ext'  => 'sib',
            'type' => 'application/octet-stream',
            'proper_filename' => $filename,
        ];
    }
    return $data;
}, 10, 4);


/* ==================================================
   HELPERS
   ================================================== */
function cc_get_file_url($field) {
    if (!$field) return '';
    if (is_array($field) && isset($field['url'])) return $field['url'];
    if (is_numeric($field)) return wp_get_attachment_url($field);
    if (is_string($field)) return $field;
    return '';
}
