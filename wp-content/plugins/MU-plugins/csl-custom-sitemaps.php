<?php
/**
 * Plugin Name:         Case Study Labs - Custom Sitemaps
 * Description:         Lightweight XML sitemap index + per-type sitemaps (pages, posts, case_studies), authors, and selected taxonomy sitemaps. Paged, cached, noindex-aware.
 * Version:             1.5.0
 * Author:              Case Study Labs
 * Text Domain:         csl-sitemaps
 */

if (!defined('ABSPATH')) exit;

class CSL_Custom_Sitemaps {
  /** Config **/
  const URLS_PER_FILE = 2000;                 // stay well under 50k
  const CACHE_TTL     = 12 * HOUR_IN_SECONDS; // 12h

  // Post types (filterable). Ensure CPT slug matches your site.
  private $post_types = ['page', 'post', 'case_studies'];

  // Only include THESE public taxonomies (filterable).
  // Edit this list to your SEO-relevant taxonomies.
  private $taxonomies = ['category', 'post_tag', 'case_study_category'];

  // Force-include URLs (filterable) — belt & suspenders
  private $extra_urls = [
    'https://casestudy-labs.com/',
    'https://casestudy-labs.com/studio/',
    'https://casestudy-labs.com/contact/',
    'https://casestudy-labs.com/news/',
    'https://casestudy-labs.com/brand-analysis-quiz/',
    'https://casestudy-labs.com/services/',
    'https://casestudy-labs.com/services/strategy/',
    'https://casestudy-labs.com/services/branding-production/',
    'https://casestudy-labs.com/services/web-design/',
    'https://casestudy-labs.com/services/content-social/',
    'https://casestudy-labs.com/services/media-buying/',
    'https://casestudy-labs.com/services/lifecycle-marketing/',
    'https://casestudy-labs.com/work/',
    'https://casestudy-labs.com/case-studies/',
    'https://casestudy-labs.com/about/john-dough-dangelo/',
    'https://casestudy-labs.com/author/jdough/',
    'https://casestudy-labs.com/privacy-policy/',
  ];

  public function __construct() {
    // Filters for you (or code) to override
    $this->post_types = apply_filters('csl_sitemap_post_types', $this->post_types);
    $this->taxonomies = apply_filters('csl_sitemap_taxonomies', $this->taxonomies);
    $this->extra_urls = array_unique(array_map(function($u){ return rtrim($u, '/').'/'; }, apply_filters('csl_sitemap_extra_urls', $this->extra_urls)));

    add_action('init',                [$this, 'add_rewrite_rules']);
    add_filter('query_vars',          [$this, 'add_query_vars']);
    add_action('template_redirect',   [$this, 'maybe_render']);

    // cache invalidation: posts
    add_action('save_post',           [$this, 'flush_cache']);
    add_action('delete_post',         [$this, 'flush_cache']);
    add_action('transition_post_status', [$this, 'flush_on_status_change'], 10, 3);

    // cache invalidation: terms
    add_action('created_term',        [$this, 'flush_cache'], 10, 3);
    add_action('edited_term',         [$this, 'flush_cache'], 10, 3);
    add_action('delete_term',         [$this, 'flush_cache'], 10, 3);
  }

