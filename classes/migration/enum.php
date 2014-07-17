<?php

namespace Enum;

abstract class Migration_Enum
{
	protected $_enum = array();

	protected $_items = array();

	public function up()
	{
		if ( ! $this->check_table())
		{
			return false;
		}

		$enum = \Model_Enum::forge($this->_enum);

		empty($this->_items) or $enum->add_item($this->_items);

		$enum->save();
	}

	public function down()
	{
		$this->delete($this->_enum['slug']);
	}

	protected function check_table()
	{
		return \DBUtil::table_exists(\Model_Enum::table());
	}

	protected function delete($name)
	{
		$model = \Model_Enum::find_by_slug($name);

		is_null($model) or $model->delete(true);
	}
}
