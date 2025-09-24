<?php
/**
 * Plugin Name: CSL Tickets
 * Description: Design request CPT + ACF fields + validation + admin UX for Case Study Labs.
 * Version: 1.0.1
 * Author: Case Study Labs
 */

if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------
 * 0) Hard guard: normalize CPT args even if another plugin
 *    re-registers later. You are the source of truth.
 * ----------------------------------------------------- */
add_filter('register_post_type_args', function ($args, $post_type) {

  // TICKETS — private back-office type
  if ($post_type === 'ticket') {
    $args['public']              = false;       // not publicly queryable
    $args['publicly_queryable']  = false;
    $args['exclude_from_search'] = true;
    $args['show_ui']             = true;
    $args['show_in_rest']        = true;        // keep block editor/REST
    $args['rewrite']             = false;       // no public URLs
    $args['map_meta_cap']        = true;
    $args['capability_type']     = 'post';
    // Ensure expected editor features
    $args['supports']            = ['title','editor'];
    $args['menu_icon']           = 'dashicons-clipboard';
  }

  // INQUIRIES — also private
  if ($post_type === 'inquiry') {
    $args['public']              = false;
    $args['publicly_queryable']  = false;
    $args['exclude_from_search'] = true;
    $args['show_ui']             = true;
    $args['show_in_rest']        = true;
    $args['rewrite']             = false;
    $args['map_meta_cap']        = true;
    $args['capability_type']     = 'post';
    $args['supports']            = ['title'];
    $args['menu_icon']           = 'dashicons-businesswoman';
    $args['menu_position']       = 21;
  }

  return $args;
}, 20, 2);

/* -------------------------------------------------------
 * 1) CPT: ticket  (guard against duplicates)
 * ----------------------------------------------------- */
add_action('init', function () {
  if (post_type_exists('ticket')) return; // someone else registered it — our filter above will still normalize
  register_post_type('ticket', [
    'labels' => [
      'name'          => 'Tickets',
      'singular_name' => 'Ticket',
      'add_new_item'  => 'Add New Ticket',
      'edit_item'     => 'Edit Ticket',
      'view_item'     => 'View Ticket',
      'search_items'  => 'Search Tickets',
    ],
    'public'              => false,
    'publicly_queryable'  => false,
    'exclude_from_search' => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_rest'        => true,
    'rewrite'             => false,
    'map_meta_cap'        => true,
    'capability_type'     => 'post',
    'supports'            => ['title','editor'],
    'menu_icon'           => 'dashicons-clipboard',
  ]);
}, 9);

