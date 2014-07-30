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
 * Enum Model
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Model_Enum extends Model
{
	use \Indigo\Skeleton\Model;

	/**
	 * {@inheritdoc}
	 */
	protected static $_has_many = [
		'items' => [
			'model_to' => 'Model_Enum_Item',
			'key_to'   => 'enum_id',
		],
	];

	/**
	 * {@inheritdoc}
	 */
	protected static $_has_one = [
		'default' => [
			'key_from' => ['id', 'default_id'],
			'key_to'   => ['enum_id', 'item_id'],
			'model_to' => 'Model_Enum_Item',
		],
	];

	/**
	 * {@inheritdoc}
	 */
	protected static $_observers = [
		'Orm\\Observer_Typing',
		'Orm\\Observer_Self' => [
			'events' => ['before_insert'],
		],
		'Orm\\Observer_Slug' => [
			'events'    => ['before_insert'],
			'source'    => 'name',
			'separator' => '_',
			'overwrite' => false,
		],
	];

	/**
	 * {@inheritdoc}
	 */
	protected static $_properties = [
		'id' => [
			'label' => 'ID',
		],
		'name' => [
			'label'      => 'Name',
			'type'       => 'text',
			'validation' => ['required'],
		],
		'slug' => [
			'label' => 'Slug',
		],
		'description' => [
			'label' => 'Description',
			'type'  => 'textarea',
		],
		'default_id' => [
			'label'     => 'Default',
			'default'   => 1,
			'data_type' => 'int',
		],
		'active' => [
			'label'     => 'Active',
			'default'   => 1,
			'data_type' => 'int',
			'options'  => ['No', 'Yes'],
			'validation' => ['value' => [0, 1]],
		],
		'read_only' => [
			'label'     => 'Read-only',
			'default'   => 0,
			'data_type' => 'int',
			'options'   => ['No', 'Yes'],
			'validation' => ['value' => [0, 1]],
		],
	];

	/**
	 * {@inheritdoc}
	 */
	protected static $_table_name = 'enums';

	/**
	 * List skeleton properties
	 *
	 * @var []
	 */
	protected static $_list_properties = [
		'id' => [
			'label' => '#',
			'type'  => 'text',
		],
		'name',
		'active' => [
			'type' => 'select',
		],
		'read_only' => [
			'type' => 'select',
		],
	];

	/**
	 * Form skeleton properties
	 *
	 * @var []
	 */
	protected static $_form_properties = [
		'name',
		'description',
		'default_id' => [
			'type' => 'select',
		],
		'active' => [
			'type'     => 'checkbox',
			'template' => 'switch',
		],
		'read_only' => [
			'type'     => 'checkbox',
			'template' => 'switch',
		],
	];

	/**
	 * View skeleton properties
	 *
	 * @var []
	 */
	protected static $_view_properties = [
		'id',
		'name',
		'description',
		'active',
		'read_only',
	];

	public static function _init()
	{
		if (\Auth::has_access('enum.enum[all]') === false)
		{
			unset(static::$_form_properties['read_only']);
		}
	}

	/**
	 * Adds an item to the enum and optionally saves it
	 *
	 * @param Model_Enum_Item $model
	 * @param boolean         $default
	 * @param boolean         $save
	 *
	 * @return this
	 */
	public function add_item(Model_Enum_Item $model, $default = false, $save = true)
	{
		$this->items[] = $model;

		if ($default === true)
		{
			$this->default = $model;
		}

		$save === true and $this->save(true);

		return $this;
	}

	/**
	 * Adds several items to the enum
	 *
	 * @param []      $models
	 * @param boolean $save
	 *
	 * @return this
	 */
	public function add_items(array $models = [], $save = true)
	{
		foreach ($models as $default => $item)
		{
			$this->add_item($item, $default === 'default', false);
		}

		$save === true and $this->save(true);

		return $this;
	}

	/**
	 * Returns items for an enum
	 *
	 * @param string|integer $enum
	 *
	 * @return []
	 */
	public static function get_enum_items($enum)
	{
		$enum = static::query()
			->related('items')
			->order_by('items.sort');

		if (is_int($enum))
		{
			$enum->where('id', $enum);
		}
		else
		{
			$enum->where('slug', $enum);
		}

		$enum = $enum->get_one();

		if (is_null($enum))
		{
			return [];
		}

		$enum = $enum->to_array();

		return \Arr::pluck($enum['items'], 'name', 'item_id');
	}
}
