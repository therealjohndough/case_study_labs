<?php
/*
Plugin Name: WP All Import - Toolset Types Add-On Pro
Plugin URI: http://www.wpallimport.com/
Description: Import to Toolset Types. Requires WP All Import & Toolset Types.
Version: 1.0.7
Author: Soflyy
*/
/**
 * Plugin root dir with forward slashes as directory separator regardless of actual DIRECTORY_SEPARATOR value
 * @var string
 */

use OTGS\Toolset\Common\Relationships\DatabaseLayer\DatabaseLayerFactory;
use OTGS\Toolset\Common\Relationships\MainController;

define('PMTI_ROOT_DIR', str_replace('\\', '/', dirname(__FILE__)));
/**
 * Plugin root url for referencing static content
 * @var string
 */
define('PMTI_ROOT_URL', rtrim(plugin_dir_url(__FILE__), '/'));
/**
 * Plugin prefix for making names unique (be aware that this variable is used in conjuction with naming convention,
 * i.e. in order to change it one must not only modify this constant but also rename all constants, classes and functions which
 * names composed using this prefix)
 * @var string
 */
define('PMTI_PREFIX', 'pmti_');

/**
 *  Current version.
 */
define('PMTI_VERSION', '1.0.7');

if ( class_exists('PMTI_Plugin') and PMTI_EDITION == "free"){

    /**
     *  Free edition notifications.
     */
    function pmti_notice() { ?>
		<div class="error"><p>
			<?php printf(__('Please de-activate and remove the free version of the Toolset Types Add-On before activating the paid version.', 'PMTI_Plugin')); ?>
		</p></div>
		<?php
		deactivate_plugins(PMTI_ROOT_DIR . '/plugin.php');
	}
	add_action('admin_notices', 'pmti_notice');

} else {

    /**
     * Current edition.
     */
    define('PMTI_EDITION', 'paid');

    require PMTI_ROOT_DIR . '/vendor/autoload.php';

	/**
	 * Main plugin file, Introduces MVC pattern
	 *
	 * @singletone
	 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
	 */

	final class PMTI_Plugin {
		/**
		 * Singletone instance
		 * @var PMTI_Plugin
		 */
		protected static $instance;

		/**
		 * Plugin options
		 * @var array
		 */
		protected $options = [];

		/** @var DatabaseLayerFactory|null */
		private $_database_layer_factory;

		/**
		 * Plugin root dir
		 * @var string
		 */
		const ROOT_DIR = PMTI_ROOT_DIR;
		/**
		 * Plugin root URL
		 * @var string
		 */
		const ROOT_URL = PMTI_ROOT_URL;
		/**
		 * Prefix used for names of shortcodes, action handlers, filter functions etc.
		 * @var string
		 */
		const PREFIX = PMTI_PREFIX;
		/**
		 * Plugin file path
		 * @var string
		 */
		const FILE = __FILE__;

        /**
         * Plugin text domain.
         */
        const TEXT_DOMAIN = 'wp_all_import_wpcs_add_on';

        /**
         * @var array
         */
        public static $allWpcsFields = [];

        /**
		 * Return singletone instance
		 * @return PMTI_Plugin
		 */
		static public function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new self();
			}
			return self::$instance;
		}

        /**
         * @return string
         */
        static public function getEddName() {
			return 'Toolset Types Add-On Pro';
		}

		/**
		 * Common logic for requestin plugin info fields
		 */
		public function __call($method, $args) {
			if (preg_match('%^get(.+)%i', $method, $mtch)) {
				$info = get_plugin_data(self::FILE);
				if (isset($info[$mtch[1]])) {
					return $info[$mtch[1]];
				}
			}
			throw new Exception("Requested method " . get_class($this) . "::$method doesn't exist.");
		}

		/**
		 * Get path to plagin dir relative to wordpress root
		 * @param bool[optional] $noForwardSlash Whether path should be returned withot forwarding slash
		 * @return string
		 */
		public function getRelativePath($noForwardSlash = false) {
			$wp_root = str_replace('\\', '/', ABSPATH);
			return ($noForwardSlash ? '' : '/') . str_replace($wp_root, '', self::ROOT_DIR);
		}

		/**
		 * Check whether plugin is activated as network one
		 * @return bool
		 */
		public function isNetwork() {
			if ( !is_multisite() )
			return false;

			$plugins = get_site_option('active_sitewide_plugins');
			if (isset($plugins[plugin_basename(self::FILE)]))
				return true;

			return false;
		}

		/**
		 * Check whether permalinks is enabled
		 * @return bool
		 */
		public function isPermalinks() {
			global $wp_rewrite;
			return $wp_rewrite->using_permalinks();
		}

		/**
		 * Return prefix for plugin database tables
		 * @return string
		 */
		public function getTablePrefix() {
			global $wpdb;
			return ($this->isNetwork() ? $wpdb->base_prefix : $wpdb->prefix) . self::PREFIX;
		}

		/**
		 * Return prefix for wordpress database tables
		 * @return string
		 */
		public function getWPPrefix() {
			global $wpdb;
			return ($this->isNetwork() ? $wpdb->base_prefix : $wpdb->prefix);
		}

		/**
		 * Class constructor containing dispatching logic
		 * @param string $rootDir Plugin root dir
		 * @param string $pluginFilePath Plugin main file
		 */
		protected function __construct() {

			// register autoloading method
			spl_autoload_register(array($this, 'autoload'));

			// register helpers
			if (is_dir(self::ROOT_DIR . '/helpers')) foreach (PMTI_Helper::safe_glob(self::ROOT_DIR . '/helpers/*.php', PMTI_Helper::GLOB_RECURSE | PMTI_Helper::GLOB_PATH) as $filePath) {
				require_once $filePath;
			}

			// init plugin options
			$option_name = get_class($this) . '_Options';
			$options_default = PMTI_Config::createFromFile(self::ROOT_DIR . '/config/options.php')->toArray();

            $this->options = array_intersect_key(get_option($option_name, []), $options_default) + $options_default;
            $this->options = array_intersect_key($options_default, array_flip(['info_api_url'])) + $this->options; // make sure hidden options apply upon plugin reactivation

            update_option($option_name, $this->options);
			$this->options = get_option(get_class($this) . '_Options');

			register_activation_hook(self::FILE, [$this, 'activation']);

			// register action handlers
			if (is_dir(self::ROOT_DIR . '/actions')) if (is_dir(self::ROOT_DIR . '/actions')) foreach (PMTI_Helper::safe_glob(self::ROOT_DIR . '/actions/*.php', PMTI_Helper::GLOB_RECURSE | PMTI_Helper::GLOB_PATH) as $filePath) {
				require_once $filePath;
				$function = $actionName = basename($filePath, '.php');
				if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
					$actionName = $m[1];
					$priority = intval($m[2]);
				} else {
					$priority = 10;
				}
				add_action($actionName, self::PREFIX . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
			}

			// register filter handlers
			if (is_dir(self::ROOT_DIR . '/filters')) foreach (PMTI_Helper::safe_glob(self::ROOT_DIR . '/filters/*.php', PMTI_Helper::GLOB_RECURSE | PMTI_Helper::GLOB_PATH) as $filePath) {
				require_once $filePath;
				$function = $actionName = basename($filePath, '.php');
				if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
					$actionName = $m[1];
					$priority = intval($m[2]);
				} else {
					$priority = 10;
				}

				add_filter($actionName, self::PREFIX . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
			}

			// register shortcodes handlers
			if (is_dir(self::ROOT_DIR . '/shortcodes')) foreach (PMTI_Helper::safe_glob(self::ROOT_DIR . '/shortcodes/*.php', PMTI_Helper::GLOB_RECURSE | PMTI_Helper::GLOB_PATH) as $filePath) {
				$tag = strtolower(str_replace('/', '_', preg_replace('%^' . preg_quote(self::ROOT_DIR . '/shortcodes/', '%') . '|\.php$%', '', $filePath)));
				add_shortcode($tag, [$this, 'shortcodeDispatcher']);
			}

			// register admin page pre-dispatcher
			add_action('admin_init', [$this, 'adminInit'], 1);
			add_action('init', [$this, 'init'], 10);
		}

		/**
		 * @return mixed|DatabaseLayerFactory|null
		 * @noinspection PhpDocMissingThrowsInspection
		 */
		public function get_database_layer_factory() {
			if ( null === $this->_database_layer_factory ) {
				MainController::get_instance()->initialize();
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->_database_layer_factory = toolset_dic()->make( DatabaseLayerFactory::class );
			}

			return $this->_database_layer_factory;
		}

        /**
         *  Init all available Toolset fields.
         */
        public static function initAllAvailableWpcsFields() {
		    self::$allWpcsFields = \wpai_toolset_types_add_on\ToolsetService::getAllWpcs();
        }

        /**
         *  Load plugin language text domain.
         */
        public function init(){
			$this->load_plugin_textdomain();
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present
		 *
		 * @access public
		 * @return void
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), PMTI_Plugin::TEXT_DOMAIN );
			load_plugin_textdomain( PMTI_Plugin::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
		}

		/**
		 * pre-dispatching logic for admin page controllers
		 */
		public function adminInit() {
			$input = new PMTI_Input();
			$page = strtolower($input->getpost('page', ''));
			if (preg_match('%^' . preg_quote(str_replace('_', '-', self::PREFIX), '%') . '([\w-]+)$%', $page)) {
				$this->adminDispatcher($page, strtolower($input->getpost('action', 'index')));
			}
		}

		/**
		 * Dispatch shorttag: create corresponding controller instance and call its index method
		 * @param array $args Shortcode tag attributes
		 * @param string $content Shortcode tag content
		 * @param string $tag Shortcode tag name which is being dispatched
		 * @return string
		 */
		public function shortcodeDispatcher($args, $content, $tag) {

			$controllerName = self::PREFIX . preg_replace_callback('%(^|_).%', [$this, "replace_callback"], $tag);// capitalize first letters of class name parts and add prefix
			$controller = new $controllerName();
			if ( ! $controller instanceof PMTI_Controller) {
				throw new Exception("Shortcode `$tag` matches to a wrong controller type.");
			}
			ob_start();
			$controller->index($args, $content);
			return ob_get_clean();
		}

		/**
		 * Dispatch admin page: call corresponding controller based on get parameter `page`
		 * The method is called twice: 1st time as handler `parse_header` action and then as admin menu item handler
		 * @param string[optional] $page When $page set to empty string ealier buffered content is outputted, otherwise controller is called based on $page value
		 */
		public function adminDispatcher($page = '', $action = 'index') {
			static $buffer = NULL;
			static $buffer_callback = NULL;
			if ('' === $page) {
				if ( ! is_null($buffer)) {
					echo '<div class="wrap">';
					echo $buffer;
					do_action('pmti_action_after');
					echo '</div>';
				} elseif ( ! is_null($buffer_callback)) {
					echo '<div class="wrap">';
					call_user_func($buffer_callback);
					do_action('pmti_action_after');
					echo '</div>';
				} else {
					throw new Exception('There is no previousely buffered content to display.');
				}
			} else {
				$actionName = str_replace('-', '_', $action);
				// capitalize prefix and first letters of class name parts
				$controllerName = preg_replace_callback('%(^' . preg_quote(self::PREFIX, '%') . '|_).%', [$this, "replace_callback"],str_replace('-', '_', $page));
				if (method_exists($controllerName, $actionName)) {

					if ( ! get_current_user_id() or ! current_user_can(PMXI_Plugin::$capabilities)) {
					    // This nonce is not valid.
					    die( 'Security check' );

					} else {

						$this->_admin_current_screen = (object)array(
							'id' => $controllerName,
							'base' => $controllerName,
							'action' => $actionName,
							'is_ajax' => isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest',
							'is_network' => is_network_admin(),
							'is_user' => is_user_admin(),
						);
						add_filter('current_screen', [$this, 'getAdminCurrentScreen']);

						$controller = new $controllerName();
						if ( ! $controller instanceof PMTI_Controller_Admin) {
							throw new Exception("Administration page `$page` matches to a wrong controller type.");
						}

						if ($this->_admin_current_screen->is_ajax) { // ajax request
							$controller->$action();
							do_action('pmti_action_after');
							die(); // stop processing since we want to output only what controller is randered, nothing in addition
						} elseif ( ! $controller->isInline) {
							ob_start();
							$controller->$action();
							$buffer = ob_get_clean();
						} else {
							$buffer_callback = [$controller, $action];
						}
					}

				} else { // redirect to dashboard if requested page and/or action don't exist
					wp_redirect(admin_url()); die();
				}
			}
		}

        /**
         * @var null
         */
        protected $_admin_current_screen = NULL;

        /**
         * @return null
         */
        public function getAdminCurrentScreen()
		{
			return $this->_admin_current_screen;
		}

        /**
         * @param $matches
         * @return string
         */
        public function replace_callback($matches){
			return strtoupper($matches[0]);
		}

		/**
		 * Autoloader
		 * It's assumed class name consists of prefix folloed by its name which in turn corresponds to location of source file
		 * if `_` symbols replaced by directory path separator. File name consists of prefix folloed by last part in class name (i.e.
		 * symbols after last `_` in class name)
		 * When class has prefix it's source is looked in `models`, `controllers`, `shortcodes` folders, otherwise it looked in `core` or `library` folder
		 *
		 * @param string $className
		 * @return bool
		 */
		public function autoload($className) {

            if ( ! preg_match('/PMTI/m', $className) ) {
                return false;
            }

			$is_prefix = false;
			$filePath = str_replace('_', '/', preg_replace('%^' . preg_quote(self::PREFIX, '%') . '%', '', strtolower($className), 1, $is_prefix)) . '.php';
			if ( ! $is_prefix) { // also check file with original letter case
				$filePathAlt = $className . '.php';
			}
			foreach ($is_prefix ? ['models', 'controllers', 'shortcodes', 'classes'] : [] as $subdir) {
				$path = self::ROOT_DIR . '/' . $subdir . '/' . $filePath;
				if (is_file($path)) {
					require $path;
					return TRUE;
				}
				if ( ! $is_prefix) {
					$pathAlt = self::ROOT_DIR . '/' . $subdir . '/' . $filePathAlt;
					if (is_file($pathAlt)) {
						require $pathAlt;
						return TRUE;
					}
				}
			}

			return FALSE;
		}

		/**
		 * Get plugin option
		 * @param string[optional] $option Parameter to return, all  array of options is returned if not set
		 * @return mixed
		 */
		public function getOption($option = NULL) {
			if (is_null($option)) {
				return $this->options;
			} else if (isset($this->options[$option])) {
				return $this->options[$option];
			} else {
				throw new Exception("Specified option is not defined for the plugin");
			}
		}

		/**
		 * Update plugin option value
		 * @param string $option Parameter name or array of name => value pairs
		 * @param mixed[optional] $value New value for the option, if not set than 1st parameter is supposed to be array of name => value pairs
		 * @return array
		 */
		public function updateOption($option, $value = NULL) {
			is_null($value) or $option = [$option => $value];
			if (array_diff_key($option, $this->options)) {
				throw new Exception("Specified option is not defined for the plugin");
			}
			$this->options = $option + $this->options;
			update_option(get_class($this) . '_Options', $this->options);

			return $this->options;
		}

		/**
		 * Plugin activation logic
		 */
		public function activation() {

			// uncaught exception doesn't prevent plugin from being activated, therefore replace it with fatal error so it does
			set_exception_handler(function($e){trigger_error($e->getMessage(), E_USER_ERROR);});

			// create plugin options
			$option_name = get_class($this) . '_Options';
			$options_default = PMTI_Config::createFromFile(self::ROOT_DIR . '/config/options.php')->toArray();
			update_option($option_name, $options_default);

		}

		/**
		 * Method returns default import options, main utility of the method is to avoid warnings when new
		 * option is introduced but already registered imports don't have it
		 */
		public static function get_default_import_options() {
			return [
                'is_update_wpcs' => 1,
                'is_update_wpcs_relationships' => 1,
                'wpcs_groups' => [],
                'wpcs_fields' => [],
                'wpcs_relationships' => [],
                'wpcs_update_logic' => 'full_update',
                'wpcs_relationships_update_logic' => 'full_update',
                'wpcs_list' => [],
                'wpcs_relationships_list' => [],

                'wpcs_only_list' => [],
                'wpcs_relationships_only_list' => [],
                'wpcs_except_list' => [],
                'wpcs_relationships_except_list' => []
            ];
		}
	}

	PMTI_Plugin::getInstance();

	// retrieve our license key from the DB
	$wpai_toolset_addon_options = get_option('PMXI_Plugin_Options');

	// Favor new API URL, but fallback to old if needed.
	if( !empty($wpai_toolset_addon_options['info_api_url_new'])){
		$api_url = $wpai_toolset_addon_options['info_api_url_new'];
	}elseif( !empty($wpai_toolset_addon_options['info_api_url'])){
		$api_url = $wpai_toolset_addon_options['info_api_url'];
	}else{
		$api_url = null;
	}

	if (!empty($api_url)){
		// setup the updater
		$updater = new PMTI_Updater( $api_url, __FILE__, [
            'version' 	=> PMTI_VERSION,		// current version number
            'license' 	=> false, // license key (used get_option above to retrieve from DB)
            'item_name' => PMTI_Plugin::getEddName(), 	// name of this plugin
            'author' 	=> 'Soflyy'  // author of this plugin
        ]);
	}
}