/* -------------------------------------------------------
 * 2) ACF Field Group: Design Request (local JSON in code)
 * ----------------------------------------------------- */
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key' => 'group_csl_design_request',
    'title' => 'Design Request',
    'fields' => [
      // Request Type
      [
        'key' => 'field_csl_request_type',
        'label' => 'Request Type',
        'name'  => 'request_type',
        'type'  => 'radio',
        'choices' => [
          'new_concept' => 'New Concept',
          'revision'    => 'Revision',
          'resize'      => 'Resize/Versioning',
          'copy_update' => 'Copy Update',
          'export'      => 'Asset Export',
          'bug_fix'     => 'Bug/Fix',
          'other'       => 'Other',
        ],
        'layout' => 'horizontal',
        'return_format' => 'value',
        'required' => 1,
      ],
      // Deliverable Type
      [
        'key' => 'field_csl_deliverable_type',
        'label' => 'Deliverable Type',
        'name'  => 'deliverable_type',
        'type'  => 'checkbox',
        'choices' => [
          'social_post'      => 'Social Media Post',
          'social_story'     => 'Social Story',
          'social_reel'      => 'Social Reel / Short Video',
          'display_ad'       => 'Display/Web Banner',
          'email'            => 'Email',
          'landing_page'     => 'Landing Page / Web Page',
          'presentation'     => 'Presentation/Deck',
          'print_collateral' => 'Print Collateral',
          'packaging'        => 'Packaging',
          'ooh'              => 'OOH / Poster',
          'presskit'         => 'Press Kit / Media Assets',
          'asset_export'     => 'Final Asset Export (Specs Only)',
          'other'            => 'Other',
        ],
        'layout' => 'horizontal',
        'return_format' => 'value',
      ],
      // Service Category
      [
        'key' => 'field_csl_service_category',
        'label' => 'Service Category',
        'name'  => 'service_category',
        'type'  => 'checkbox',
        'choices' => [
          'copywriting'    => 'Copywriting',
          'graphic_design' => 'Graphic Design',
          'motion'         => 'Motion/Animation',
          'photo'          => 'Photography',
          'video'          => 'Video Editing',
          'web_design'     => 'Web Design (UI/UX)',
          'web_dev'        => 'Web Development',
          'email_build'    => 'Email Build (HTML)',
          'strategy'       => 'Strategy/Concept',
          'qa'             => 'QA / Bug Fix',
          'other'          => 'Other',
        ],
        'layout' => 'horizontal',
        'return_format' => 'value',
      ],
      // Destination
      [
        'key' => 'field_csl_destination',
        'label' => 'Destination / Placement',
        'name'  => 'destination',
        'type'  => 'checkbox',
        'choices' => [
          'print'        => 'Print',
          'web'          => 'Web',
          'email'        => 'Email',
          'social'       => 'Social',
          'ads'          => 'Ads',
          'ooh'          => 'OOH',
          'packaging'    => 'Packaging',
          'presentation' => 'Presentation',
          'internal'     => 'Internal',
          'other'        => 'Other',
        ],
        'layout' => 'horizontal',
      ],
      // Priority
      [
        'key' => 'field_csl_priority',
        'label' => 'Priority',
        'name'  => 'priority',
        'type'  => 'radio',
        'choices' => [
          'p0' => 'P0—Critical (today)',
          'p1' => 'P1—High (48h)',
          'p2' => 'P2—Medium (1–2 weeks)',
          'p3' => 'P3—Low (backlog)',
        ],
        'default_value' => 'p2',
        'layout' => 'horizontal',
      ],
      // Due Date
      [
        'key' => 'field_csl_due_date',
        'label' => 'Due Date',
        'name'  => 'due_date',
        'type'  => 'date_time_picker',
        'display_format' => 'Y-m-d H:i',
        'return_format'  => 'U',
      ],
      // Brief Summary
      [
        'key' => 'field_csl_brief_summary',
        'label' => 'Brief Summary',
        'name'  => 'brief_summary',
        'type'  => 'textarea',
        'maxlength' => 320,
        'rows' => 3,
        'required' => 1,
      ],
      // File Uploads (repeater)
      [
        'key' => 'field_csl_file_uploads',
        'label' => 'File Uploads',
        'name'  => 'file_uploads',
        'type'  => 'repeater',
        'layout' => 'row',
        'button_label' => 'Add File',
        'sub_fields' => [
          [
            'key' => 'field_csl_asset_file',
            'label' => 'Asset File',
            'name'  => 'asset_file',
            'type'  => 'file',
            'mime_types' => 'pdf,ai,psd,indd,fig,jpg,jpeg,png,mp4,zip',
            'return_format' => 'array',
          ],
          [
            'key' => 'field_csl_usage_rights',
            'label' => 'Usage Rights',
            'name'  => 'usage_rights',
            'type'  => 'select',
            'choices' => [
              'owned'         => 'Owned',
              'licensed'      => 'Licensed',
              'needs_license' => 'Needs License',
            ],
            'allow_null' => 1,
          ],
          [
            'key' => 'field_csl_file_notes',
            'label' => 'Notes',
            'name'  => 'notes',
            'type'  => 'text',
          ],
        ],
      ],
      // Internal Notes (admin-only on front-end)
      [
        'key' => 'field_csl_internal_notes',
        'label' => 'Internal Notes',
        'name'  => 'internal_notes',
        'type'  => 'textarea',
        'rows'  => 3,
      ],
    ],
    'location' => [[[
      'param' => 'post_type',
      'operator' => '==',
      'value' => 'ticket',
    ]]],
    'position' => 'acf_after_title',
    'style' => 'default',
    'active' => true,
  ]);
});

/* -------------------------------------------------------
 * 3) Validation: Due Date is required for P0/P1
 * ----------------------------------------------------- */
add_filter('acf/validate_value/key=field_csl_due_date', function ($valid, $value) {
  if ($valid !== true) return $valid;
  $priority = $_POST['acf']['field_csl_priority'] ?? $_POST['acf']['priority'] ?? null;
  if (in_array($priority, ['p0','p1'], true) && empty($value)) {
    return 'Due Date is required for P0/P1.';
  }
  return $valid;
}, 10, 2);

