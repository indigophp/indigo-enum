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
 * Enum admin controller
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Controller_Admin extends \Admin\Controller_Admin_Skeleton
{
	/**
	 * {@inheritdoc}
	 */
	protected $module = 'enum';

	/**
	 * {@inheritdoc}
	 */
	protected $model = 'Model_Enum';

	/**
	 * {@inheritdoc}
	 */
	protected $name = [
		'enum',
		'enums',
	];

	/**
	 * {@inheritdoc}
	 */
	public function has_access($access)
	{
		return parent::has_access('enum.enum[' . $access . ']');
	}

	/**
	 * {@inheritdoc}
	 */
	public function query($options = [])
	{
		$query = parent::query()
			->related('default');

		if ($this->request->action === 'view')
		{
			$query->related('items')
				->order_by('items.sort');
		}

		if ($this->has_access('all') === false)
		{
			$query->where('read_only', 0);
		}

		return $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public function view($view, $data = [], $auto_filter = null)
	{
		switch ($view)
		{
			case 'admin/skeleton/view':
				$view = 'admin/enum/view';
				break;
		}

		return parent::view($view, $data, $auto_filter);
	}
}
