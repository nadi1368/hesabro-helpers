<?php

namespace hesabro\helpers\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Class DocumentsDataBehavior
 * @package common\behaviors
 * @author Nader <nader.bahadorii@gmail.com>
 * @property ActiveRecord $owner
 */
class DocumentsDataBehavior extends \yii\base\Behavior
{
    public string $documentArrayField;
    public string $documentClass;
    public string $documentViewUrl;

    /**
     * @param int $documentId
     * @param string $documentTitle
     * @return bool
     */

    public function pushDocument(int $documentId, string $documentTitle): bool
    {
        if (is_array($this->owner->{$this->documentArrayField})) {
            foreach ($this->owner->{$this->documentArrayField} as $item) {
                if (array_key_exists($documentId, $item)) {
                    return true; // if exist
                }
            }
        } else {
            $this->owner->{$this->documentArrayField} = [];
        }
        $this->owner->{$this->documentArrayField}[] = [$documentId => $documentTitle];
        return $this->owner->save(false);
    }

    /**
     * @return bool
     * @throws \yii\db\StaleObjectException
     */
    public function popDocumentAndDelete(): bool
    {
        if (!is_array($this->owner->{$this->documentArrayField})) {
            return true;
        }
        $documentData = array_pop($this->owner->{$this->documentArrayField});
        $documentId = array_key_first($documentData);
        $document = ($this->documentClass)::findOne($documentId);
        if ($document !== null) {
            if (!$document->canDelete(false, false)) {
                $this->owner->addError("{$this->documentArrayField}", 'امکان حذف سند حسابداری وجود ندارد.');
                return false;
            }
            if (!$document->delete()) {
                $this->owner->addError("{$this->documentArrayField}", 'امکان حذف سند حسابداری وجود ندارد.');
                return false;
            }
        }
        return $this->owner->save(false);
    }

    /**
     * @return Document|null
     */
    public function popDocument()
    {
        if (!is_array($this->owner->{$this->documentArrayField})) {
            return null;
        }
        $documentArray = $this->owner->{$this->documentArrayField};
        $documentData = array_pop($documentArray);
        $documentId = array_key_first($documentData);
        return ($this->documentClass)::findOne($documentId);
    }

    /**
     * @param $key
     * @return bool
     */
    public function popDocumentWithKey($key)
    {
        if (!is_array($this->owner->{$this->documentArrayField})) {
            return false;
        }
        $this->owner->{$this->documentArrayField} = array_filter($this->owner->{$this->documentArrayField}, function ($v, $k) use ($key) {
            if (array_key_exists($key, $v)) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);

        return $this->owner->save(false);
    }

    /**
     * @return string
     */
    public function showDocument(): string
    {
        $data = '';
        if (!is_array($this->owner->{$this->documentArrayField})) {
            return '';
        }
        foreach ($this->owner->{$this->documentArrayField} as $item) {
            foreach ($item as $documentId => $documentTitle) {
                $data .= Html::a('مشاهده ' . $documentTitle, [$this->documentViewUrl, 'id' => $documentId], ['class' => 'btn btn-info ml-2 showModalButton', 'title' => $documentTitle . ' #' . $documentId, 'data-size' => 'modal-xl']);
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public function buttonDropdownDocument(array $items): array
    {

        if (is_array($this->owner->{$this->documentArrayField})) {
            foreach ($this->owner->{$this->documentArrayField} as $item) {
                foreach ($item as $documentId => $documentTitle) {
                    $items[] = [
                        'label' => Html::tag('i', ' ', ['class' => 'fa fa-file']) . ' ' . 'مشاهده ' . $documentTitle,
                        'url' => [$this->documentViewUrl, 'id' => $documentId],
                        'encode' => false,
                        'linkOptions' => [
                            'class' => 'showModalButton',
                            'title' => $documentTitle . ' #' . $documentId,
                            'data-size' => 'modal-xl'
                        ],
                    ];
                }
            }
        }

        return $items;
    }
}