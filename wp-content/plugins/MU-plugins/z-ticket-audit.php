<?php
/**
 * Plugin Name: Ticket CPT Audit
 * Description: Logs every register_post_type() call and flags duplicate/competing registrations for slug "ticket".
 */

if (!defined('ABSPATH')) exit;

$GLOBALS['__ticket_cpt_audit'] = $GLOBALS['__ticket_cpt_audit'] ?? [];

add_action('registered_post_type', function ($post_type, $args) {
    if (!is_admin()) return;

    // Build a trimmed backtrace to find the offending file(s)
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $files = [];
    foreach ($trace as $t) {
        if (!empty($t['file'])) {
            // Ignore core files to surface theme/plugins
            if (strpos($t['file'], WP_CONTENT_DIR) !== false) {
                $files[] = str_replace(ABSPATH, '', $t['file']);
            }
        }
    }
    $files = array_values(array_unique($files));

    $GLOBALS['__ticket_cpt_audit'][$post_type][] = [
        'labels' => isset($args->labels->name) ? $args->labels->name : '',
        'public' => $args->public ?? null,
        'show_ui' => $args->show_ui ?? null,
        'rewrite' => $args->rewrite ?? null,
        'supports' => $args->supports ?? null,
        'menu_position' => $args->menu_position ?? null,
        'from_files' => $files,
        'hook_priority' => did_action('init'), // rough indicator of when it ran
    ];
}, 999, 2);

// Admin screen to view results
add_action('admin_menu', function () {
    add_management_page('Ticket CPT Audit', 'Ticket CPT Audit', 'manage_options', 'ticket-cpt-audit', function () {
        $audit = $GLOBALS['__ticket_cpt_audit'] ?? [];
        echo '<div class="wrap"><h1>Ticket CPT Audit</h1>';
        if (!$audit) { echo '<p>No post types registered yet. Visit any admin page again.</p></div>'; return; }

        foreach ($audit as $pt => $regs) {
            echo "<h2>post_type: <code>{$pt}</code></h2>";
            if (count($regs) > 1) {
                echo '<p style="color:#b32d2e;font-weight:bold;">⚠ Detected multiple registrations for this post type. Last one wins and can override args.</p>';
            }
            foreach ($regs as $i => $r) {
                echo '<div style="border:1px solid #ddd;padding:12px;margin:12px 0;background:#fff;">';
                echo '<p><strong>Registration #'.($i+1).'</strong></p>';
                echo '<p><strong>Labels:</strong> '.esc_html($r['labels']).'</p>';
                echo '<p><strong>Public:</strong> '.var_export($r['public'], true).'</p>';
                echo '<p><strong>Show UI:</strong> '.var_export($r['show_ui'], true).'</p>';
                echo '<p><strong>Rewrite:</strong> <code>'.esc_html(print_r($r['rewrite'], true)).'</code></p>';
                echo '<p><strong>Supports:</strong> <code>'.esc_html(print_r($r['supports'], true)).'</code></p>';
                echo '<p><strong>Menu Position:</strong> '.esc_html((string)$r['menu_position']).'</p>';
                if (!empty($r['from_files'])) {
                    echo '<p><strong>Likely source files:</strong><br><code>'.implode('</code><br><code>', array_map('esc_html', $r['from_files'])).'</code></p>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    });
});
add_action('registered_taxonomy', function ($taxonomy, $object_type, $args) {
  if (!is_admin()) return;
  $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
  $files = [];
  foreach ($trace as $t) {
    if (!empty($t['file']) && strpos($t['file'], WP_CONTENT_DIR) !== false) {
      $files[] = str_replace(ABSPATH, '', $t['file']);
    }
  }
  $files = array_values(array_unique($files));
  $GLOBALS['__tax_audit'][$taxonomy][] = [
    'object_type' => $object_type,
    'public'      => $args['public'] ?? null,
    'show_in_rest'=> $args['show_in_rest'] ?? null,
    'rewrite'     => $args['rewrite'] ?? null,
    'from_files'  => $files,
  ];
}, 999, 3);

add_action('admin_menu', function () {
  add_management_page('Taxonomy Audit', 'Taxonomy Audit', 'manage_options', 'taxonomy-audit', function () {
    $audit = $GLOBALS['__tax_audit'] ?? [];
    echo '<div class="wrap"><h1>Taxonomy Audit</h1>';
    if (!$audit) { echo '<p>No taxonomies registered yet.</p></div>'; return; }
    foreach ($audit as $tax => $regs) {
      echo "<h2>taxonomy: <code>{$tax}</code></h2>";
      if (count($regs) > 1) echo '<p style="color:#b32d2e;font-weight:bold;">⚠ Multiple registrations detected.</p>';
      foreach ($regs as $i => $r) {
        echo '<div style="border:1px solid #ddd;padding:12px;margin:12px 0;background:#fff;">';
        echo '<p><strong>Registration #'.($i+1).'</strong></p>';
        echo '<p><strong>Object Types:</strong> <code>'.esc_html(implode(',', (array)$r['object_type'])).'</code></p>';
        echo '<p><strong>Public:</strong> '.var_export($r['public'], true).'</p>';
        echo '<p><strong>Show in REST:</strong> '.var_export($r['show_in_rest'], true).'</p>';
        echo '<p><strong>Rewrite:</strong> <code>'.esc_html(print_r($r['rewrite'], true)).'</code></p>';
        if (!empty($r['from_files'])) {
          echo '<p><strong>Likely source files:</strong><br><code>'.implode('</code><br><code>', array_map('esc_html',$r['from_files'])).'</code></p>';
        }
        echo '</div>';
      }
    }
    echo '</div>';
  });
});
