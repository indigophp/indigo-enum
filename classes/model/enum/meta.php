<?php

/*
 * This file is part of the Indigo Enum module.
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
	/**
	 * {@inheritdoc}
	 */
	protected static $_belongs_to = array(
		'item' => array(
			'key_from' => 'item_id',
			'model_to' => 'Model_Enum_Item',
		)
	);

	/**
	 * {@inheritdoc}
	 */
	protected static $_properties = array(
		'id',
		'item_id',
		'attribute',
		'value',
	);

	/**
	 * {@inheritdoc}
	 */
	protected static $_table_name = 'enum_meta';
}
