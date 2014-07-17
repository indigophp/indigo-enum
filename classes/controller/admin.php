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

/**
 * Enum admin class
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Controller_Admin extends \Admin\Controller_Admin_Skeleton
{
	protected $module = 'enum';

	protected $model = 'Model_Enum';

	protected $name = array(
		'enum',
		'enums',
	);

	/**
	 * {@inheritdocs}
	 */
	public function has_access($access)
	{
		return parent::has_access('enum[' . $access . ']');
	}

	/**
	 * {@inheritdocs}
	 */
	public function query($options = array())
	{
		$query = parent::query()
			->related('default');

		if ($this->request->action === 'view')
		{
			$query->related('items')
				->order_by('items.sort');
		}

		if ( ! $this->has_access('all'))
		{
			$query->where('read_only', 0);
		}

		return $query;
	}

	public function view($view, $data = array(), $auto_filter = null)
	{
		switch ($this->request->action)
		{
			case 'view':
				$view = 'admin/enum/view';
				break;
			default:
				break;
		}

		return parent::view($view, $data, $auto_filter);
	}

	protected function map(\Orm\Model $model, array $properties)
	{
		$data = parent::map($model, $properties);

		empty($data['default.name']) and $data['default.name'] = gettext('<i>None</i>');

		return $data;
	}
}