/* -------------------------------------------------------
 * 4) Hide Internal Notes on front-end (still visible in wp-admin)
 * ----------------------------------------------------- */
add_filter('acf/prepare_field/key=field_csl_internal_notes', function ($field) {
  if (!is_admin()) return false;
  return $field;
});

/* -------------------------------------------------------
 * 5) Auto-title on create (friendly admin lists)
 * ----------------------------------------------------- */
add_action('acf/save_post', function($post_id){
  if (get_post_type($post_id) !== 'ticket' || wp_is_post_revision($post_id)) return;

  $post = get_post($post_id);
  if ($post && !trim($post->post_title)) {
    $type  = get_field('request_type', $post_id) ?: 'Request';
    $prio  = get_field('priority', $post_id) ?: 'p2';
    $brief = (string) get_field('brief_summary', $post_id);
    $first = $brief ? wp_trim_words($brief, 6, '') : '';
    wp_update_post([
      'ID'         => $post_id,
      'post_title' => sprintf('[%s] %s — %s', strtoupper($prio), ucwords(str_replace('_',' ', $type)), $first),
    ]);
  }
}, 15);

/* -------------------------------------------------------
 * 6) Admin list columns (priority, type, due, date)
 * ----------------------------------------------------- */
add_filter('manage_edit-ticket_columns', function($cols){
  return [
    'cb'           => $cols['cb'],
    'title'        => 'Title',
    'priority'     => 'Priority',
    'request_type' => 'Type',
    'due_date'     => 'Due',
    'date'         => 'Submitted',
  ];
});
add_action('manage_ticket_posts_custom_column', function($col, $post_id){
  switch ($col) {
    case 'priority':
      echo esc_html(get_field('priority', $post_id) ?: '—');
      break;
    case 'request_type':
      echo esc_html(get_field('request_type', $post_id) ?: '—');
      break;
    case 'due_date':
      $due = get_field('due_date', $post_id);
      echo $due ? esc_html(date_i18n('M j, Y g:ia', is_numeric($due) ? $due : strtotime((string)$due))) : '—';
      break;
  }
}, 10, 2);

/* -------------------------------------------------------
 * 7) Upload MIME types (AI/PSD/INDD/FIG etc.)
 * ----------------------------------------------------- */
add_filter('upload_mimes', function($m){
  // Keep expectations realistic: some hosts block exotic types regardless.
  $m['ai']   = 'application/postscript';
  $m['psd']  = 'image/vnd.adobe.photoshop';
  $m['indd'] = 'application/x-indesign';
  $m['fig']  = 'application/octet-stream';
  return $m;
});

/* -------------------------------------------------------
 * 8) Email notify on new ticket (studio + requester)
 * ----------------------------------------------------- */
