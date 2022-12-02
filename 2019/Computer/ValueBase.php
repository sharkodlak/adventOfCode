<?php declare(strict_types=1);

namespace adventOfCode\Computer;

class ValueBase implements AddrValue {
	protected $value;
	public function __construct(int $value) {
		$this->value = $value;
	}
	public function get() {
		return $this->value;
	}
	public function getStoreAddr() {
		return $this->value;
	}
	public function getValue() {
		return $this->value;
	}
	public function __toString() {
		return (string) $this->get();
	}
}
