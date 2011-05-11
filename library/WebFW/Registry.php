<?php

namespace WebFW;

class Registry implements \ArrayAccess
{
    private static $instance;
    protected $variables = array();

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
    }

    public function __set($name, $value) {
        $this->variables[$name] = $value;
    }

    public function __get($name) {
        if (isset($this->$name)) {
            return $this->variables[$name];
        } else {
            return null;
        }
    }

    public function __isset($name) {
        return isset($this->variables[$name]);
    }

    public function __unset($name) {
        unset($this->variables[$name]);
    }

    public static function set($name, $value) {
        self::getInstance()->$name = $value;
    }

    public static function get($name) {
        return self::getInstance()->$name;
    }

    public static function exists($name) {
        return isset(self::getInstance()->$name);
    }

    public static function remove($name) {
        unset(self::getInstance()->$name);
    }

    public function offsetSet($name, $value) {
        $this->$name = $value;
    }

    public function offsetGet($name) {
        return $this->$name;
    }

    public function offsetExists($name) {
        return isset($this->$name);
    }

    public function offsetUnset($name) {
        unset($this->$name);
    }
}
