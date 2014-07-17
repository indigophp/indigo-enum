<?php

/*
 * This file is part of the Indigo Enum module.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Indigo\Core\Alias;

Alias::instance('default')->alias(array(
	'Model_Enum'      => 'Enum\\Model_Enum',
	'Model_Enum_Item' => 'Enum\\Model_Enum_Item',
	'Model_Enum_Meta' => 'Enum\\Model_Enum_Meta',
));
