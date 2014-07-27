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
 * Enum migration
 *
 * Use this class to add simple enum migrations
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class Migration_Enum
{
	/**
	 * Enum details
	 *
	 * @var []
	 */
	protected $enum = [];

	/**
	 * Item details
	 *
	 * @var []
	 */
	protected $items = [];

	public function up()
	{
		if ($this->check_table() === false)
		{
			return false;
		}

		$enum = \Model_Enum::forge($this->enum);

		empty($this->items) or $enum->add_item($this->items);

		$enum->save();
	}

	public function down()
	{
		$this->delete($this->enum['slug']);
	}

	/**
	 * Checks if table exists
	 *
	 * @return boolean
	 */
	protected function check_table()
	{
		return \DBUtil::table_exists(\Model_Enum::table());
	}

	/**
	 * Deletes an enum
	 *
	 * @param string $name
	 */
	protected function delete($name)
	{
		$model = \Model_Enum::find_by_slug($name);

		is_null($model) or $model->delete(true);
	}
}
