<?php declare(strict_types=1);

namespace adventOfCode\Computer;

class Addr extends ValueBase {
	protected $interpreter;
	public function __construct(int $value, Interpreter $interpreter) {
		parent::__construct($value);
		$this->interpreter = $interpreter;
	}
	public function get() {
		return $this->interpreter->addrLoad($this->getAddr());
	}
	protected function getAddr() {
		return $this->getValue();
	}
	public function __toString() {
		return '*' . $this->getValue() . '=' . $this->get();
	}
}
