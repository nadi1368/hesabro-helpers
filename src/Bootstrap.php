<?php

namespace hesabro\helpers;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;

class Bootstrap implements BootstrapInterface
{
    private array $requiredConfigs = [];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $config = $this->findConfig($app);

        if (!$config) {
            return;
        }

        $app->params['bsVersion'] = 4;

        $configs = require __DIR__ . '/config/main.php';

        $this->setConfig($app, $configs['components'] ?? [], 'components');
        $this->setConfig($app, $configs['modules'] ?? [], 'modules');
    }

    private function findConfig(Application $app): array|bool
    {
        $moduleConfig = current(array_filter($app->modules, fn($i) => ($i['class'] ?? '') === Module::class));

        if (!$moduleConfig) {
            return false;
        }

        foreach ($this->requiredConfigs as $requiredConfig) {
            if (!isset($moduleConfig[$requiredConfig])) {
                throw new InvalidConfigException(Module::class .": '$requiredConfig' must configure in module setup");
            }
        }

        return $moduleConfig;
    }

    private function setConfig(Application $app, array $items, string $target): void
    {
        foreach ($items as $item => $config) {

            $notExist = !current(array_filter($app->$target, fn($i) => ($i['class'] ?? 'unknown') === $config['class'] ?? ''));

            $method = 'set' . ucfirst($target);

            if ($notExist) {
                $app->$method([ $item => $config ]);
            }
        }
    }
}
