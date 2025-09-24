<?php
/**
 * Render admin notices if Toolset Types or WP All Import plugins are activated
 */
function pmti_admin_notices() {

	// notify user if history folder is not writable		
	if ( ! class_exists( 'PMXI_Plugin' ) ) { ?>
		<div class="error"><p>
			<?php printf(
					__('<b>%s Plugin</b>: WP All Import must be installed. Free edition of WP All Import at <a href="http://wordpress.org/plugins/wp-all-import/" target="_blank">http://wordpress.org/plugins/wp-all-import/</a> and the paid edition at <a href="http://www.wpallimport.com/">http://www.wpallimport.com/</a>', PMTI_Plugin::TEXT_DOMAIN),
					PMTI_Plugin::getInstance()->getName()
			) ?>
		</p></div>
		<?php
		deactivate_plugins( PMTI_ROOT_DIR . '/wpai-toolset-types-add-on.php');
	}

	if ( class_exists( 'PMXI_Plugin' ) and ( version_compare(PMXI_VERSION, '4.1.1') < 0 and PMXI_EDITION == 'paid' or version_compare(PMXI_VERSION, '3.2.3') <= 0 and PMXI_EDITION == 'free') ) { ?>
		<div class="error"><p>
			<?php printf(
					__('<b>%s Plugin</b>: Please update your WP All Import to the latest version', PMTI_Plugin::TEXT_DOMAIN),
					PMTI_Plugin::getInstance()->getName()
			) ?>
		</p></div>
		<?php
		deactivate_plugins( PMTI_ROOT_DIR . '/wpai-toolset-types-add-on.php');
	}

	if ( ! class_exists( 'Types_Main' ) ) { ?>
		<div class="error"><p>
			<?php printf(
					__('<b>%s Plugin</b>: <a target="_blank" href="https://toolset.com/">Toolset Types</a> must be installed', PMTI_Plugin::TEXT_DOMAIN),
					PMTI_Plugin::getInstance()->getName()
			) ?>
		</p></div>
		<?php
		deactivate_plugins( PMTI_ROOT_DIR . '/wpai-toolset-types-add-on.php');
	}

	if ( class_exists( 'Types_Main' ) and ( version_compare(TYPES_VERSION, '3.4.1') < 0 ) ) { ?>
        <div class="error"><p>
				<?php printf(
					__('<b>%s Plugin</b>:Please update your <a target="_blank" href="https://toolset.com/">Toolset Types</a> plugin to the latest version', PMTI_Plugin::TEXT_DOMAIN),
					PMTI_Plugin::getInstance()->getName()
				) ?>
            </p></div>
		<?php
		deactivate_plugins( PMTI_ROOT_DIR . '/wpai-toolset-types-add-on.php');
	}

	$input = new PMTI_Input();
	$messages = $input->get('pmti_nt', []);
	if ($messages) {
		is_array($messages) or $messages = [$messages];
		foreach ($messages as $type => $m) {
			in_array((string)$type, ['updated', 'error']) or $type = 'updated'; ?>
			<div class="<?php echo $type ?>"><p><?php echo $m ?></p></div>
			<?php 
		}
	}
}