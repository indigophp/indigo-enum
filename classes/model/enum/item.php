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

use Fuel\Orm\SortableInterface;
use Orm\Model;

/**
 * Enum Item Model
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Model_Enum_Item extends Model implements SortableInterface
{
	use \Indigo\Skeleton\Model;

	/**
	 * {@inheritdoc}
	 */
	protected static $_belongs_to = ['enum'];

	/**
	 * {@inheritdoc}
	 */
	protected static $_eav = ['meta'];

	/**
	 * {@inheritdoc}
	 */
	protected static $_has_many = [
		'meta' => [
			'model_to'       => 'Model_Enum_Meta',
			'key_to'         => 'item_id',
			'cascade_delete' => true,
		],
	];

	/**
	 * {@inheritdoc}
	 */
	protected static $_observers = [
		'Orm\\Observer_Typing',
		'Orm\\Observer_Self' => [
			'events' => ['before_insert', 'before_update'],
		],
		'Fuel\\Orm\\Observer\\Sort',
	];

	/**
	 * {@inheritdoc}
	 */
	protected static $_properties = [
		'id' => [
			'label'     => 'ID',
			'data_type' => 'integer',
		],
		'item_id' => [
			'label'     => 'Item ID',
			'data_type' => 'integer',
		],
		'enum_id' => [
			'label'     => 'Enum ID',
			'data_type' => 'integer',
		],
		'name' => [
			'label'      => 'Name',
			'validation' => ['required'],
		],
		'slug' => [
			'label' => 'Slug',
		],
		'description' => [
			'label' => 'Description',
		],
		'active' => [
			'label'      => 'Active',
			'default'    => 1,
			'data_type'  => 'integer',
			'options'    => ['No', 'Yes'],
			'validation' => ['value' => [0, 1]],
		],
		'sort' => [
			'label'     => 'Sort',
			'data_type' => 'integer',
		],
	];

	/**
	 * Skeleton properties
	 *
	 * @var []
	 */
	protected static $skeleton = [
		'lists' => [
			'id' => [
				'label' => '#',
				'type'  => 'text',
			],
			'name',
			'active' => [
				'type' => 'select',
			],
		],
		'form' => [
			'name',
			'description' => [
				'type' => 'textarea',
			],
			'active' => [
				'type'     => 'checkbox',
				'template' => 'switch',
			],
		],
		'view' => [
			'id',
			'name',
			'description',
			'active',
		],
	];

	/**
	 * {@inheritdoc}
	 */
	protected static $_table_name = 'enum_items';

	/**
	 * {@inheritdoc}
	 */
	public function getSortMax()
	{
		return $this->query()->where('enum_id', $this->enum_id)->max('sort');
	}

	public function _event_before_insert()
	{
		$this->item_id = $this->query()->where('enum_id', $this->enum_id)->max('item_id') + 1;
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
		if (empty($same) === false)
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
