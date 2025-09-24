<?php
/**
 * Template: Fallback index.
 *
 * This file exists to satisfy WordPress’s requirement for a theme to have a
 * `templates/index.html` or `index.php` file when treating the theme as a
 * block theme. Even though this theme is a classic PHP theme, some
 * environments erroneously look for a template in the `templates` directory.
 * To avoid installation errors, we include this simple wrapper that loads
 * the standard `index.php` from the root of the theme. If you are not
 * using full site editing (FSE), this file will never be executed.
 *
 * @package auragrid-lite
 */

// Include the classic template
require get_template_directory() . '/index.php';