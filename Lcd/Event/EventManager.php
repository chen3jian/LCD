<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-14
 * Time: 上午10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Event;

class EventManager {
	public static $defaultPriority = 10;//默认优先级为10

    /**
     * @var EventManager
     */
    protected static $_generalManager = null;

	protected $_listeners = array();

	protected $_isGlobal = false;


    /**
     *
     * @param EventManager $manager
     * @return EventManager
     */
    public static function instance(EventManager $manager = null) {
		if ($manager instanceof EventManager) {
			static::$_generalManager = $manager;
		}
		if (empty(static::$_generalManager)) {
			static::$_generalManager = new EventManager();
		}

		static::$_generalManager->_isGlobal = true;
		return static::$_generalManager;
	}

    /**
     * 添加监听者
     * @param $callable
     * @param null $eventKey
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function attach($callable, $eventKey = null, $options = array()) {
		if (!$eventKey && !($callable instanceof EventListener)) {
			throw new \InvalidArgumentException('The eventKey variable is required');
		}
		if ($callable instanceof EventListener) {
			$this->attachSubscriber($callable);
			return;
		}
		$options = $options + array('priority' => static::$defaultPriority);
		$this->_listeners[$eventKey][$options['priority']][] = array(
			'callable' => $callable,
		);
	}

    /**
     * 添加订阅者
     * @param EventListener $subscriber
     * @return void
     */
    protected function attachSubscriber(EventListener $subscriber) {
		foreach ((array)$subscriber->implementedEvents() as $eventKey => $function) {
			$options = array();
			$method = $function;
			if (is_array($function) && isset($function['callable'])) {
				list($method, $options) = $this->extractCallable($function, $subscriber);
			} elseif (is_array($function) && is_numeric(key($function))) {
				foreach ($function as $f) {
					list($method, $options) = $this->extractCallable($f, $subscriber);
					$this->attach($method, $eventKey, $options);
				}
				continue;
			}
			if (is_string($method)) {
				$method = array($subscriber, $function);
			}
			$this->attach($method, $eventKey, $options);
		}
	}

    /**
     *
     * @param $function
     * @param $object
     * @return array
     */
    protected function extractCallable($function, $object) {
		$method = $function['callable'];
		$options = $function;
		unset($options['callable']);
		if (is_string($method)) {
			$method = array($object, $method);
		}
		return array($method, $options);
	}

    /**
     * 移除事件监听者
     * @param $callable
     * @param null $eventKey
     * @return void
     */
    public function detach($callable, $eventKey = null) {
		if ($callable instanceof EventListener) {
			$this->detachSubscriber($callable, $eventKey);
            return;
		}
		if (empty($eventKey)) {
			foreach (array_keys($this->_listeners) as $eventKey) {
				$this->detach($callable, $eventKey);
			}
			return;
		}
		if (empty($this->_listeners[$eventKey])) {
			return;
		}
		foreach ($this->_listeners[$eventKey] as $priority => $callables) {
			foreach ($callables as $k => $callback) {
				if ($callback['callable'] === $callable) {
					unset($this->_listeners[$eventKey][$priority][$k]);
					break;
				}
			}
		}
	}

    /**
     * 移除订阅者
     * @param EventListener $subscriber
     * @param null $eventKey
     * @return void
     */
    protected function detachSubscriber(EventListener $subscriber, $eventKey = null) {
		$events = (array)$subscriber->implementedEvents();
		if (!empty($eventKey) && empty($events[$eventKey])) {
			return;
		} elseif (!empty($eventKey)) {
			$events = array($eventKey => $events[$eventKey]);
		}
		foreach ($events as $key => $function) {
			if (is_array($function)) {
				if (is_numeric(key($function))) {
					foreach ($function as $handler) {
						$handler = isset($handler['callable']) ? $handler['callable'] : $handler;
						$this->detach(array($subscriber, $handler), $key);
					}
					continue;
				}
				$function = $function['callable'];
			}
			$this->detach(array($subscriber, $function), $key);
		}
	}

    /**
     * 事件调度
     * @param $event
     * @return void
     */
    public function dispatch($event) {
		if (is_string($event)) {
			$event = new Event($event);
		}

		$listeners = $this->listeners($event->name());
		if (empty($listeners)) {
			return;
		}

		foreach ($listeners as $listener) {
			if ($event->isStopped()) {
				break;
			}
			$result = $this->callListener($listener['callable'], $event);
			if ($result === false) {
				$event->stopPropagation();
			}
			if ($result !== null) {
				$event->result = $result;
			}
		}
	}

    /**
     * @param callable $listener
     * @param Event $event
     * @return mixed
     */
    protected function callListener(callable $listener, Event $event) {
		$data = $event->data();
		$length = count($data);
		if ($length) {
			$data = array_values($data);
		}
		switch ($length) {
			case 0:
				return $listener($event);
			case 1:
				return $listener($event, $data[0]);
			case 2:
				return $listener($event, $data[0], $data[1]);
			case 3:
				return $listener($event, $data[0], $data[1], $data[2]);
			default:
				array_unshift($data, $event);
				return call_user_func_array($listener, $data);
		}
	}

    /**
     * 事件监听
     * @param $eventKey
     * @return array
     */
    public function listeners($eventKey) {
		$globalListeners = array();
		if (!$this->_isGlobal) {
			$globalListeners = static::instance()->prioritisedListeners($eventKey);
		}

		if (empty($this->_listeners[$eventKey]) && empty($globalListeners)) {
			return array();
		}

		$listeners = $this->_listeners[$eventKey];
		foreach ($globalListeners as $priority => $priorityQ) {
			if (!empty($listeners[$priority])) {
				$listeners[$priority] = array_merge($priorityQ, $listeners[$priority]);
				unset($globalListeners[$priority]);
			}
		}
		$listeners = $listeners + $globalListeners;

		ksort($listeners);
		$result = array();
		foreach ($listeners as $priorityQ) {
			$result = array_merge($result, $priorityQ);
		}
		return $result;
	}

    /**
     * 获取事件监听列表
     * @param $eventKey
     * @return array
     */
    public function prioritisedListeners($eventKey) {
		if (empty($this->_listeners[$eventKey])) {
			return array();
		}
		return $this->_listeners[$eventKey];
	}
}
