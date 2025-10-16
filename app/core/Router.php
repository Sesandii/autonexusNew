<?php
namespace app\core;
class Router {
  private array $routes = ['GET'=>[], 'POST'=>[]];
  private array $config;
  public function __construct(array $config){ $this->config = $config; }
  public function get(string $pattern, $handler){ $this->add('GET',$pattern,$handler); }
  public function post(string $pattern, $handler){ $this->add('POST',$pattern,$handler); }
  private function add(string $method, string $pattern, $handler){
    $pattern = rtrim($pattern,'/') ?: '/';
    $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#','(?P<$1>[^/]+)', $pattern);
    $regex = '#^' . $regex . '$#';
    $this->routes[$method][] = ['regex'=>$regex, 'handler'=>$handler];
  }
  public function dispatch(){
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    $uri    = rtrim($uri,'/') ?: '/';
    foreach ($this->routes[$method] ?? [] as $route){
      if (preg_match($route['regex'], $uri, $matches)){
        $params = array_filter($matches,'is_string',ARRAY_FILTER_USE_KEY);
        return $this->invoke($route['handler'], $params);
      }
    }
    http_response_code(404); echo '404 Not Found';
  }
  private function invoke($handler, array $params){
    if (is_callable($handler)) return call_user_func_array($handler, $params);
    if (is_array($handler) && count($handler)===2){
      [$class, $method] = $handler;
      $controller = new $class($this->config);
      return call_user_func_array([$controller, $method], $params);
    }
    throw new \RuntimeException('Invalid route handler.');
  }
}
