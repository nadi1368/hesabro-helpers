<?php

namespace hesabro\helpers\widgets\grid;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\grid\GridView as KartikGridView;

/**
 * The GridView widget is used to display data in a grid.
 *
 */
class GridView extends KartikGridView
{
    /**
     * @var array the HTML attributes for the grid table element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
	public $tableOptions = ['class' => 'table table-striped text-center'];
    /**
     * @var array the HTML attributes for the container tag of the grid view.
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */

    public $options = ['class' => 'grid-view'];

    public $layout = "{summary}\n<div class='table-responsive mb-2'>{items}</div>{pager}";

    public $pager = [
        'options' => ['class' => 'pagination'],
        'prevPageLabel' => "<",
        'nextPageLabel' => ">",
        'firstPageLabel' => "<<",
        'lastPageLabel' => ">>",
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
        'maxButtonCount' => 5,
    ];

    public $dataColumnClass = DataColumn::class;

    public $responsive = false;

    public $responsiveWrap = false;

    public $resizableColumns = false;

    //public $toolbar = [];

	public $bordered = false;
	public $striped = false;

	public $headerContainer = [];

    public $showCustomToolbar = false;

    public $showCreateBtnAtToolbar = true;

    public $showDeleteBtnAtToolbar = true;

    public $showConfirmBtnAtToolbar = false;

    public $customToolbar = [];

    public $reloadPjaxContainer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->customToolbar)) {
            $this->customToolbar = [
                [
                    'content' =>
                        ($this->showCreateBtnAtToolbar ? (Html::a('<i class="far fa-plus"></i>', ['create'], [
                                'title' => Yii::t('app', 'New'),
                                'data' => [
                                    'toggle' => 'tooltip',
                                    'pjax' => '0',
                                ],
                                'class' => 'btn btn-success btn-outline mb-2'
                            ]) . ' ') : '') .
                        ($this->showDeleteBtnAtToolbar ? (Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt"]), 'javascript:void(0)',
                                [
                                    'id' => 'grid-delete-selected-btn',
                                    'title' => Yii::t('app', 'Remove selected row(s).'),
                                    'aria-label' => Yii::t('app', 'Remove selected row(s).'),
                                    'data-reload-pjax-container' => $this->reloadPjaxContainer,
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete-selected']),
                                    'class' => "btn btn-danger ml-1 mb-2 p-jax-btn",
                                    'data-title' => Yii::t('app', 'Remove selected row(s).'),
                                    'data-toggle' => 'tooltip',
                                    'data-method' => 'post'

                                ]) . ' ') : '') .
                        ($this->showConfirmBtnAtToolbar ? (Html::a(Html::tag('span', '', ['class' => "far fa-check"]), 'javascript:void(0)',
                                [
                                    'id' => 'grid-confirm-selected-btn',
                                    'title' => Yii::t('app', 'Confirm selected row(s).'),
                                    'aria-label' => Yii::t('app', 'Confirm selected row(s).'),
                                    'data-reload-pjax-container' => $this->reloadPjaxContainer,
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['confirm-selected']),
                                    'class' => "btn btn-success ml-1 mb-2 p-jax-btn",
                                    'data-title' => Yii::t('app', 'Confirm selected row(s).'),
                                    'data-toggle' => 'tooltip',
                                    'data-method' => 'post'

                                ]) . ' ') : ''),
                ]
            ];
        }

        $this->toolbar = $this->showCustomToolbar ? ArrayHelper::merge($this->customToolbar, $this->toolbar) : $this->toolbar;

        $this->columns = $this->showCustomToolbar ?
            ArrayHelper::merge(
                [
                    [
                        'class' => CheckboxColumn::class,
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return [
                                'class' => 'custom-control-input'
                            ];
                        }
                    ]
                ], $this->columns
            ) : $this->columns;


        Parent::init();

        if ($this->showCustomToolbar) {
            $view = $this->getView();
            $view->registerJs("jQuery('#grid-delete-selected-btn, .grid-modal').on('click', function(e){
            selectedIds = jQuery('#" . $this->options['id'] . "').yiiGridView('getSelectedRows');
            if(selectedIds.length > 0){
                href = jQuery(this).data('url');
                if(href.indexOf('?') != -1){
                    href = href.substr(0, href.indexOf('?'));
                }
                jQuery(this).data('url', href + '?selectedIds=' + selectedIds)
                jQuery(this).attr('href', href + '?selectedIds=' + selectedIds)
            }else{           
                swal({
                    title: 'هشدار',
                    text: '" . Yii::t('app', 'Select one or more row!') . "',
                    type: 'warning',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'بله'
                });
                return false;
            }
        })", View::POS_READY);
            $view->registerJs("jQuery('#grid-confirm-selected-btn, .grid-modal').on('click', function(e){
            selectedIds = jQuery('#" . $this->options['id'] . "').yiiGridView('getSelectedRows');
            if(selectedIds.length > 0){
                href = jQuery(this).data('url');
                if(href.indexOf('?') != -1){
                    href = href.substr(0, href.indexOf('?'));
                }
                jQuery(this).data('url', href + '?selectedIds=' + selectedIds)
                jQuery(this).attr('href', href + '?selectedIds=' + selectedIds)
            }else{           
                swal({
                    title: 'هشدار',
                    text: '" . Yii::t('app', 'Select one or more row!') . "',
                    type: 'warning',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'بله'
                });
                return false;
            }
        })", View::POS_READY);
        }
    }

    /**
     * @throws \Exception
     */
    public function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $columnKey => $column) {
            /* @var $column DataColumn */
            if(is_array($column->footer) && $column->footer) {
                $cells[0][$columnKey] = '<td></td>';
                foreach ($column->footer as $key => $item) {
                    $cells[$key][$columnKey] = $column->renderFooterCell($key);
                }
            } else {
                $cells[0][$columnKey] = $column->renderFooterCell();
            }
        }
        $content = '';
        ksort($cells);
        foreach ($cells as $cellKey => $cell) {
            if($cellKey != 0) {
                foreach ($cells[0] as $k => $c) {
                    if(!isset($cell[$k])) {
                        $cell[$k] = '<td></td>';
                    }
                }
            }
            ksort($cell);
            $content .= Html::tag('tr', implode('', $cell), $this->footerRowOptions);
        }
        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }
        return $this->renderTablePart('tfoot', $content);
    }
}
