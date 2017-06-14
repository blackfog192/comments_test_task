<?php

/**
* 
*/
class Router {
	
	private $routes;

	function __construct() {
		// Load all routes
		$routesPath = ROOT.'/config/routes.php';
		$this->routes = include($routesPath);
	}

	/**
	* Returns request string
	*/
	private function getURI() {
		if(!empty($_SERVER['REQUEST_URI'])) {
			return trim($_SERVER['REQUEST_URI'], '/');
		} 
	}

	public function run() {
		// Get request uri
		$uri = $this->getURI();
		
		// Check for a request in routes.php
		foreach ($this->routes as $uriPattern => $path) {
			// Compare $uriPattern and $path
			if(preg_match("~$uriPattern~", $uri)) {
				// Getting the inner path
				$iternalRoute = preg_replace("~$uriPattern~", $path, $uri);

				// Determine which controller and 
				// action the request is made of.
				$segments = explode('/', $iternalRoute);
				$controllerName = array_shift($segments).'Controller';
				$controllerName = ucfirst($controllerName);

				$actionName = 'action'. ucfirst(array_shift($segments));

				// Connecting the controller file
				$controllerFile = ROOT. '/controllers/' . $controllerName . '.php';
				if(file_exists($controllerFile)) {
					include_once($controllerFile);
				} else {
					// 404 error
					echo '<center><h3>404 Error</h3><br>Page not found</center>';
					break;
				}

				// Create object and call method
				$controllerObject = new $controllerName;
				if(method_exists($controllerObject, $actionName)) {
					$result = call_user_func_array(array($controllerObject, $actionName), $segments);
					if($result != null) {
						break;
					}
				}
			}
		}
	}
}

?>
