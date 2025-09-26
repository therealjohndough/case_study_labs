<?php
$csv = __DIR__ . '/../skyworld-import.csv';
$out = __DIR__ . '/../skyworld-import.xml';
if ( ! file_exists( $csv ) ) {
    echo "CSV not found: $csv\n";
    exit(1);
}
$fh = fopen( $csv, 'r' );
$headers = fgetcsv( $fh );
$rows = [];
while ( ( $r = fgetcsv( $fh ) ) !== false ) {
    $row = [];
    foreach ( $headers as $i => $h ) {
        $row[ trim( strtolower( preg_replace('/[^a-z0-9]+/','_', $h) ) ) ] = $r[$i] ?? '';
    }
    $rows[] = $row;
}
fclose( $fh );

$now = date('D, d M Y H:i:s O');
$xml = [];
$xml[] = '<?xml version="1.0" encoding="UTF-8" ?>';
$xml[] = '<rss version="2.0" xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:wp="http://wordpress.org/export/1.2/">';
$xml[] = '<channel>';
$xml[] = '<title>Skyworld Import</title>';
$xml[] = '<link>http://example.local</link>';
$xml[] = '<description>Skyworld import</description>';
$xml[] = "<pubDate>$now</pubDate>";
$xml[] = '<language>en-US</language>';
$xml[] = '<wp:wxr_version>1.2</wp:wxr_version>';
$xml[] = '<wp:base_site_url>http://example.local</wp:base_site_url>';
$xml[] = '<wp:base_blog_url>http://example.local</wp:base_blog_url>';

$post_id = 1000;
foreach ( $rows as $r ) {
    $post_id++;
    $strain_name = $r['strain_name'] ?? $r['strain'] ?? '';
    $slug = strtolower( preg_replace('/[^a-z0-9]+/','-', $strain_name ) );
    $xml[] = '<item>';
    $xml[] = "<title>" . htmlspecialchars( $strain_name ) . "</title>";
    $xml[] = "<link>http://example.local/strain/$slug</link>";
    $xml[] = "<pubDate>$now</pubDate>";
    $xml[] = '<dc:creator>admin</dc:creator>';
    $xml[] = "<guid isPermaLink=\"false\">http://example.local/?post_type=strain&amp;p=$post_id</guid>";
    $xml[] = '<description></description>';
    $xml[] = '<content:encoded><![CDATA[]]></content:encoded>';
    $xml[] = '<excerpt:encoded><![CDATA[]]></excerpt:encoded>';
    $xml[] = "<wp:post_id>$post_id</wp:post_id>";
    $xml[] = "<wp:post_date>2025-09-25 00:00:00</wp:post_date>";
    $xml[] = "<wp:post_date_gmt>2025-09-25 00:00:00</wp:post_date_gmt>";
    $xml[] = '<wp:comment_status>closed</wp:comment_status>';
    $xml[] = '<wp:ping_status>closed</wp:ping_status>';
    $xml[] = "<wp:post_name>$slug</wp:post_name>";
    $xml[] = '<wp:status>publish</wp:status>';
    $xml[] = '<wp:post_parent>0</wp:post_parent>';
    $xml[] = '<wp:menu_order>0</wp:menu_order>';
    $xml[] = '<wp:post_type>strain</wp:post_type>';
    $xml[] = '<wp:post_password></wp:post_password>';
    $xml[] = '<wp:is_sticky>0</wp:is_sticky>';

    // meta
    $meta_keys = [ 'genetics', 'thc', 'terp_total', 'terp_1', 'terp_1_data', 'terp_2', 'terp_2_data', 'terp_3', 'terp_3_data', 'effects', 'nose', 'flavor', '@image' ];
    foreach ( $meta_keys as $k ) {
        $val = $r[$k] ?? '';
        if ( $val !== '' ) {
            $xml[] = '<wp:postmeta>';
            $xml[] = "<wp:meta_key>$k</wp:meta_key>";
            $xml[] = '<wp:meta_value><![CDATA[' . $val . ']]></wp:meta_value>';
            $xml[] = '</wp:postmeta>';
        }
    }

    $xml[] = '</item>';
}

// Products
$post_id = 2000;
foreach ( $rows as $r ) {
    $post_id++;
    $strain_name = $r['strain_name'] ?? $r['strain'] ?? '';
    $batch = $r['batch_lot_#'] ?? $r['batch/lot_#'] ?? $r['batch/lot_'] ?? $r['batch'] ?? $r['batch/lot'] ?? $r['batch_lot'] ?? $r['batch/lot_#_'] ?? $r['batch/lot_#'] ?? '';
    $title = trim( $strain_name ) . ' — ' . trim( $batch );
    $slug = strtolower( preg_replace('/[^a-z0-9]+/','-', $title ) );
    $xml[] = '<item>';
    $xml[] = "<title>" . htmlspecialchars( $title ) . "</title>";
    $xml[] = "<link>http://example.local/product/$slug</link>";
    $xml[] = "<pubDate>$now</pubDate>";
    $xml[] = '<dc:creator>admin</dc:creator>';
    $xml[] = "<guid isPermaLink=\"false\">http://example.local/?post_type=sky_product&amp;p=$post_id</guid>";
    $xml[] = "<wp:post_id>$post_id</wp:post_id>";
    $xml[] = "<wp:post_date>2025-09-25 00:00:00</wp:post_date>";
    $xml[] = "<wp:post_date_gmt>2025-09-25 00:00:00</wp:post_date_gmt>";
    $xml[] = '<wp:post_type>sky_product</wp:post_type>';

    // product meta
    $meta_map = [ 'batch_number' => $batch, 'thc_percent' => rtrim( $r['thc'] ?? '', '%'), 'pert_total' => $r['terp_total'] ?? '', 'weight' => '', 'image_path' => $r['@image'] ?? '' ];
    foreach ( $meta_map as $mk => $mv ) {
        if ( $mv !== '' ) {
            $xml[] = '<wp:postmeta>';
            $xml[] = "<wp:meta_key>$mk</wp:meta_key>";
            $xml[] = '<wp:meta_value><![CDATA[' . $mv . ']]></wp:meta_value>';
            $xml[] = '</wp:postmeta>';
        }
    }

    // link to strain by title (we don't know exact post IDs on import, but admin can link after import). We'll add strain name in meta.
    if ( $strain_name ) {
        $xml[] = '<wp:postmeta>';
        $xml[] = '<wp:meta_key>related_strain_name</wp:meta_key>';
        $xml[] = '<wp:meta_value><![CDATA[' . $strain_name . ']]></wp:meta_value>';
        $xml[] = '</wp:postmeta>';
    }

    $xml[] = '</item>';
}

$xml[] = '</channel>';
$xml[] = '</rss>';

file_put_contents( $out, implode("\n", $xml) );

echo "WXR written to: $out\n";
