<?php

namespace WebFW;
use Exception;

class Loader
{
    private static $instance;
    
    private $namespaces = array();
    private $fallbackNamespaces = array();
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    private function __construct() {}
    
    /**
     * Provide a path to the root of the namespace.
     * E.g. 'WebFW' => /library/WebFW/, not /library/
     */
    public function addNamespace($namespace, $path) {
        $this->namespaces[$namespace] = $path;
    }
    
    public function addNamespaces(array $namespaces) {
        foreach ($namespaces as $ns => $path) {
            $this->addNamespace($ns, $path);
        }
    }
    
    public function register($prepend = false) {
        spl_autoload_register(array($this, "loadClass"), true, $prepend);
    }
    
    public static function loadClass($class) {
        $instance = self::getInstance();
        
        $namespace = substr($class, 0, strrpos($class, "\\"));
        
        // Find best match
        $best = null;
        foreach (array_keys($instance->namespaces) as $ns) {
            if (strpos($namespace, $ns) === 0) {
                // If this is the first, or a better match, use it
                if (!isset($best) || strlen($best) < strlen($ns)) $best = $ns;
                // If this is an identical match stop searching
                if ($ns == $namespace) break;
            }
        }
        
        // If we haven't found a best match use a fallback
        if (!isset($best)) {
            foreach ($instance->fallbackNamespaces as $fallbackPath) {
                if (file_exists(($path = $fallbackPath . "/" . self::getPath($class)))) {
                    require_once $path;
                    return;
                }                    
            }
            throw new Exception("Unable to find $class");
        }
        
        // Lose the root namespace on the class, unless it is a fallback
        // This is to allow for namespace -> folder mapping, e.g.:
        // Sample\Controller\A => application/Controller/A.php
        $class = substr($class, strpos($class, "\\") + 1);
        $path = $instance->namespaces[$best] . "/" . self::getPath($class);
        if (file_exists($path)) {
            require_once $path;
            return;
        }
        throw new Exception("Unable to find $class in {$instance->namespaces[$best]}");
    }
    
    public static function getPath($class) {
        return str_replace(array("_", "\\"), "/", $class) . ".php";
    }
}