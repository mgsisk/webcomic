<?php
/**
 * WordPress plugin and theme framework.
 * 
 * As an abstract class, the framework cannot
 * be directly instantiated; it must be extended
 * by another class that defines the actual
 * plugin or theme.
 */
abstract class mgs_core {
	/** Variables used internally by the framework. */
	protected $name, $version, $file, $type;
	static $base, $cdir, $curl, $dir, $url, $update, $errors, $options;
	
	/**
	 * Initialize the framework.
	 * 
	 * The following class variables should be redefined
	 * by any class that directly extends the framework:
	 * 
	 * 'name'    = The name of the plugin or theme. Should be slug-like,
	 *             with no spaces or special characters.
	 * 'version' = The version of the plugin or theme. Defaults to the
	 *             framework version.
	 * 'file'    = Full path and filename of the main plugin or theme file.
	 *             Use of the PHP magic constant __FILE__ is encouraged.
	 * 'type'    = One of 'plugin' or 'theme'. Defaults to 'plugin'.
	 * 
	 * These class variables are static and cannot be redefined:
	 * 
	 * 'base'    = The filename (for plugins) or directory name (for themes).
	 * 'cdir'    = Absolute path to the WordPress content directory.
	 * 'curl'    = URL to the WordPress content directory.
	 * 'dir'     = Absolute path to the plugin or theme directory.
	 * 'url'     = URL to the plugin or theme directory.
	 * 'options' = Plugin or theme options. Use the 'option' method to interact with this variable (see below).
	 * 
	 * The 'errors' and 'update' variables are intended for storing
	 * notifications presented to users in the adminsitrative panel.
	 * It's up to the individual plugin or theme to decide how to
	 * best utilize these varaibles.
	 * 
	 * Hooks and shortcodes can be added automatically be prefixing
	 * function names with either "hook_" and the name of the hook
	 * or "short_" and the name to be used for the new shortcode.
	 * You can specifiy hook priority by ending function names
	 * with a number, like:
	 * 
	 * function hook_the_content_42() { ... }
	 * 
	 * @package mgs_core
	 * @version 1
	 */
	final public function __construct() {
		if ( !$this->name )    $this->name    = 'mgs_core';
		if ( !$this->version ) $this->version = 1;
		if ( !$this->file )    $this->file    = __FILE__;
		if ( !$this->type )    $this->type    = 'plugin';
		
		$upload = wp_upload_dir();
		
		$this->base    = ( 'plugin' == $this->type ) ? plugin_basename( $this->file ) : dirname( $this->file );
		$this->cdir    = $upload[ 'basdir' ];
		$this->curl    = $upload[ 'baseurl' ];
		$this->update  = array();
		$this->errors  = array();
		$this->options = $this->option();
		
		if ( 'plugin' == $this->type ) {
			$this->dir = ( realpath( dirname( $this->file ) ) !== realpath( WPMU_PLUGIN_DIR ) ) ? trailingslashit( WP_PLUGIN_DIR ) . dirname( $this->base ) : WPMU_PLUGIN_DIR;
			$this->url = ( realpath( dirname( $this->file ) ) !== realpath( WPMU_PLUGIN_DIR ) ) ? trailingslashit( WP_PLUGIN_URL ) . str_replace( '/' . basename( $this->file ), '', $this->base ) : trailingslashit( WPMU_PLUGIN_URL ) . str_replace( '/' . basename( $this->file ), '', $this->base );
		} else {
			$this->dir = TEMPLATEPATH;
			$this->url = get_template_directory_uri();
		}
		
		$class   = new ReflectionClass( get_class( $this ) );
		$methods = $class->getMethods();
		
		foreach ( $methods as $method ) {
			if ( 0 === strpos( $method->name, 'hook_' ) ) {
				$hook = substr( $method->name, 5 );
				$prio = array_pop( explode( '_', $hook ) );
				
				if ( is_numeric( $prio ) )
					$hook = substr( $hook, 0, - ( strlen( '_' . $prio ) ) );
				else
					$prio = 10;
				
				add_filter( $hook, array( &$this, $method->name ), $prio, $method->getNumberOfParameters() );
			} elseif ( 0 === strpos( $method->name, 'short_' ) ) {
				$short = substr( $method->name, 6 );
				add_shortcode( $short, array( &$this, $method->name ) );
			}
		} unset( $class, $methods, $hook, $prio );
		
		if ( empty( $this->options ) )
			add_filter( 'init', array( &$this, 'install' ) );
		elseif ( $this->options[ 'version' ] < $this->version )
			add_filter( 'init', array( &$this, 'upgrade' ) );
		elseif ( $this->options[ 'version' ] > $this->version )
			add_filter( 'init', array( &$this, 'downgrade' ) );
		elseif ( !empty( $this->options[ 'uninstall' ] ) && 'plugin' == $this->type )
			register_deactivation_hook( $this->file, array( &$this, 'deactivate' ) );
	}
	
