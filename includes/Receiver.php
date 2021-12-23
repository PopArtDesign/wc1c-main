<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;

/**
 * Receiver
 *
 * @package Wc1c
 */
final class Receiver
{
	/**
	 * Receiver constructor.
	 */
	public function __construct()
	{
		// hook
		do_action(WC1C_PREFIX . 'receiver_before_loading');

		// run
		add_filter('parse_request', [$this, 'handleRequests']);

		// hook
		do_action(WC1C_PREFIX . 'receiver_after_loading');
	}

	/**
	 * Handle requests
	 */
	public function handleRequests()
	{
		$wc1c_receiver = wc1c()->getVar($_GET['wc1c-receiver'], false);

		wc1c()->log('receiver')->info(__('Receiver request start.', 'wc1c'));
		wc1c()->log('receiver')->debug(__('Receiver request params.', 'wc1c'), ['GET' => $_GET, 'POST' => $_POST, 'SERVER' => $_SERVER]);

		if(false !== $wc1c_receiver)
		{
			wc1c()->define('WC1C_RECEIVER_REQUEST', true);

			if('yes' !== wc1c()->settings()->get('receiver', 'yes'))
			{
				wc1c()->log('receiver')->warning(__('Receiver is offline. Request reject.', 'wc1c'));
				die(__('Receiver is offline. Request reject.', 'wc1c'));
			}

			try
			{
				$configuration = new Configuration($wc1c_receiver);
			}
			catch(Exception $e)
			{
				wc1c()->log('receiver')->warning(__('Input unavailable', 'wc1c'), ['exception' => $e]);
				die(__('Receiver is unavailable.', 'wc1c'));
			}

			wc1c()->environment()->set('current_configuration_id', $wc1c_receiver);

			if($configuration->getStatus() !== 'active' && $configuration->getStatus() !==  'processing')
			{
				wc1c()->log('receiver')->warning(__('Configuration offline.', 'wc1c'));
				die(__('Configuration offline.', 'wc1c'));
			}

			try
			{
				$configuration->setDateActivity(time());
				$configuration->save();
			}
			catch(Exception $e)
			{
				wc1c()->log('receiver')->error('Configuration save error.', ['exception' => $e]);
				die(__('Configuration offline.', 'wc1c'));
			}

			try
			{
				wc1c()->schemas()->init($configuration);
			}
			catch(Exception $e)
			{
				wc1c()->log('receiver')->error('Schema is not initialized.', ['exception' => $e]);
				die(__('Schema offline.', 'wc1c'));
			}

			$action = false;
			$wc1c_receiver_action = 'wc1c_receiver_' . $configuration->getSchema();

			if(has_action($wc1c_receiver_action))
			{
				$action = true;

				ob_start();
				nocache_headers();

				do_action($wc1c_receiver_action);

				ob_end_clean();
			}

			if(false === $action)
			{
				wc1c()->log('receiver')->warning(__('Receiver request is very bad! Action not found.', 'wc1c'), ['action' => $wc1c_receiver_action]);
				die(__('Receiver request is very bad! Action not found.', 'wc1c'));
			}
			die();
		}
	}
}