add_action('acf/save_post', function($post_id){
  // Only for Tickets; skip revisions/autosaves
  if (get_post_type($post_id) !== 'ticket' || wp_is_post_revision($post_id)) return;

  // Only on initial creation (not updates)
  $post = get_post($post_id);
  if (!$post) return;
  if ($post->post_date_gmt !== $post->post_modified_gmt) return;

  // Gather fields
  $title    = get_the_title($post_id);
  $prio     = get_field('priority', $post_id) ?: 'Not Set';
  $type     = get_field('request_type', $post_id) ?: 'Not Set';
  $summary  = (string) get_field('brief_summary', $post_id);
  $due_raw  = get_field('due_date', $post_id);
  $due_disp = $due_raw ? date_i18n('M j, Y g:ia', is_numeric($due_raw) ? (int)$due_raw : strtotime((string)$due_raw)) : '—';

  // Requester info
  $author_id = (int) get_post_field('post_author', $post_id);
  $author    = $author_id ? get_userdata($author_id) : null;
  $req_name  = $author ? $author->display_name : 'Requester';
  $req_email = $author ? $author->user_email : '';

  // File uploads (cap at 5 lines)
  $files = get_field('file_uploads', $post_id);
  $file_lines = [];
  if (is_array($files)) {
    foreach ($files as $i => $row) {
      if ($i >= 5) { $file_lines[] = '… (more files attached)'; break; }
      $f = $row['asset_file'] ?? null;
      if (is_array($f) && !empty($f['url'])) {
        $filename = $f['filename'] ?? basename($f['url']);
        $file_lines[] = sprintf('- %s (%s)', sanitize_text_field($filename), esc_url_raw($f['url']));
      }
    }
  }

  // Links
  $admin_url = admin_url("post.php?post=$post_id&action=edit");
  $front_url = esc_url( add_query_arg('post_id', $post_id, site_url('/edit-request/')) );

  // Studio Notification
  $to       = 'ticket@casestudy-labs.com';
  $subject  = "New Design Request: $title";
  $headers  = ['Content-Type: text/plain; charset=UTF-8'];

  $body  = "A new design request has been submitted.\n\n";
  $body .= "Title: $title\n";
  $body .= "Priority: " . strtoupper($prio) . "\n";
  $body .= "Request Type: " . ucwords(str_replace('_', ' ', $type)) . "\n";
  $body .= "Due: $due_disp\n";
  $body .= "Requester: $req_name" . ($req_email ? " <{$req_email}>" : '') . "\n\n";
  if ($summary) {
    $body .= "Summary:\n" . wp_strip_all_tags($summary) . "\n\n";
  }
  if (!empty($file_lines)) {
    $body .= "Files:\n" . implode("\n", $file_lines) . "\n\n";
  }
  $body .= "Admin edit:\n$admin_url\n";
  $body .= "Front-end view:\n$front_url\n";

  wp_mail($to, $subject, $body, $headers);

  // Requester Confirmation
  if ($req_email && is_email($req_email)) {
    $subj2 = "We got your request: $title";
    $body2  = "Hi $req_name,\n\n";
    $body2 .= "Thanks for submitting your design request to Case Study Labs. Our studio has received it and will review shortly.\n\n";
    if ($summary) $body2 .= "Summary:\n" . wp_strip_all_tags($summary) . "\n\n";
    $body2 .= "Priority: " . strtoupper($prio) . "\n";
    $body2 .= "Request Type: " . ucwords(str_replace('_', ' ', $type)) . "\n";
    $body2 .= "Due: $due_disp\n\n";
    $body2 .= "You can view or update your ticket here:\n$front_url\n\n";
    $body2 .= "— The Case Study Labs Studio Team";

    wp_mail($req_email, $subj2, $body2, $headers);
  }

}, 20); // after auto-title (priority 15)

/* -------------------------------------------------------
 * 9) CPT: inquiry  (guard against duplicates)
 * ----------------------------------------------------- */
add_action('init', function () {
  if (post_type_exists('inquiry')) return;
  register_post_type('inquiry', [
    'labels' => [
      'name'          => 'Inquiries',
      'singular_name' => 'Inquiry',
      'add_new_item'  => 'Add New Inquiry',
      'edit_item'     => 'Edit Inquiry',
      'view_item'     => 'View Inquiry',
      'search_items'  => 'Search Inquiries',
    ],
    'public'              => false,
    'publicly_queryable'  => false,
    'exclude_from_search' => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_rest'        => true,
    'rewrite'             => false,
    'map_meta_cap'        => true,
    'capability_type'     => 'post',
    'supports'            => ['title'], // title auto-generated elsewhere if needed
    'menu_icon'           => 'dashicons-businesswoman',
    'menu_position'       => 21,
  ]);
}, 9);
/* -------------------------------------------------------
 * 10) Ensure ACF front-end forms initialize on our pages
 * ----------------------------------------------------- */
add_action('template_redirect', function () {
  if (!function_exists('acf_form_head')) return;

  // Load ACF assets/handlers early on pages that will show our forms
  if (is_page() && function_exists('has_shortcode')) {
    $page = get_queried_object();
    if ($page && !empty($page->post_content)) {
      if (has_shortcode($page->post_content, 'csl_new_ticket_form') || has_shortcode($page->post_content, 'csl_contact_form')) {
        acf_form_head();
      }
    }
  }
});

/* -------------------------------------------------------
 * 11) Shortcode: [csl_new_ticket_form]
 *     - Creates a new "ticket" post with your ACF group.
 * ----------------------------------------------------- */
