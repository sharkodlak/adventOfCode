<?php declare(strict_types=1);

namespace adventOfCode\Computer;

class RelAddr extends Addr {
	protected function getAddr() {
		return $this->interpreter->getRelativeBase() + $this->getValue();
	}
	public function getStoreAddr() {
		return $this->getAddr();
	}
	public function __toString() {
		return '*' . $this->interpreter->getRelativeBase() . '+' . $this->getValue() . '=' . $this->get();
	}
}
