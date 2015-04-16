<?php  namespace Mayo505\Framvc\Lang;

class Lang implements LangInterface {
    protected $locale = "sk";
    protected $loadedFiles;
    protected $baseLangPath;

    public function setBaseLangPath($path) {
        $this->baseLangPath = $path;
    }

    public function setLocale($lang) {
        $this->locale = $lang;
    }

    public function get($key, $variables = array()) {
        list($path, $item) = $this->parseKey($key);
        $fullPath = $this->getFullPath($path);

        $this->load($fullPath);
        return $this->getItem($fullPath, $item, $variables);
    }

    public function getItem($fullPath, $item, $variables = array()) {
        $keys = explode(".", $item);

        if (!isset($this->loadedFiles[$fullPath][$this->locale])) {
            return $item;
        }

        $array = $this->loadedFiles[$fullPath][$this->locale];
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return $item;
            }
            if (!is_array($array[$key])) {
                return $this->replaceVariables($array[$key], $variables);
            }

            $array = $array[$key];
        }
    }

    public function parseKey($key) {
        $parts = explode(".", $key);
        $path = array_shift($parts);
        $item = implode(".", $parts);

        return [$path, $item];
    }

    public function load($fullPath) {
        if (isset($this->loadedFiles[$fullPath][$this->locale])) {
            return;
        }

        if (!file_exists($fullPath)) {
            return;
        }

        $values = include($fullPath);
        $this->loadedFiles[$fullPath][$this->locale] = $values;
    }

    public function replaceVariables($string, $variables) {
        foreach ($variables as $variable => $value) {
            $string = str_replace(":" . $variable, $value, $string);
        }
        return $string;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getFullPath($path) {
        return rtrim($this->baseLangPath, "/") . "/" . $this->locale . "/" . $path . ".php";
    }
}