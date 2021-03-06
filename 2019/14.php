#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$file = [
	'10 ORE => 10 A',
	'1 ORE => 1 B',
	'7 A, 1 B => 1 C',
	'7 A, 1 C => 1 D',
	'7 A, 1 D => 1 E',
	'7 A, 1 E => 1 FUEL',
];
$file = [
	'9 ORE => 2 A',
	'8 ORE => 3 B',
	'7 ORE => 5 C',
	'3 A, 4 B => 1 AB',
	'5 B, 7 C => 1 BC',
	'4 C, 1 A => 1 CA',
	'2 AB, 3 BC, 4 CA => 1 FUEL',
];
$file = [
	'157 ORE => 5 NZVS',
	'165 ORE => 6 DCFZ',
	'44 XJWVT, 5 KHKGT, 1 QDVJ, 29 NZVS, 9 GPVTF, 48 HKGWZ => 1 FUEL',
	'12 HKGWZ, 1 GPVTF, 8 PSHF => 9 QDVJ',
	'179 ORE => 7 PSHF',
	'177 ORE => 5 HKGWZ',
	'7 DCFZ, 7 PSHF => 2 XJWVT',
	'165 ORE => 2 GPVTF',
	'3 DCFZ, 7 NZVS, 5 HKGWZ, 10 PSHF => 8 KHKGT',
];
$file = [
	'2 VPVL, 7 FWMGM, 2 CXFTF, 11 MNCFX => 1 STKFG',
	'17 NVRVD, 3 JNWZP => 8 VPVL',
	'53 STKFG, 6 MNCFX, 46 VJHF, 81 HVMC, 68 CXFTF, 25 GNMV => 1 FUEL',
	'22 VJHF, 37 MNCFX => 5 FWMGM',
	'139 ORE => 4 NVRVD',
	'144 ORE => 7 JNWZP',
	'5 MNCFX, 7 RFSQX, 2 FWMGM, 2 VPVL, 19 CXFTF => 3 HVMC',
	'5 VJHF, 7 MNCFX, 9 VPVL, 37 CXFTF => 6 GNMV',
	'145 ORE => 6 MNCFX',
	'1 NVRVD => 8 CXFTF',
	'1 VJHF, 6 MNCFX => 4 RFSQX',
	'176 ORE => 6 VJHF',
];
/*
$file = [
	'171 ORE => 8 CNZTR',
	'7 ZLQW, 3 BMBT, 9 XCVML, 26 XMNCP, 1 WPTQ, 2 MZWV, 1 RJRHP => 4 PLWSL',
	'114 ORE => 4 BHXH',
	'14 VRPVC => 6 BMBT',
	'6 BHXH, 18 KTJDG, 12 WPTQ, 7 PLWSL, 31 FHTLT, 37 ZDVW => 1 FUEL',
	'6 WPTQ, 2 BMBT, 8 ZLQW, 18 KTJDG, 1 XMNCP, 6 MZWV, 1 RJRHP => 6 FHTLT',
	'15 XDBXC, 2 LTCX, 1 VRPVC => 6 ZLQW',
	'13 WPTQ, 10 LTCX, 3 RJRHP, 14 XMNCP, 2 MZWV, 1 ZLQW => 1 ZDVW',
	'5 BMBT => 4 WPTQ',
	'189 ORE => 9 KTJDG',
	'1 MZWV, 17 XDBXC, 3 XCVML => 2 XMNCP',
	'12 VRPVC, 27 CNZTR => 2 XDBXC',
	'15 KTJDG, 12 BHXH => 5 XCVML',
	'3 BHXH, 2 VRPVC => 7 MZWV',
	'121 ORE => 7 VRPVC',
	'7 XCVML => 6 RJRHP',
	'5 BHXH, 4 VRPVC => 5 LTCX',
];
*/
//$file = file(substr(__FILE__, 0, -4) . '/input.txt', FILE_IGNORE_NEW_LINES);

class Chemical {
	private $name;
	private $order;
	private $quantity;
	private $reactants = [];

	public function __construct(string $name, int $order, int $quantity, array $reactants = []) {
		$this->name = $name;
		$this->order = $order;
		$this->quantity = $quantity;
		uasort($reactants, 'self::cmpReactants');
		foreach (array_reverse($reactants) as $reactant) {
			$this->reactants[$reactant->getName()] = $reactant;
		}
	}

	public function __toString(): string {
		return implode(', ', $this->reactants) . " => {$this->quantity} {$this->name}";
	}

	private function cmpReactants(Reactant $a, Reactant $b): int {
		return $a->getOrder() - $b->getOrder();
	}

	public function getName(): string {
		return $this->name;
	}

	public function getOrder(): int {
		return $this->order;
	}

	public function getQuantity(): int {
		return $this->quantity;
	}

	public function getReactants(): array {
		return $this->reactants;
	}

	public function findReaction(array $sources): Chemical {
		foreach ($this->reactants as $reactant) {
			$sourceQuantity = $sources[$reactant->getName()] ?? 0;
			$reactantAmount = $reactant->getAmount();
			if ($reactantAmount > $sourceQuantity) {
				return $reactant->findReaction($sources);
			}
		}
		return $this;
	}
}

class Reactant {
	private $amount;
	private $chemical;

	public function __construct(int $amount, Chemical $chemical) {
		$this->amount = $amount;
		$this->chemical = $chemical;
	}

	public function __toString(): string {
		return "-{$this->amount} {$this->chemical->getName()}";
	}

	public function getAmount(): int {
		return $this->amount;
	}

	public function getName(): string {
		return $this->chemical->getName();
	}

