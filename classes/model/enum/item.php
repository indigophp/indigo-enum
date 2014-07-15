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
 * Enum Item Model
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Model_Enum_Item extends Model
{
	use \Indigo\Base\Model\SkeletonTrait;

	protected static $_enum;

	protected static $_belongs_to = array('enum');

	protected static $_eav = array(
		'meta' => array(
			'attribute' => 'key',
			'value'     => 'value',
		)
	);

	protected static $_has_many = array(
		'meta' => array(
			'model_to'       => 'Model_Enum_Meta',
			'key_to'         => 'item_id',
			'cascade_delete' => true,
		),
	);

	protected static $_observers = array(
		'Orm\\Observer_Typing',
		'Orm\\Observer_Self' => array(
			'events' => array('before_insert', 'before_update')
		)
	);

	protected static $_properties = array(
		'id' => array(),
		'item_id' => array(),
		'enum_id' => array('data_type' => 'int'),
		'name' => array(
			'form' => array('type' => 'text'),
			'validation' => 'required|trim',
		),
		'slug' => array(),
		'description' => array(
			'form' => array('type' => 'textarea'),
		),
		'active' => array(
			'default'   => 1,
			'data_type' => 'int',
			'min'       => 0,
			'max'       => 1,
			'form'      => array(
				'type' => 'switch'
			),
		),
		'sort' => array('data_type' => 'int'),
	);

	protected static $_sort = true;

	protected static $_table_name = 'enum_items';

	protected static $_primary_key = array('id');

	public static function _init()
	{
		static::$_properties = \Arr::merge(static::$_properties, array(
			'id' => array(
				'label' => gettext('ID')
			),
			'item_id' => array(
				'label' => gettext('Item ID')
			),
			'enum_id' => array(
				'label' => gettext('Enum ID')
			),
			'name' => array(
				'label' => gettext('Name'),
			),
			'slug' => array(
				'label' => gettext('Slug'),
			),
			'description' => array(
				'label' => gettext('Description'),
			),
			'active' => array(
				'label' => gettext('Active'),
				'form' => array(
					'options' => array(
						0 => gettext('No'),
						1 => gettext('Yes'),
					),
				),
			),
			'sort' => array(
				'label' => gettext('Sort'),
			),
		));
	}

	public function _event_before_insert()
	{
		$this->item_id = $this->query()->where('enum_id', $this->enum_id)->max('item_id') + 1;
		static::$_sort === true and $this->sort = $this->query()->where('enum_id', $this->enum_id)->max('sort') + 10;
		$this->slug = $this->_get_slug();
	}

	public function _event_before_update()
	{
		$slug = \Inflector::friendly_title($this->name, '_', true);

		// update it if it's different from the current one
		$this->slug === $slug or $this->slug = $this->_get_slug();
	}

	protected function _get_slug()
	{
		$slug = \Inflector::friendly_title($this->name, '_', true);

		$same = $this->query()
			->where('slug', 'LIKE', $slug.'%')
			->where('enum_id', $this->enum_id)
			->get();

		// make sure our slug is unique
		if ( ! empty($same))
		{
			$max = -1;

			foreach ($same as $record)
			{
				if (preg_match('/^'.$slug.'(?:_([0-9]+))?$/', $record->slug, $matches))
				{
					$index = isset($matches[1]) ? (int) $matches[1] : 0;
					$max < $index and $max = $index;
				}
			}

			$max < 0 or $slug .= '_'.($max + 1);
		}

		return $slug;
	}
}
