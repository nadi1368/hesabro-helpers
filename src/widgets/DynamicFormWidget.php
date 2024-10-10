<?php

namespace hesabro\helpers\widgets;

use Symfony\Component\DomCrawler\Crawler;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget as DynamicFormWidgetBase;
use yii\helpers\Html;
use yii\helpers\Json;

class DynamicFormWidget extends DynamicFormWidgetBase
{

	private $_options;

	private $_hashVar;

	private $_insertPositions = ['bottom', 'top'];


	/**
	 * Initializes the widget options
	 */
	protected function initOptions()
	{
		$this->_options['widgetContainer'] = $this->widgetContainer;
		$this->_options['widgetBody'] = $this->widgetBody;
		$this->_options['widgetItem'] = $this->widgetItem;
		$this->_options['limit'] = $this->limit;
		$this->_options['insertButton'] = $this->insertButton;
		$this->_options['deleteButton'] = $this->deleteButton;
		$this->_options['insertPosition'] = $this->insertPosition;
		$this->_options['formId'] = $this->formId;
		$this->_options['min'] = $this->min;
		$this->_options['fields'] = [];

		foreach ($this->formFields as $field) {
			$this->_options['fields'][] = [
				'id' => Html::getInputId($this->model, '[{}]' . $field),
				'name' => Html::getInputName($this->model, '[{}]' . $field)
			];
		}

		ob_start();
		ob_implicit_flush(false);
	}

	protected function registerOptions($view)
	{
		$encOptions = Json::encode($this->_options);
		$this->_hashVar = \wbraganca\dynamicform\DynamicFormWidget::HASH_VAR_BASE_NAME . hash('crc32', $encOptions);
		$view->registerJs("var {$this->_hashVar} = {$encOptions};\n", $view::POS_HEAD);
	}

	/**
	 * Registers the needed assets
	 */
	public function registerAssets()
	{
		$view = $this->getView();
		DynamicFormAsset::register($view);
		$options = Json::encode($this->_options);
		$this->registerOptions($view);

		$js = 'jQuery("#' . $this->formId . '").yiiDynamicForm(' . $this->_hashVar . ');' . "\n";
		$view->registerJs($js, $view::POS_READY);

		// add a click handler for the clone button
		$js = 'jQuery("#' . $this->formId . '").on("click", "' . $this->insertButton . '", function(e) {' . "\n";
		$js .= "    e.preventDefault();\n";
		$js .= '    jQuery(".' . $this->widgetContainer . '").triggerHandler("beforeInsert", [jQuery(this)]);' . "\n";
		$js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("addItem", ' . $this->_hashVar . ", e, jQuery(this));\n";
		$js .= "});\n";
		$view->registerJs($js, $view::POS_READY);

		// add a click handler for the remove button
		$js = 'jQuery("#' . $this->formId . '").on("click", "' . $this->deleteButton . '", function(e) {' . "\n";
		$js .= "    e.preventDefault();\n";
		$js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("deleteItem", ' . $this->_hashVar . ", e, jQuery(this));\n";
		$js .= "});\n";
		$view->registerJs($js, $view::POS_READY);
	}

	public function run()
	{
		$content = ob_get_clean();
		$crawler = new Crawler();
		$crawler->addHTMLContent($content, \Yii::$app->charset);
		$results = $crawler->filter($this->widgetItem);
		$document = new \DOMDocument('1.0', \Yii::$app->charset);
		$document->appendChild($document->importNode($results->first()->getNode(0), true));
		$this->_options['template'] = \Yii::$app->phpNewVer->trim($document->saveHTML());

		if (isset($this->_options['min']) && $this->_options['min'] === 0 && $this->model->isNewRecord) {
			$content = $this->removeItems($content);
		}

		$this->registerAssets();
		echo Html::tag('div', $content, ['class' => $this->widgetContainer, 'data-dynamicform' => $this->_hashVar]);
	}

	private function removeItems($content)
	{
		$crawler = new Crawler();
		$crawler->addHTMLContent($content, \Yii::$app->charset);
		$crawler->filter($this->widgetItem)->each(function ($nodes) {
			foreach ($nodes as $node) {
				$node->parentNode->removeChild($node);
			}
		});

		return $crawler->html();
	}
}