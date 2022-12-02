<?php declare(strict_types=1);

namespace adventOfCode\Computer;

/*
add: get
	001: 123 -> loadAddr(123)
	101: 234 -> 234
	201: 345 -> loadAddr(base + 345)
input:getStoreAddr
	003: 123 -> addrStore(123, input)
	103: 123 -> addrStore(123, input)
	203: 123 -> addrStore(base + 123, input)
output:get
	004: 123 -> loadAddr(123);
	104: 234 -> 234;
	204: 345 -> loadAddr(base + 345);
*/

interface AddrValue {
	public function get();
	public function getStoreAddr();
}
