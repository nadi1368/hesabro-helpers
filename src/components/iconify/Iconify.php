<?php

namespace hesabro\helpers\components\iconify;

use Exception;
use Yii;
use yii\helpers\Html;

class Iconify
{
    private static $instance = null;

    private $loadedIconSet = [];

    private function  __construct() { }

    private function  __clone() { }

    final public static function getInstance(): self
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    private function getIconSet($iconSet): array
    {
        if (isset($this->loadedIconSet[$iconSet])) {
            return $this->loadedIconSet[$iconSet];
        }

        $icons = [];
        $file = __DIR__ . "/icons/$iconSet.json";
        if (is_file($file)) {
            try {
                $content = file_get_contents($file);
                $content = json_decode($content, true);
                $icons = $content['icons'] ?? [];
            } catch (Exception $e) {
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            }

            if (count($icons)) {
                $this->loadedIconSet[$iconSet] = $icons;
            }
        }

        return $icons;
    }

    /** @var array $options It
     */
    public function icon(string $name, string $htmlClass = '', array $options = []): string
    {
        if (!str_contains($name, ':')) {
            return '';
        }

        [$iconSet, $iconName] = explode(':', $name);
        $icons = $this->getIconSet($iconSet);
        $icon = trim(($icons[$iconName]['body'] ?? ''));
        return str_starts_with($icon, '<svg') ? $icon : Html::tag('svg', $icon, [
            'xmlns' => 'http://www.w3.org/2000/svg',
            'width' => '1em',
            'height' => '1em',
            'class' => $htmlClass,
            'viewBox' => '0 0 256 256',
            ...$options
        ]);
    }
}