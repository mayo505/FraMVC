<?php  namespace Mayo505\Framvc\Providers;

class LangProvider extends Provider {

    public function register() {
        $this->container->shared("Mayo505\Framvc\Lang\LangInterface", function($container) {
            $lang = $container->make("Mayo505\Framvc\Lang\Lang");
            $lang->setLocale("sk");
            $lang->setBaseLangPath(appRootPath() . "App/Lang/");
            return $lang;
        });
    }
}