  /** ---- Rewrites ---- */
  public function add_rewrite_rules() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?csl_sitemap=index', 'top');
    add_rewrite_rule('^sitemap-authors-([0-9]+)\.xml$', 'index.php?csl_sitemap=authors&csl_page=$matches[1]', 'top');
    add_rewrite_rule('^sitemap-([a-z0-9_-]+)-([0-9]+)\.xml$', 'index.php?csl_sitemap=$matches[1]&csl_page=$matches[2]', 'top'); // post types
    add_rewrite_rule('^sitemap-taxonomy-([a-z0-9_-]+)-([0-9]+)\.xml$', 'index.php?csl_tax=$matches[1]&csl_page=$matches[2]', 'top'); // taxonomies
  }

  public function add_query_vars($vars) {
    $vars[] = 'csl_sitemap';
    $vars[] = 'csl_page';
    $vars[] = 'csl_tax';
    return $vars;
  }

  /** ---- Controller ---- */
  public function maybe_render() {
    $which = get_query_var('csl_sitemap');
    $tax   = get_query_var('csl_tax');
    if (!$which && !$tax) return;

    nocache_headers();
    header('Content-Type: application/xml; charset=UTF-8');
    header('X-Robots-Tag: all');
    header('Cache-Control: max-age=3600, must-revalidate');

    // Index
    if ($which === 'index') {
      echo $this->xml_header() . $this->render_index();
      exit;
    }

    $page = max(1, (int) get_query_var('csl_page'));

    // Authors
    if ($which === 'authors') {
      echo $this->xml_header() . $this->render_authors($page);
      exit;
    }

    // Taxonomies (only if allowed)
    if ($tax) {
      $tax = sanitize_key($tax);
      if (!in_array($tax, $this->public_allowed_taxonomies(), true)) {
        $this->send_404('Unknown taxonomy.');
      }
      echo $this->xml_header() . $this->render_taxonomy($tax, $page);
      exit;
    }

    // Post types
    $type = sanitize_key($which);
    if (!in_array($type, $this->public_queryable_types(), true)) {
      $this->send_404('Unknown sitemap type.');
    }
    echo $this->xml_header() . $this->render_post_type($type, $page);
    exit;
  }

  /** ---- Helpers ---- */
  private function xml_header(): string { return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; }

  private function public_queryable_types(): array {
    return array_values(array_filter($this->post_types, function($pt){
      $o = get_post_type_object($pt);
      return $o && !empty($o->public) && !empty($o->publicly_queryable);
    }));
  }

  private function public_allowed_taxonomies(): array {
    return array_values(array_filter($this->taxonomies, function($tx){
      $o = get_taxonomy($tx);
      return $o && !empty($o->public);
    }));
  }

  private function lastmod_site(): string {
    global $wpdb;
    $types = $this->public_queryable_types();
    if (empty($types)) return gmdate('c');
    $in = implode("','", array_map('esc_sql', $types));
    $date = $wpdb->get_var("
      SELECT post_modified_gmt
      FROM {$wpdb->posts}
      WHERE post_status='publish' AND post_type IN('$in')
      ORDER BY post_modified_gmt DESC LIMIT 1
    ");
    return $date ? gmdate('c', strtotime($date)) : gmdate('c');
  }

  private function send_404(string $msg) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    echo '<error xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.esc_html($msg).'</error>';
    exit;
  }

  private function is_noindex(int $post_id): bool {
    if (get_post_meta($post_id, '_csl_noindex', true)) return true;

    $yoast = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);
    if ((string)$yoast === '1') return true;

    $rm = get_post_meta($post_id, 'rank_math_robots', true);
    if ($rm) {
      if (is_array($rm) && in_array('noindex', $rm, true)) return true;
      if (is_string($rm) && stripos($rm, 'noindex') !== false) return true;
    }
    return false;
  }

  /** ---- Index ---- */
  private function render_index(): string {
    $base = rtrim(get_home_url(), '/');
    $lastmod = $this->lastmod_site();

    $cache_key = 'csl_sitemap_index';
    if ($out = get_transient($cache_key)) return $out;

    $xml  = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Post-type sitemaps (paged)
    foreach ($this->public_queryable_types() as $type) {
      $pages = $this->pagecount_for_type($type);
      if ($pages < 1) continue;
      for ($p = 1; $p <= $pages; $p++) {
        $xml .= "  <sitemap>\n";
        $xml .= "    <loc>".esc_url("$base/sitemap-$type-$p.xml")."</loc>\n";
        $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
        $xml .= "  </sitemap>\n";
      }
    }

    // Authors
    if ($this->authors_count() > 0) {
      $xml .= "  <sitemap>\n";
      $xml .= "    <loc>".esc_url("$base/sitemap-authors-1.xml")."</loc>\n";
      $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
      $xml .= "  </sitemap>\n";
    }

    // Selected Taxonomies
    foreach ($this->public_allowed_taxonomies() as $tax) {
      $pages = $this->pagecount_for_tax($tax);
      if ($pages < 1) continue;
      for ($p = 1; $p <= $pages; $p++) {
        $xml .= "  <sitemap>\n";
        $xml .= "    <loc>".esc_url("$base/sitemap-taxonomy-$tax-$p.xml")."</loc>\n";
        $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
        $xml .= "  </sitemap>\n";
      }
    }

    $xml .= "</sitemapindex>";

    set_transient($cache_key, $xml, self::CACHE_TTL);
    return $xml;
  }

  private function pagecount_for_type(string $type): int {
    $counts = (array) wp_count_posts($type);
    $total  = isset($counts['publish']) ? (int) $counts['publish'] : 0;
    return (int) ceil(max(0, $total) / self::URLS_PER_FILE);
  }

  private function pagecount_for_tax(string $taxonomy): int {
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true, 'fields' => 'ids']);
    if (is_wp_error($terms) || empty($terms)) return 0;
    return (int) ceil(count($terms) / self::URLS_PER_FILE);
  }

  /** ---- Post Type Sitemaps ---- */
  private function render_post_type(string $type, int $page): string {
    $cache_key = "csl_sitemap_type_{$type}_{$page}";
    if ($out = get_transient($cache_key)) return $out;

    $offset = ($page - 1) * self::URLS_PER_FILE;

    $q = new WP_Query([
      'post_type'      => $type,
      'post_status'    => 'publish',
      'posts_per_page' => self::URLS_PER_FILE,
      'offset'         => $offset,
      'orderby'        => 'modified',
      'order'          => 'DESC',
      'no_found_rows'  => true,
      'fields'         => 'ids',
    ]);

    $urls = [];
    if ($q->have_posts()) {
      foreach ($q->posts as $post_id) {
        if ($this->is_noindex($post_id)) continue;
        $loc = esc_url(get_permalink($post_id));
        if (!$loc) continue;
        $urls[] = [
          'loc'     => $loc,
          'lastmod' => get_post_modified_time('c', true, $post_id),
        ];
      }
    }

    // Append extras to pages sitemap (dedup)
    if ($type === 'page' && !empty($this->extra_urls)) {
      $seen = [];
      foreach ($urls as $u) { $seen[$u['loc']] = true; }
      foreach ($this->extra_urls as $extra) {
        if (!isset($seen[$extra])) {
          $urls[] = ['loc' => esc_url($extra), 'lastmod' => gmdate('c')];
          $seen[$extra] = true;
        }
      }
    }

    if (empty($urls)) $this->send_404('No entries for this page.');

    $xml  = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $u) {
      $xml .= "  <url>\n";
      $xml .= "    <loc>{$u['loc']}</loc>\n";
      if (!empty($u['lastmod'])) $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
      $xml .= "  </url>\n";
    }
    $xml .= "</urlset>";

    set_transient($cache_key, $xml, self::CACHE_TTL);
    return $xml;
  }

  /** ---- Authors ---- */
  private function authors_ids(): array {
    global $wpdb;
    $ids = $wpdb->get_col("
      SELECT DISTINCT post_author
      FROM {$wpdb->posts}
      WHERE post_status='publish' AND post_type='post'
    ");
    return array_map('intval', $ids ?: []);
  }

  private function authors_count(): int { return count($this->authors_ids()); }

  private function render_authors(int $page): string {
    if ($page !== 1) $this->send_404('No entries for this page.');

    $cache_key = 'csl_sitemap_authors';
    if ($out = get_transient($cache_key)) return $out;

    $ids = $this->authors_ids();
    if (empty($ids)) $this->send_404('No authors with published posts.');

    $users = get_users(['include' => $ids, 'fields' => ['ID','user_nicename']]);

    $xml  = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($users as $u) {
      $loc = esc_url(get_author_posts_url($u->ID, $u->user_nicename));
      $last = get_posts([
        'author'      => $u->ID,
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => 1,
        'orderby'     => 'modified',
        'order'       => 'DESC',
        'fields'      => 'ids',
      ]);
      $lastmod = !empty($last) ? get_post_modified_time('c', true, $last[0]) : gmdate('c');

      $xml .= "  <url>\n";
      $xml .= "    <loc>{$loc}</loc>\n";
      $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
      $xml .= "  </url>\n";
    }
    $xml .= "</urlset>";

    set_transient($cache_key, $xml, self::CACHE_TTL);
    return $xml;
  }

  /** ---- Taxonomies (selected) ---- */
  private function render_taxonomy(string $taxonomy, int $page): string {
    $cache_key = "csl_sitemap_tax_{$taxonomy}_{$page}";
    if ($out = get_transient($cache_key)) return $out;

    $offset = ($page - 1) * self::URLS_PER_FILE;

    $terms = get_terms([
      'taxonomy'   => $taxonomy,
      'hide_empty' => true,
      'fields'     => 'ids',
      'number'     => self::URLS_PER_FILE,
      'offset'     => $offset,
    ]);

    if (is_wp_error($terms) || empty($terms)) $this->send_404('No entries for this page.');

    $xml  = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($terms as $term_id) {
      $link = get_term_link((int)$term_id, $taxonomy);
      if (is_wp_error($link)) continue;
      $xml .= "  <url>\n";
      $xml .= "    <loc>".esc_url($link)."</loc>\n";
      // Optional: compute a smarter lastmod by latest post in term (expensive). Skipped for speed.
      $xml .= "  </url>\n";
    }
    $xml .= "</urlset>";

    set_transient($cache_key, $xml, self::CACHE_TTL);
    return $xml;
  }

  /** ---- Cache ---- */
  public function flush_cache() {
    delete_transient('csl_sitemap_index');
    delete_transient('csl_sitemap_authors');

    // nuke cached post-type pages
    global $wpdb;
    foreach ($this->public_queryable_types() as $type) {
      $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like("_transient_csl_sitemap_type_{$type}_").'%'
      ));
      $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like("_transient_timeout_csl_sitemap_type_{$type}_").'%'
      ));
    }

    // nuke cached taxonomy pages
    foreach ($this->public_allowed_taxonomies() as $tax) {
      $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like("_transient_csl_sitemap_tax_{$tax}_").'%'
      ));
      $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like("_transient_timeout_csl_sitemap_tax_{$tax}_").'%'
      ));
    }
  }

  public function flush_on_status_change($new, $old, $post) {
    if (in_array($post->post_type, $this->public_queryable_types(), true) && ($new === 'publish' || $old === 'publish')) {
      $this->flush_cache();
    }
  }
}

new CSL_Custom_Sitemaps();

/** Activation/Deactivation */
register_activation_hook(__FILE__, function () {
  (new CSL_Custom_Sitemaps())->add_rewrite_rules();
  flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function () {
  flush_rewrite_rules();
});
