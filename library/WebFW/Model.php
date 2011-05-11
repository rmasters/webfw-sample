<?php

namespace WebFW;

class Model
{
    public function __construct(array $params = null) {
        if (!is_null($params)) {
            foreach ($params as $key => $value) {
                $method = "set" . ucfirst($key);
                if (!method_exists($this, $method)) {
                    throw new Exception("Invalid property $key");
                } else {
                    $this->$method($value);
                }
            }
        }
    }
    
    public function toArray() {
        $toArray = function ($vars) use (&$toArray) {
            if (is_object($vars)) {
                if (method_exists($vars, "toArray")) {
                    $vars = $vars->toArray();
                } else {
                    $vars = (array) $vars;
                }
            }
            
            if (is_array($vars)) {
                foreach ($vars as $k => $v) {
                    $vars[$k] = $toArray($v);
                }
            }
            
            return $vars;
        };
        return $toArray(get_object_vars($this));
    }
}