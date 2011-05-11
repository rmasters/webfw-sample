<?php

namespace WebFW;

class View
{
	public static $defaultPath;
	protected $path;
	protected $variables;
	
	public function __construct($path, array $variables = null, $useDefaultPath = true) {
		$this->variables = array();
		
		if ($useDefaultPath && self::$defaultPath) {
			$this->path = self::$defaultPath . $path;
		} else {
			$this->path = $path;
		}
		
		if (!is_null($variables)) {
			foreach ($variables as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	public function __set($name, $value) {
		$this->variables[$name] = $value;
	}
	
	public function __get($name) {
		return $this->variables[$name];
	}
	
	public function __isset($name) {
		return isset($this->variables[$name]);
	}
	
	public function __unset($name) {
		unset($this->variables[$name]);
    }

    public function compile() {
        require $this->path;
        flush();
    }
	
	public function __toString() {
        $this->compile();

		$buffer = ob_get_contents();
		ob_clean();
		return $buffer;
	}
}
