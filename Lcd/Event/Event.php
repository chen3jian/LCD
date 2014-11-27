<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-14
 * Time: 上午10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Event;

class Event {

    /**
     * 事件名称
     * @var null
     */
    protected $_name = null;

    /**
     * @var null
     */
    protected $_subject;

    /**
     * 事件的参数
     * @var array
     */
    public $data = null;

    /**
     * @var null
     */
    public $result = null;

    /**
     * @var bool
     */
    protected $_stopped = false;


    /**
     * 架构方法
     * @param string $name
     * @param mixed $subject
     * @param array $data
     */
    public function __construct($name, $subject = null, $data = null) {
		$this->_name = $name;
		$this->data = $data;
		$this->_subject = $subject;
	}

    /**
     * 获取name|subject
     * @param $attribute
     * @return mixed
     */
    public function __get($attribute) {
		if ($attribute === 'name' || $attribute === 'subject') {
			return $this->{$attribute}();
		}
        return false;
	}

    /**
     * 获取名称
     * @return null|string
     */
    public function name() {
		return $this->_name;
	}

    /**
     * @return mixed|null
     */
    public function subject() {
		return $this->_subject;
	}

    /**
     * @return bool
     */
    public function stopPropagation() {
		return $this->_stopped = true;
	}

    /**
     * @return bool
     */
    public function isStopped() {
		return $this->_stopped;
	}

    /**
     * @return array
     */
    public function data() {
		return (array)$this->data;
	}
}