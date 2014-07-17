<?php

namespace Enum;

class Controller_Admin_Item extends \Admin\Controller_Admin_Skeleton
{
	protected $module = 'enum_item';
	protected $model = 'Model_Enum_Item';
	protected $_enum;

	protected $name = array(
		'enum item',
		'enum items',
	);

	/**
	 * {@inheritdocs}
	 */
	public function has_access($access)
	{
		return \Auth::has_access('enum.item[' . $access . ']');
	}

	public function query($options = array())
	{
		$query = parent::query()
			->related('enum');

		if ( ! \Auth::has_access('enum.enum[all]'))
		{
			$query->where('enum.read_only', 0);
		}

		return $query;
	}

	protected function find($id = null)
	{
		$model = parent::find($id);

		$this->enum($model->enum);

		return $model;
	}

	protected function enum($id = null)
	{
		if ($id instanceof \Model_Enum)
		{
			return $this->_enum = $id;
		}

		$query = \Model_Enum::query();

		if (is_numeric($id))
		{
			$query->where('id', $id);
		}
		else
		{
			$query->where('slug', $id);
		}

		if ( ! \Auth::has_access('enum.enum[all]'))
		{
			$query->where('read_only', 0);
		}

		if (is_null($id) or is_null($model = $query->get_one()))
		{
			throw new \HttpNotFoundException();
		}

		return $this->_enum = $model;
	}

	protected function url()
	{
		if ( ! empty($this->_url) and empty($this->_enum))
		{
			return $this->_url;
		}

		return $this->_url = \Uri::admin() . 'enum/' . (isset($this->_enum) ? 'view/' . $this->_enum->id : '');
	}

	protected function forge($data = array(), $new = true, $view = null, $cache = true)
	{
		$model = parent::forge($data, $new, $view, $cache);
		isset($this->_enum) and $model->enum = $this->_enum;

		return $model;
	}

	public function action_index()
	{
		return $this->redirect($this->url());
	}

	public function action_view($id = null)
	{
		$model = $this->find($id);
		return $this->redirect($this->url());
	}

	public function action_create($enum = null)
	{
		$this->enum($enum);

		return parent::action_create();
	}

	public function post_create($enum = null)
	{
		$this->enum($enum);

		return parent::post_create();
	}

	public function action_reorder()
	{
		if ($this->is_ajax())
		{
			$id = \Input::param('id');
			$from = (int) \Input::param('fromPosition');
			$to = (int) \Input::param('toPosition');
			$movement = array($from, $to);
			asort($movement);

			$step = \Input::param('step');

			$model = \Model_Enum_Item::query()
				->related('enum')
				->where('id', $id)
				->get_one();

			if (is_null($model))
			{
				throw new \HttpNotFoundException();
			}

			if ($model->sort !== $to)
			{
				$model->sort = $to;
				$model->save();

				$direction = $from < $to ? -$step : $step;

				$models = \Model_Enum_Item::query()
					->where('enum_id', $model->enum_id)
					->where('id', '!=', $model->id)
					->where('sort', 'BETWEEN', $movement)
					->get();

				foreach ($models as $m)
				{
					$m->sort += $direction;
					$m->save();
				}
			}
		}

		echo 'ok';
	}
}