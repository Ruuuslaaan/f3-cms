<?php
declare(strict_types=1);

namespace App;

use Base;
use ComposerAutoloaderInit4fafe4f50ca85dfbc9a48b7d52914875;
use RuntimeException;
use function array_map;
use function dirname;
use function glob;

final class Bootstrap
{
    /**
     * @var Base
     */
    private $f3;

    public function run(): void
    {
        $this->initF3()
            ->registerModules()
            ->initRoutes()
            ->initView();

        $this->f3->run();
    }

    /**
     * @param string $globPattern
     * @return array
     */
    private function readGlob(string $globPattern): array
    {
        $baseDir = dirname(__DIR__) . '/';

        $files = glob($baseDir . $globPattern, GLOB_NOSORT);
        if ($files === false) {
            throw new RuntimeException("glob(): error with '$baseDir$globPattern'");
        }

        return $files;
    }

    /**
     * @return self
     */
    private function initF3(): self
    {
        $this->f3 = Base::instance();
        return $this;
    }

    /**
     * @return self
     */
    private function registerModules(): self
    {
        array_map(
            static function (string $file) {
                $registration = require $file;
                ComposerAutoloaderInit4fafe4f50ca85dfbc9a48b7d52914875::getLoader()
                    ->addPsr4($registration, dirname($file));
            },
            $this->readGlob('app/modules/*/*/registration.php')
        );

        return $this;
    }

    /**
     * @return self
     */
    private function initRoutes(): self
    {
        $that = $this;
        array_map(
            static function (string $file) use ($that){
                $that->f3->config($file);
            },
            $this->readGlob('app/modules/*/*/etc/routes.ini')
        );

        return $this;
    }

    /**
     * @return self
     */
    private function initView(): self
    {
        $viewDirectories = $this->readGlob('app/modules/*/*/view/');
        $viewDirectories[] = __DIR__ . '/view/';
        $this->f3->set('UI', implode(';', $viewDirectories));

        return $this;
    }
}
