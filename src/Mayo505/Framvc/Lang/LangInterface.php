<?php  namespace Mayo505\Framvc\Lang;

interface LangInterface {
    public function get($key, $values);
    public function setLocale($locale);
    public function setBaseLangPath($path);
}