add_shortcode('csl_new_ticket_form', function ($atts = []) {
  if (!function_exists('acf_form')) {
    return '<p><em>Form error: ACF Pro is required.</em></p>';
  }

  // Optionally require login for ticket intake
  if (!is_user_logged_in()) {
    return '<p>Please <a href="' . esc_url(wp_login_url(get_permalink())) . '">log in</a> to submit a ticket.</p>';
  }

  ob_start();

  // Success message
  if (!empty($_GET['ticket_submitted'])) {
    echo '<div class="notice notice-success" style="margin:1em 0;padding:12px;border-left:4px solid #46b450;background:#f6fff6;">Thanks — your ticket was submitted.</div>';
  }

  // Render the ACF form for a NEW ticket
  acf_form([
    'id'             => 'csl-new-ticket',
    'post_id'        => 'new_post',
    'new_post'       => [
      'post_type'   => 'ticket',
      'post_status' => 'publish',
      // Title is auto-generated by our acf/save_post hook; leave empty.
    ],
    // Use your field group key from this plugin:
    'field_groups'   => ['group_csl_design_request'],
    'form'           => true,
    'html_submit_button' => '<button type="submit" class="button button-primary">Submit Ticket</button>',
    'submit_value'   => 'Submit Ticket',
    'uploader'       => 'wp',
    'honeypot'       => true,
    'html_before_fields' => '<div class="csl-form csl-ticket">',
    'html_after_fields'  => '</div>',
    'return'         => add_query_arg('ticket_submitted', '1', get_permalink()),
  ]);

  return ob_get_clean();
});

/* -------------------------------------------------------
 * Shortcode: [csl_contact_form]
 *  - Qualifies the lead and creates an "inquiry" post
 *  - Sends a detailed email to studio + confirmation to sender
 * ----------------------------------------------------- */
