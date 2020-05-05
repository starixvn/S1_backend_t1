<?php
/**
 * For http unit testing.
 *
 * History:
 * 0.0.1 Elvis_Chen Initial file.
 *
 * @author Elvis_Chen<elvis.chen@tripleonetech.net>
 * @version 0.0.1
 * @since 0.0.1 Elvis_Chen Initial file.
 *
 */
require_once dirname(dirname(__FILE__)) . '/BaseController.php';

class TestController extends BaseController {
	protected $_checkAccessPermission = TRUE;

	public function _remap($method, $segments){
		$RTR =& load_class('Router', 'core');
		$CI = $this;

		$class = get_class($CI);

		// is_callable() returns TRUE on some versions of PHP 5 for private and protected
		// methods, so we'll use this workaround for consistent behavior
		if ( ! in_array(strtolower($method), array_map('strtolower', get_class_methods($CI))))
		{
			// Check and see if we are using a 404 override and use it.
			if ( ! empty($RTR->routes['404_override']))
			{
				$x = explode('/', $RTR->routes['404_override']);
				$class = $x[0];
				$method = (isset($x[1]) ? $x[1] : 'index');
				if ( ! class_exists($class))
				{
					if ( ! file_exists(APPPATH.'controllers/'.$class.'.php'))
					{
						show_404("{$class}/{$method}");
					}

					include_once(APPPATH.'controllers/'.$class.'.php');
					unset($CI);
					$CI = new $class();
				}
			}
			else
			{
				show_404("{$class}/{$method}");
			}
		}

		if($this->_checkAccessPermission){
			if(!$this->checkAccessPermission()){
				show_404("{$class}/{$method}");
			}
		}

		// Call the requested method.
		// Any URI segments present (besides the class/function) will be passed to the method for convenience
		call_user_func_array(array($CI, $method), $segments);
	}

	/**
	 * check access permission
	 *
	 * @return void
	 */
	public function checkAccessPermission(){
		$environment = $this->config->item('RUNTIME_ENVIRONMENT');

		return preg_match('/local-?.*/', $environment);
	}
}