	public function getOrder(): int {
		return $this->chemical->getOrder();
	}

	public function findReaction(array $sources): Chemical {
		return $this->chemical->findReaction($sources);
	}
}

/*
FUEL1
7   1
|     E1
|   7 1
| /   D1
|   7 1
| /   C1
|   7 1
A10   B1
10  1
ORE

FUEL1
28  1
A10 |
10  /
ORE



1 = 1 + 30(28)
2 = 2 + 60(56)
3 = 3 + 90(84)
4 = 4 + 120(112)
5 = 5 + 140(140)


ORE(1)
   >9= A(2)
        >1-------------\
        >3---\         |
   >8= B(3)  |         |
        >4---+= AB(1)  |
        >5---\    >2---|--------\
   >7= C(5)  |         |        |
        >7---+= BC(1)  |        |
                  >3---|--------+
        >4-------------+=CA(1)  |
                           >4---+= FUEL(1)

-FUEL: 2520
-AB:   5040
-BC:   7560
-CA:  10080
-A:   10080 *3(>AB)
-B:   37800 *4(>AB)
-C:   10080 *5(>CA) *7(>BC)
ORE:  57960 *2(>B) *3(>A)

ORE(1)
 >157= NZVS(5)
          >29---------------------------\
           >7---------------\           |
 >165= DCFZ(6)              |           |
           >3---------------+           |
           >7---\           |           |
 >179= PSHF(7)  |           |           |
           >7---= XJWVT(2)  |           |
                      >44---|-----------+
          >10---------------+           |
                            |           |
           >8----\          |           |
 >177= HKGWZ(5)  |          |           |
            >5---|----------= KHKGT(8)  |
                 |                 >5---+
           >12---+                      |
           >48---|----------------------+
 >165= GPVTF(2)  |                      |
            >1---= QDVJ(9)              |
                       >1---------------+
            >9--------------------------= FUEL(1)

-FUEL:    1 * 2(GPVTF>QDVJ)
NZVS:    29
XJWVT:   44
KHKGT:    5
HKGWZ:   48
QDVJ:     1
GPVTF:    9

*/

$reactions = [];
$regexp = '~(?P<quantity>\d+) (?P<chemical>[A-Z]+)~';

foreach ($file as $line) {
	preg_match_all($regexp, $line, $matches, PREG_SET_ORDER);
	$result = array_pop($matches);
	$reactants = [];
	foreach ($matches as $match) {
		$reactants[$match['chemical']] = intval($match['quantity']);
	}
	$reactions[$result['chemical']] = [
		'quantity' => intval($result['quantity']),
		'reactants' => $reactants,
	];
}

$order = 0;
$chemicals = [
	'ORE' => new Chemical('ORE', $order++, 1),
];
while ($reactions) {
	foreach ($reactions as $chemicalName => $reaction) {
		$reactants = [];
		$reactantsExists = true;
		foreach ($reaction['reactants'] as $reactantName => $amount) {
			if (!isset($chemicals[$reactantName])) {
				$reactantsExists = false;
				break;
			}
			$reactants[$reactantName] = new Reactant($amount, $chemicals[$reactantName]);
		}
		if ($reactantsExists) {
			$chemicals[$chemicalName] = new Chemical($chemicalName, $order++, $reaction['quantity'], $reactants);
			unset($reactions[$chemicalName]);
			break;
		}
	}
}

$sums = [
	'FUEL' => 1,
];
$i = 0;
while (array_diff_key($sums, array_flip(['ORE']))) {
	$chemical = null;
	foreach ($sums as $chemicalName => $amount) {
		if (empty($chemical) || $chemicals[$chemicalName]->getOrder() > $chemical->getOrder()) {
			$chemical = $chemicals[$chemicalName];
		}
	}
	$chemicalSum = $sums[$chemical->getName()];
	foreach ($chemical->getReactants() as $reactant) {
		$previousAmount = $sums[$reactant->getName()] ?? 0;
		$reactionQuantity = $chemical->getQuantity();
		$chemicalSumCeil = $reactionQuantity * ceil($chemicalSum / $reactionQuantity);
		$sums[$reactant->getName()] = $previousAmount + $chemicalSumCeil / $reactionQuantity * $reactant->getAmount();
	}
	unset($sums[$chemical->getName()]);
}

echo "Number of ORE to process is: {$sums['ORE']} .\n";


$initialORE = 1000000000000;
$sources = [
	'ORE' => $initialORE,
];
$completeReaction = ['ORE' => null, 'FUEL' => null];
$i = 0;
while (isset($sources['ORE']) && $sources['ORE'] > 0) {
	if (isset($sources['FUEL']) && !array_diff_key($sources, $completeReaction)) {
		$completeReaction['ORE'] = $initialORE - $sources['ORE'];
		$completeReaction['FUEL'] = $sources['FUEL'];
		$sources['ORE'] = $initialORE % $completeReaction['ORE'];
		$sources['FUEL'] *= floor($initialORE / $completeReaction['ORE']);
	}
	$chemical = $chemicals['FUEL']->findReaction($sources);
	if ($chemical->getName() == 'ORE') {
		break;
	}
	foreach ($chemical->getReactants() as $reactantName => $reactant) {
		$sources[$reactantName] -= $reactant->getAmount();
		if ($sources[$reactantName] == 0) {
			unset($sources[$reactantName]);
		}
	}
	$sources[$chemical->getName()] = $chemical->getQuantity() + ($sources[$chemical->getName()] ?? 0);
}

echo "Maximum FUEL created is {$sources['FUEL']} .\n";
