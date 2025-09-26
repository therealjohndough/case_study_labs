<?php
// Bootstrap WP and run skyworld CSV importer
$base = __DIR__;
$wp_load = $base . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) {
    echo "wp-load.php not found at $wp_load\n";
    exit(1);
}
require_once $wp_load;

$importer = get_stylesheet_directory() . '/inc/importer.php';
// get_stylesheet_directory may not be available yet in a non-theme context; derive path directly
$theme_importer = __DIR__ . '/wp-content/themes/skyworld-wp-child/inc/importer.php';
if ( file_exists( $theme_importer ) ) {
    require_once $theme_importer;
} else {
    echo "Importer not found at $theme_importer\n";
    exit(1);
}

$csv = __DIR__ . '/skyworld-import.csv';
if ( ! file_exists( $csv ) ) {
    echo "CSV not found at $csv\n";
    exit(1);
}

if ( ! function_exists( 'skyworld_import_products_csv' ) ) {
    echo "Importer function not available.\n";
    exit(1);
}

$res = skyworld_import_products_csv( $csv );
if ( empty( $res['success'] ) ) {
    echo "Import failed: " . ( $res['message'] ?? 'unknown' ) . "\n";
    exit(1);
}

echo "Imported rows: " . (int) $res['rows'] . "\n";
echo "Created: " . (int) $res['created'] . "\n";
echo "Updated: " . (int) $res['updated'] . "\n";

return 0;
