<?php

namespace WebFW\Router;
use Exception;

class Route
{
    protected $pattern;
    protected $patternRegex;
    protected $defaultController;
    protected $defaultAction;
    protected $controller;
    protected $action;
    protected $variableRules;
    protected $variables = array();

    public function __construct($pattern, array $defaults, array $variableRules = array()) {
        $this->pattern = $pattern;

        if (isset($defaults["controller"], $defaults["action"])) {
            $this->defaultController = $defaults["controller"];
            $this->defaultAction = $defaults["action"];

            $this->controller = $this->defaultController;
            $this->action = $this->defaultAction;
        } else {
            throw new Exception("Route must specify a default controller and action");
        }

        $this->variableRules = $variableRules;

        $this->parsePattern();
    }

    protected function parsePattern() {
        // Cleanup and trim out slashes
        $this->patternRegex = "/" . str_replace("/", "\/", $this->pattern) . "/";

        if (preg_match_all("/:([A-Za-z0-9_]+)/", $this->pattern, $variables, PREG_PATTERN_ORDER)) {
            foreach ($variables[1] as $name) {
                // First check a rule exists, or add one if the variable is the controller/action
                if (in_array($name, array("controller", "action"))) {
                    if (!isset($this->variableRules[":$name"])) {
                        $this->variableRules[":$name"] = "[A-Za-z]+";
                    }
                } else if (!isset($this->variableRules[":$name"])) {
                    throw new Exception("Missing variable rule for matched variable :$name");
                }

                // Replace the variable in the pattern regex
                $this->patternRegex = str_replace(":$name", "(?<$name>{$this->variableRules[":$name"]})", $this->patternRegex);

                // Set the default value for this variable
                if (isset($this->defaults[$name])) {
                    $this->variables[$name] = $this->defaults[$name];
                }
            }
        }
    }

    public function match($input) {
        $input = rtrim($input, "/");
        $input = (substr($input, 0, 1) != "/") ? "/$input" : $input;


        if (preg_match($this->patternRegex, $input, $variables) === 1) {
            // If we match we also need to check the length of the url, compared
            // to the length of the pattern - this prevents "/" matching everything
            // Create a new route for this, replacing vars with '?'
            $target = preg_replace("/:([A-Za-z0-9_]+)/", "?", $this->pattern);
            $inputBits = explode("/", $input);
            foreach (explode("/", $target) as $i => $n) {
                if ($n == "?") {
                    $inputBits[$i] = $n;
                }
            }
            if (strlen($target) !== strlen(implode("/", $inputBits))) {
                return false;
            }

            foreach ($variables as $name => $value) {
                if (!is_numeric($name)) {
                    $this->variables[$name] = $value;
                }
            }

            foreach (array("controller", "action") as $var) {
                if (isset($this->variables[$var])) {
                    $this->controller = $this->variables[$var];
                    unset($this->variables[$var]);
                }
            }

            return $this;
        } else {
            return false;
        }
    }

    public function getControllerName() {
        return $this->controller;
    }

    public function getActionName() {
        return $this->action;
    }

    public function getVariables() {
        return $this->variables;
    }

    public function getVariable($name) {
        return $this->variables[$name];
    }

    public function variableExists($name) {
        return isset($this->variables[$name]);
    }

    public function getPattern() {
        return $this->pattern;
    }

    public function getPatternRegex() {
        return $this->patternRegex;
    }
}