add_shortcode('csl_contact_form', function ($atts = []) {
  $atts = shortcode_atts([
    'to' => 'ticket@casestudy-labs.com',
  ], $atts, 'csl_contact_form');

  $errors = [];
  $ok     = !empty($_GET['contact_ok']);

  // Handle POST
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csl_contact_nonce'])) {
    if (!wp_verify_nonce($_POST['csl_contact_nonce'], 'csl_contact')) {
      $errors[] = 'Security check failed. Please try again.';
    } else {
      // Honeypot & basic timing anti-spam
      $hp = trim($_POST['csl_hp'] ?? '');
      $ts = isset($_POST['csl_ts']) ? (int) $_POST['csl_ts'] : 0;
      if ($hp !== '') $errors[] = 'Unexpected input.';
      if (time() - $ts < 3) $errors[] = 'Please wait a moment before submitting.';

      // Collect + sanitize fields
      $name        = sanitize_text_field($_POST['csl_name'] ?? '');
      $email       = sanitize_email($_POST['csl_email'] ?? '');
      $phone       = sanitize_text_field($_POST['csl_phone'] ?? '');
      $company     = sanitize_text_field($_POST['csl_company'] ?? '');
      $proj_type   = sanitize_text_field($_POST['csl_project_type'] ?? '');
      $budget      = sanitize_text_field($_POST['csl_budget'] ?? '');
      $timeline    = sanitize_text_field($_POST['csl_timeline'] ?? '');
      $source      = sanitize_text_field($_POST['csl_source'] ?? '');
      $message_raw = wp_kses_post($_POST['csl_message'] ?? '');
      $message     = trim($message_raw);

      // Validate requireds
      if ($name === '')                   $errors[] = 'Name is required.';
      if (!is_email($email))              $errors[] = 'A valid email is required.';
      if ($proj_type === '')              $errors[] = 'Please choose a project type.';
      if ($budget === '')                 $errors[] = 'Please choose a budget range.';
      if ($timeline === '')               $errors[] = 'Please choose a timeline.';
      if ($message === '')                $errors[] = 'Message / project goals are required.';

      if (!$errors) {
        // Create inquiry post
        $title = sprintf(
          'Inquiry: %s — %s',
          $name,
          wp_trim_words(wp_strip_all_tags($message), 8, '…')
        );

        $post_id = wp_insert_post([
          'post_type'   => 'inquiry',
          'post_status' => 'publish',
          'post_title'  => $title,
          'post_content'=> $message, // keep the raw message in post content
        ]);

        if ($post_id && !is_wp_error($post_id)) {
          // Save structured meta for admin use / querying
          update_post_meta($post_id, 'client_name', $name);
          update_post_meta($post_id, 'email', $email);
          if ($phone)   update_post_meta($post_id, 'phone', $phone);
          if ($company) update_post_meta($post_id, 'company', $company);
          update_post_meta($post_id, 'project_type', $proj_type);
          update_post_meta($post_id, 'budget_range', $budget);
          update_post_meta($post_id, 'timeline', $timeline);
          if ($source)  update_post_meta($post_id, 'referral_source', $source);

          // Build studio email
          $admin_url = admin_url("post.php?post={$post_id}&action=edit");
          $subject   = "New Project Inquiry from {$name}";
          $headers   = ['Content-Type: text/plain; charset=UTF-8'];

          $body  = "A new client inquiry has been submitted.\n\n";
          $body .= "Name: {$name}\n";
          $body .= "Email: {$email}\n";
          if ($phone)   $body .= "Phone: {$phone}\n";
          if ($company) $body .= "Company: {$company}\n";
          $body .= "Project Type: {$proj_type}\n";
          $body .= "Budget Range: {$budget}\n";
          $body .= "Timeline: {$timeline}\n";
          if ($source)  $body .= "How they heard about us: {$source}\n";
          $body .= "\nProject Goals / Description:\n" . wp_strip_all_tags($message) . "\n\n";
          $body .= "Admin edit:\n{$admin_url}\n";

          wp_mail($atts['to'], $subject, $body, $headers);

          // Confirmation to sender (best-effort)
          if (is_email($email)) {
            $conf_subj = 'We received your inquiry';
            $conf_body  = "Hi {$name},\n\n";
            $conf_body .= "Thanks for reaching out. We received your inquiry and will get back to you shortly.\n\n";
            $conf_body .= "Summary of your submission:\n";
            $conf_body .= "- Project Type: {$proj_type}\n";
            $conf_body .= "- Budget Range: {$budget}\n";
            $conf_body .= "- Timeline: {$timeline}\n\n";
            $conf_body .= "Your message:\n" . wp_strip_all_tags($message) . "\n\n";
            $conf_body .= "— Case Study Labs";
            wp_mail($email, $conf_subj, $conf_body, $headers);
          }

          // Redirect to avoid resubmits
          wp_safe_redirect(add_query_arg('contact_ok', '1', get_permalink()));
          exit;
        } else {
          $errors[] = 'Could not save your inquiry. Please try again.';
        }
      }
    }
  }

  // Render form
  ob_start();

  if ($ok) {
    echo '<div class="notice notice-success" style="margin:1em 0;padding:12px;border-left:4px solid #46b450;background:#f6fff6;">Thanks — we received your inquiry.</div>';
  }

  if ($errors) {
    echo '<div class="notice notice-error" style="margin:1em 0;padding:12px;border-left:4px solid #dc3232;background:#fff7f7;"><ul style="margin:0;padding-left:1.2em;">';
    foreach ($errors as $e) echo '<li>' . esc_html($e) . '</li>';
    echo '</ul></div>';
  }

  $now = time();
  ?>
  <form method="post" class="csl-form csl-contact form" novalidate>
    <?php wp_nonce_field('csl_contact', 'csl_contact_nonce'); ?>
    <input type="hidden" name="csl_ts" value="<?php echo esc_attr($now); ?>">
    <input type="text" name="csl_hp" value="" style="display:none !important;" tabindex="-1" autocomplete="off">

    <h3 class="title">Project Inquiry</h3>

    <p>
      <label>
        <input class="input" type="text" name="csl_name" required placeholder=" ">
        <span>Name</span>
      </label>
    </p>

    <div class="flex">
      <label>
        <input class="input" type="email" name="csl_email" required placeholder=" ">
        <span>Email</span>
      </label>
      <label>
        <input class="input" type="text" name="csl_phone" placeholder=" ">
        <span>Phone (optional)</span>
      </label>
    </div>

    <p>
      <label>
        <input class="input" type="text" name="csl_company" placeholder=" ">
        <span>Company / Org (optional)</span>
      </label>
    </p>

    <div class="flex">
      <label>
<select class="input" name="csl_project_type" required>
  <option value="" selected disabled hidden>Select project type…</option>
  <option value="website">Website</option>
  <option value="branding">Branding</option>
  <option value="marketing">Marketing</option>
  <option value="ecommerce">E-commerce</option>
  <option value="other">Other</option>
</select>
<span>Project Type</span>
      </label>

      <label>
        <select class="input" name="csl_budget" required>
          <option value="">Select budget…</option>
          <option>&lt;$5k</option>
          <option>$5k–$10k</option>
          <option>$10k–$25k</option>
          <option>$25k+</option>
        </select>
        <span>Budget Range</span>
      </label>
    </div>

    <div class="flex">
      <label>
        <select class="input" name="csl_timeline" required>
          <option value="">Select timeline…</option>
          <option>ASAP</option>
          <option>1–3 months</option>
          <option>3–6 months</option>
          <option>Flexible</option>
        </select>
        <span>Timeline</span>
      </label>

      <label>
        <select class="input" name="csl_source">
          <option value="">How did you hear about us? (optional)</option>
          <option>Referral</option>
          <option>Google</option>
          <option>Social</option>
          <option>Event</option>
          <option>Other</option>
        </select>
        <span>Referral Source</span>
      </label>
    </div>

    <p>
      <label>
        <textarea class="input" name="csl_message" rows="6" required placeholder=" "></textarea>
        <span>Project goals / description</span>
      </label>
    </p>

    <p><button type="submit" class="submit">Submit Inquiry</button></p>
  </form>
  <?php

  return ob_get_clean();
});

