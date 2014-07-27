<?php

/*
 * This file is part of the Indigo Enum module.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuel\Migrations;

/**
 * Permission migration
 *
 * Creates permissions for enum
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Create_permissions extends \Admin\Migration_Permission
{
	/**
	 * {@inheritdoc}
	 */
	protected $permissions = [
		[
			'area'        => 'enum',
			'permission'  => 'enum',
			'actions'     => ['all', 'create', 'delete', 'edit', 'view'],
			'description' => 'Permission for enum',
		],
		[
			'area'        => 'enum',
			'permission'  => 'item',
			'actions'     => ['all', 'create', 'delete', 'edit', 'view'],
			'description' => 'Permission for enum items',
		],
	];
}
