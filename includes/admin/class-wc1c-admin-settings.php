<?php
/**
 * Settings class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Settings extends Wc1c_Admin_Abstract_Form
{
	/**
	 * Wc1c_Admin_Settings constructor
	 *
	 * @param bool $init
	 */
	public function __construct($init = true)
	{
		/**
		 * Form id
		 */
		$this->set_id('settings');

		/**
		 * Auto init
		 */
		if($init)
		{
			$this->init();
		}
	}

	/**
	 * Initialized
	 */
	public function init()
	{
		/**
		 * Init fields
		 */
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_main'), 10);
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_enabled_data'), 20);
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_technical'), 30);
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_uninstall'), 40);

		/**
		 * Load form fields
		 */
		$this->load_fields();

		/**
		 * Load form saved data
		 */
		$this->load_saved_data();

		/**
		 * Form show
		 */
		add_action('wc1c_admin_' . $this->get_id() . '_form_show', array($this, 'output'), 10);

		/**
		 * Form save
		 */
		$this->save();
	}

	/**
	 * Form show
	 */
	public function output()
	{
		echo '<form method="post" action="">';
		echo wp_nonce_field('wc1c-admin-settings-save', '_wc1c-admin-nonce');
		echo '<table class="form-table wc1c-admin-form-table wc1c-admin-settings-form-table">';
		$this->generate_html($this->get_fields(), true);
		echo '</table>';
		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="' . __('Save settings', 'wc1c') . '"></p>';
		echo '</form>';
	}

	/**
	 * Save form data
	 *
	 * @return bool
	 */
	public function save()
	{
		/**
		 * Post data
		 */
		$post_data = $this->get_posted_data();

		/**
		 * Data from form available
		 */
		if(!isset($post_data['_wc1c-admin-nonce']))
		{
			return false;
		}

		/**
		 * Security
		 */
		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce'], 'wc1c-admin-settings-save'))
		{
			/**
			 * Show error in admin header
			 */
			WC1C_Admin()->add_message('error', __('Save error. Please retry.', 'wc1c'));

			/**
			 * Break
			 */
			return false;
		}

		/**
		 * All form fields validate
		 */
		foreach($this->get_fields() as $key => $field)
		{
			/**
			 * Title break
			 */
			if('title' === $this->get_field_type($field))
			{
				continue;
			}

			/**
			 * Validate
			 */
			try {
				$this->saved_data[$key] = $this->get_field_value($key, $field, $post_data);
			} catch(Exception $e) {
				WC1C_Admin()->add_message('error', $e->getMessage());
			}
		}

		/**
		 * Saving
		 */
		$saved = WC1C()->save_settings($this->get_saved_data());

		/**
		 * Show admin messages
		 */
		if($saved)
		{
			WC1C_Admin()->add_message('update', __('Saving settings success.', 'wc1c'));
		}
		else
		{
			WC1C_Admin()->add_message('error', __('Saving settings error. Please retry saving or change settings.', 'wc1c'));
		}

		return true;
	}

	/**
	 * Loading saved data
	 *
	 * @param array $saved_data
	 */
	public function load_saved_data($saved_data = [])
	{
		$saved_data = WC1C()->get_settings();

		if(!is_array($saved_data))
		{
			$form_fields = $this->get_fields();
			$saved_data = array_merge
			(
				array_fill_keys(array_keys($form_fields), ''),
				wp_list_pluck($form_fields, 'default')
			);
		}

		$this->set_saved_data(apply_filters('wc1c_admin_' . $this->get_id() . '_form_load_saved_data', $saved_data));
	}

	/**
	 * Init fields for main settings
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_main($fields)
	{
		$fields['main'] = array
		(
			'title' => __('Main', 'wc1c'),
			'type' => 'title',
			'description' => __('Basic options. After the options change, must save it.', 'wc1c'),
		);

		$fields['enable'] = array
		(
			'title' => __('Online / Offline', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enabling and disabling all exchange mechanisms. If the checkbox is not checked, then all mechanisms for changing any data are disabled.', 'wc1c'),
			'description' => __('This setting is used to ensure that data changes in the site database are disabled.', 'wc1c'),
			'default' => 'no'
		);

		return $fields;
	}


	/**
	 * Add settings for enabled data
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_enabled_data($fields)
	{
		$fields['change_data_objects'] = array
		(
			'title' => __('Data change by objects', 'wc1с'),
			'type' => 'title',
			'description' => __('Specifying the ability to work with changing data by object types (data types).', 'wc1c'),
		);

		$fields['change_data_products'] = array
		(
			'title' => __('Enable changing products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enabling and disabling capabilities of the products. If the checkbox is not checked, it means that all mechanisms for working with products are disabled.', 'wc1c'),
			'description' => __('This setting is used as a global flag indicating the ability to work with products (delete, change, add).', 'wc1c'),
			'default' => 'yes'
		);

		$fields['change_data_orders'] = array
		(
			'title' => __('Enable changing orders', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enabling and disabling of features of work with orders. If the checkbox is not checked, it means that all mechanisms for working with orders are disabled.', 'wc1c'),
			'description' => __('This setting is used as a global flag indicating that you can work with product orders (delete, change, add).', 'wc1c'),
			'default' => 'yes'
		);

		$fields['change_data_images'] = array
		(
			'title' => __('Enable changing images', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enabling and disabling image processing capabilities. If the checkbox is not checked, it means that all mechanisms for working with images are disabled.', 'wc1c'),
			'description' => __('This setting is used as a global flag indicating work with product images (delete, change, add).', 'wc1c'),
			'default' => 'yes'
		);

		return $fields;
	}

	/**
	 * Add settings for technical
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_technical($fields)
	{
		$fields['technical'] = array
		(
			'title' => __('Technical parameters', 'wc1с'),
			'type' => 'title',
			'description' => __('Used by technical specialists. Can leave it at that.', 'wc1c'),
		);

		$fields['logger'] = array
		(
			'title' => __('Logging level', 'wc1c'),
			'type' => 'select',
			'description' => __('You can enable logging, specify the level of error that you want to benefit from logging. You can send reports to developer manually by pressing the button. All sensitive data in the report are deleted. By default, the error rate should not be less than ERROR.', 'wc1c'),
			'default' => '400',
			'options' => array
			(
				'' => __('Off', 'wc1c'),
				'100' => __('DEBUG', 'wc1c'),
				'200' => __('INFO', 'wc1c'),
				'250' => __('NOTICE', 'wc1c'),
				'300' => __('WARNING', 'wc1c'),
				'400' => __('ERROR', 'wc1c'),
				'500' => __('CRITICAL', 'wc1c'),
				'550' => __('ALERT', 'wc1c'),
				'600' => __('EMERGENCY', 'wc1c')
			)
		);

		$fields['upload_directory_name'] = array
		(
			'title' => __('Name of upload directory', 'wc1c'),
			'type' => 'text',
			'description' => __('You need to change the name of the standard directory for security.', 'wc1c'),
			'default' => 'wc1c',
			'css' => 'min-width: 300px;',
		);

		$fields['enable_api'] = array
		(
			'title' => __('API Online / Offline', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable or disable receiving requests via the API. If the checkbox is not checked, then all mechanisms for receiving requests via the API are disabled.', 'wc1c'),
			'description' => __('This API uses exchange schemes to receive requests from 1C and send data there.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['enable_extensions'] = array
		(
			'title' => __('Extensions', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable or disable loading extensions.', 'wc1c'),
			'description' => __('Regulates the loading of extensions. By default, loading extensions is enabled.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['external_schemas'] = array
		(
			'title' => __('External schemas', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enabling and disabling schemas from third-party developers. If the checkbox is not checked, then all mechanisms for adding external schemas are disabled.', 'wc1c'),
			'description' => __('Schemas are added to support different ways of exchanging data. Each scheme has its own processing algorithms and unique features.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['external_tools'] = array
		(
			'title' => __('External tools', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enabling and disabling third-party tools. If the checkbox is not checked, then all mechanisms for adding external tools are disabled.', 'wc1c'),
			'description' => __('Tools can be added by exchange schemes, as well as simply go as third-party plugins. Tools are added for ease of working with data.', 'wc1c'),
			'default' => 'yes'
		);

		return $fields;
	}

	/**
	 * Add settings for Uninstall parameters
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_uninstall($fields)
	{
		$fields['uninstall'] = array
		(
			'title' => __('Uninstall parameters', 'wc1с'),
			'type' => 'title',
			'description' => __('Used by technical specialists. Can leave it at that.', 'wc1c'),
		);

		$fields['uninstall_remove_files'] = array
		(
			'title' => __('Remove files', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Deleting all files in their file system. If the checkbox is not checked, then the files will not be deleted from the /wp-content/uploads directory', 'wc1c'),
			'description' => __('Deleting files is disabled by default. It is best to delete files via FTP', 'wc1c'),
			'default' => 'no'
		);

		$fields['uninstall_remove_settings'] = array
		(
			'title' => __('Remove settings', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Deleting all the main plugin settings. If the checkbox is not checked, then the plugin settings will remain in the site database.', 'wc1c'),
			'description' => __('Deletion is disabled by default. Cleaning all the basic settings when finally deleting the plugin.', 'wc1c'),
			'default' => 'no'
		);

		$fields['uninstall_remove_tables'] = array
		(
			'title' => __('Remove tables', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Deleting all the main plugin tables. If the checkbox is not checked, then the plugin tables will remain in the site database.', 'wc1c'),
			'description' => __('Deletion is disabled by default. Cleaning all the basic tables when finally deleting the plugin.', 'wc1c'),
			'default' => 'no'
		);

		return $fields;
	}
}