// Footer email signup form (upgraded)
add_shortcode('csl_footer_signup', function () {
  $ok = (isset($_GET['nl_ok']) && $_GET['nl_ok'] === '1');
  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST'
      && isset($_POST['csl_footer_submit']) && $_POST['csl_footer_submit'] === '1') {

    // Nonce
    if (!isset($_POST['csl_footer_nonce']) || !wp_verify_nonce($_POST['csl_footer_nonce'], 'csl_footer')) {
      $errors[] = 'Security check failed. Please try again.';
    }

    // Honeypot + timing
    $hp = trim((string)($_POST['csl_hp2'] ?? ''));
    $ts = (int)($_POST['csl_ts2'] ?? 0);
    if ($hp !== '')              $errors[] = 'Unexpected input.';
    if (time() - $ts < 2)        $errors[] = 'Please wait a moment before submitting.';

    // Email
    $email = sanitize_email((string)($_POST['csl_email'] ?? ''));
    if (!is_email($email))       $errors[] = 'Please enter a valid email address.';

    if (!$errors) {
      // Save signup as Inquiry (or swap to a dedicated CPT later)
      $post_id = wp_insert_post([
        'post_type'   => 'inquiry',
        'post_status' => 'publish',
        'post_title'  => sprintf('Signup: %s', $email),
        'post_content'=> '',
      ]);

      if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, 'email', $email);
        update_post_meta($post_id, 'source', 'footer_signup');

        // Notify you
        wp_mail(
          'dough@casestudylabs.com',
          'New Email Signup',
          "New subscriber: {$email}\n\nAdmin: " . admin_url("post.php?post={$post_id}&action=edit"),
          ['Content-Type: text/plain; charset=UTF-8']
        );

        // Redirect back to referrer to avoid resubmits
        $back = wp_get_referer() ?: home_url('/');
        wp_safe_redirect(add_query_arg('nl_ok', '1', $back));
        exit;
      } else {
        $errors[] = 'Could not save your signup. Please try again.';
      }
    }
  }

  ob_start();

  if ($ok) {
    echo '<div class="notice notice-success">Thanks — you’re on the list.</div>';
  }

  if ($errors) {
    echo '<div class="notice notice-error"><ul style="margin:0;padding-left:1.2em;">';
    foreach ($errors as $e) echo '<li>' . esc_html($e) . '</li>';
    echo '</ul></div>';
  }

  $now = time();
  ?>
  <form method="post" class="csl-footer-signup" novalidate>
    <?php wp_nonce_field('csl_footer', 'csl_footer_nonce'); ?>
    <input type="hidden" name="csl_footer_submit" value="1">
    <input type="hidden" name="csl_ts2" value="<?php echo esc_attr($now); ?>">
    <input type="text" name="csl_hp2" value="" style="display:none !important;" tabindex="-1" autocomplete="off">

    <div class="signup-row">
      <input type="email" name="csl_email" placeholder="Your email" required>
      <button type="submit" class="btn btn-accent">Join</button>
    </div>
  </form>
  <?php

  return ob_get_clean();
});
