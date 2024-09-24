<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace hesabro\helpers\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;


class StatusActiveBehavior extends AttributeBehavior
{
	const STATUS_ACTIVE = 1;
	/**
	 * @var string the attribute that will receive default value
	 * Set this property to false if you do not want to record the creation time.
	 */
	public $statusAttribute = 'status';
	/**
	 * {@inheritdoc}
	 * In case, when the value is `null`, the result 1
	 * will be used as value.
	 */
	public $value;


	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();

		if (empty($this->attributes)) {
			$this->attributes = [
				BaseActiveRecord::EVENT_BEFORE_INSERT => $this->statusAttribute,
			];
		}
	}

	/**
	 * {@inheritdoc}
	 * In case, when the [[value]] is `null`, the result of the PHP function [time()](https://www.php.net/manual/en/function.time.php)
	 * will be used as value.
	 */
	protected function getValue($event)
	{
		if ($this->value === null) {
			return self::STATUS_ACTIVE;
		}

		return parent::getValue($event);
	}
}
