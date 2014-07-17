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
	use \Indigo\Base\Model\SkeletonTrait;

	protected static $_has_many = array(
		'items' => array(
			'model_to' => 'Model_Enum_Item',
			'key_to'   => 'enum_id',
		),
	);

	protected static $_has_one = array(
		'default' => array(
			'key_from' => array('id', 'default_id'),
			'key_to'   => array('enum_id', 'item_id'),
			'model_to' => 'Model_Enum_Item',
		),
	);

	protected static $_observers = array(
		'Orm\\Observer_Typing',
		'Orm\\Observer_Self' => array(
			'events' => array('before_insert')
		),
		'Orm\\Observer_Slug' => array(
			'events'    => array('before_insert'),
			'source'    => 'name',
			'separator' => '_',
			'overwrite' => false,
		),
	);

	protected static $_properties = array(
		'id' => array(
			'label' => 'ID',
			'view' => false,
		),
		'name' => array(
			'label' => 'Name',
			'form' => array('type' => 'text'),
			'list' => array('type' => 'text'),
			'validation' => array('required'),
		),
		'slug' => array('label' => 'Slug'),
		'description' => array(
			'label' => 'Description',
			'form' => array('type' => 'textarea'),
		),
		'default_id' => array(
			'label' => 'Default',
			'default'   => 1,
			'data_type' => 'int',
			'view' => false,
			'form' => array('type' => 'select'),
		),
		'default.name' => array(
			'label' => 'Default',
			'list' => array('type' => 'text'),
		),
		'active' => array(
			'label'     => 'Active',
			'default'   => 1,
			'data_type' => 'int',
			'min'       => 0,
			'max'       => 1,
			'form'      => array(
				'type'     => 'checkbox',
				'template' => 'switch',
				'options'  => array('No', 'Yes'),
			),
			'list'      => array('type' => 'select'),
		),
		'read_only' => array(
			'label'     => 'Read-only',
			'default'   => 0,
			'data_type' => 'int',
			'min'       => 0,
			'max'       => 1,
			'list' => array(
				'type'    => 'select',
				'default' => 0
			),
		),
	);

	protected static $_table_name = 'enums';

	public static function _init()
	{
		if (\Auth::has_access('enum.enum[all]'))
		{
			\Arr::set(static::$_properties, 'read_only.form', array(
					'type' => 'checkbox',
					'template' => 'switch',
					'options' => array(
						gettext('No'),
						gettext('Yes'),
					),
			));
		}
	}

	public function add_item($data = array(), $default = false, $save = true)
	{
		if (\Arr::is_multi($data))
		{
			foreach ($data as $default => $item)
			{
				$this->add_item($item, $default === 'default', false);
			}
		}
		else
		{
			$model = \Model_Enum_Item::forge();
			$model->set($data);
			$this->items[] = $model;
			$default === true and $this->default = $model;
		}

		$save === true and $this->save(true);
	}

	public static function get_enum_options($enum)
	{
		$options = static::query()
			->related('default')
			->related('items')
			->related('items.meta')
			->where('slug', $enum)
			->get_one();

		if (is_null($options))
		{
			$options = array();
		}
		else
		{
			$options = $options->to_array();
			$options = \Arr::pluck($options['items'], 'name', 'item_id');
		}

		return $options;
	}
}
