<?php

namespace hesabro\helpers\traits;

use Yii;

trait MenuHelper
{
    /**
     * Get searchable items recursively
     *
     * @param array $items
     * @param bool $main
     * @param array $result
     * @return array
     */
    public function toSearchDataRecursively($items, $main = true, &$result = [])
    {
        foreach ($items as $item) {
            if (isset($item['url']) && isset($item['label'])) {
                $result[Yii::$app->urlManager->createUrl($item['url'])] = $item['label'];
            }
            if (!empty($item['items'])) {
                $this->toSearchDataRecursively($item['items'], false, $result);
            }
        }

        if ($main) return $result;
    }

    /**
     * Get select2 convertions
     *
     * @return array
     */
    public function searchData(): array
    {
        return $this->toSearchDataRecursively($this->items());
    }
}
