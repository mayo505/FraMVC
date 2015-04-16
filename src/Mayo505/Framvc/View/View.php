<?php  namespace Mayo505\Framvc\View;

use Mayo505\Framvc\View\Exceptions\InvalidViewPathException;
use Mayo505\Framvc\View\Exceptions\ViewException;

class View implements ViewInterface {

    protected static $baseViewPath;
    protected $attributes = [];
    protected $path;
    protected $parent = null;
    protected $currentSection = null;
    protected $sections = [];

    public function __construct($path, array $attributes = array()) {
        $this->path = $path;
        $this->with($attributes);
    }

    public function with($attributes, $value = null) {
        if (is_array($attributes)) {
            $this->attributes = array_merge($this->attributes, $attributes);
            return $this;
        }
        $this->attributes[$attributes] = $value;
        return $this;
    }

    public static function setViewBasePath($path) {
        self::$baseViewPath = $path;
    }

    public function render() {
        $path = $this->getAbsolutePath($this->path);
        if (!file_exists($path)) {
            throw new InvalidViewPathException("Given path for view does not exists: " . $path);
        }
        extract($this->attributes);
        require($path);
    }

    public function extend($path) {
        if ($this->parent != null) {
            throw new ViewException("You can extend only one template");
        }
        $this->parent = new View($path);
    }

    public function endExtend() {
        if ($this->parent == null) {
            throw new ViewException("Parent view has not been defined");
        }

        $this->parent->render();
    }

    public function startSection($name) {
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection() {
        if ($this->currentSection == null) {
            throw new ViewException("Section haven't been started");
        }
        if ($this->parent == null) {
            throw new ViewException("Parent view has not been defined");
        }

        $html = ob_get_clean();
        $this->parent->setSection($this->currentSection, $html);
    }

    public function setSection($sectionName, $sectionHtml) {
        $this->sections[$sectionName] = $sectionHtml;
    }

    public function section($sectionName) {
        if (!isset($this->sections[$sectionName])) {
            throw new ViewException("Section $sectionName was not set");
        }

        echo $this->sections[$sectionName];
    }

    public function includeView($path, $attributes = array()) {
        $view = (new View($path))->with($attributes);
        $view->render();
    }

    /**
     * @return string
     */
    protected function getAbsolutePath($path) {
        if (self::$baseViewPath === null) {
            $absolutePath = $path;
        } else {
            $absolutePath = rtrim(self::$baseViewPath, "/") . "/" . $path;
        }

        return $absolutePath;
    }
}