	/**
	 * Deactivation function for plugins.
	 * 
	 * Deletes plugin options when the plugin is deactivated.
	 * The uninstall function (which classes using the
	 * framework must define) should remove any additional
	 * information, as well as add the 'uninstall' option
	 * which triggers the deactivation hook.
	 * 
	 * @package mgs_core
	 * @since 1
	 */
	final public function deactivate( $t = false ) {
		$this->option( null );
	}
	
	/**
	 * Load text domain for translations.
	 * 
	 * Plugins need to call this function anytime
	 * strings can be translated in a function. Themes
	 * should usually call this function once in a
	 * hook_after_setup_theme() function.
	 * 
	 * @package mgs_core
	 * @since 1
	 */
	final public function domain() {
		if ( 'plugin' == $this->type && realpath( dirname( $this->file ) ) !== realpath( WPMU_PLUGIN_DIR ) )
			load_muplugin_textdomain( $this->name, $this->dir . '/' . $this->name . '-includes/languages' );
		elseif ( 'plugin' == $this->type )
			load_plugin_textdomain( $this->name, $this->dir . '/' . $this->name . '-includes/languages', dirname( $this->base ) );
		elseif ( 'theme' == $this->type ) {
			load_theme_textdomain( $this->name, $this->dir . '/' . 'languages' );
			
			$l  = get_locale();
			$lf = $this->dir . "/languages/$l.php";
			
			if ( is_readable( $lf ) ) require_once( $lf );
		} else
			return false;
	}
	
	/**
	 * Add, retrieve, update, or delete an option or options.
	 * 
	 * Options are stored in a single database entry as an array
	 * and loaded into the framework $options variable during
	 * initialization.
	 * 
	 * @package mgs_core
	 * @since 1
	 * 
	 * @param str|arr|null The name of the option to return, an array of new options to save, or null (deletes all existing options).
	 * @param str|null The new value for the option specified in $o, or null to delete the option.
	 * @return Return all options, the specified options, true when updating options, or false on error.
	 */
	final public function option( $o = false, $v = false ) {
		if ( false === $o ) {
			return get_option( $this->name . '_options', array() );
		} elseif ( null === $o ) {
			$this->options = $o;
			return delete_option( $this->name . '_options' );
		} elseif ( is_array( $o ) ) {
			$this->options = $o;
			return update_option( $this->name . '_options', $o );
		} elseif ( array_key_exists( $o, $this->options ) ) {
			if ( false === $v )
				return $this->options[ $o ];
			elseif ( null === $v )
				unset ( $this->options[ $o ] );
			else
				$this->options[ $o ] = $v;
			
			return update_option( $this->name . '_options', $this->options );
		}
		
		return false;
	}
	
	/**
	 * Abstract functions that must be defined.
	 * 
	 * These abstract functions must be defined in any class
	 * extending the framework, even if they're unused:
	 * 
	 * Install is a run-once function that should, at the very least,
	 * set plugin or theme options with a 'version' key.
	 * 
	 * Upgrade is run if the class $version is greater than the version
	 * stored in the plugin or theme options.
	 * 
	 * Downgrade is run if the class $version is less than the version
	 * stored in the plugin or theme options.
	 * 
	 * Uninstall must be called by another class method and should, at the
	 * very least, set the 'uninstall' option to true.
	 * 
	 * @package mgs_core
	 * @since 1
	 */
	abstract public function install();
	abstract public function upgrade();
	abstract public function downgrade();
	abstract public function uninstall();
}
?>