<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enum;

use Orm\Model;

/**
 * Enum Meta Model
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Model_Enum_Meta extends Model
{
	protected static $_belongs_to = array(
		'item' => array(
			'key_from' => 'item_id',
			'model_to' => 'Model_Enum_Item',
		)
	);

	protected static $_properties = array(
		'id',
		'item_id',
		'key',
		'value',
	);

	protected static $_table_name = 'enum_meta';
}
