<?php
/*
 * Frac
 * Copyright (c) 2009 Frac Development Team
 *
 * See COPYING for license conditions.
 */

class TaskType extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('tasktypes');

		$this->hasColumn('id', 'integer', null, array(
				'unsigned' => true,
				'primary' => true,
				'autoincrement' => true
			)
		);
		$this->hasColumn('name', 'string', 32, array(
				'notnull' => true
			)
		);
		$this->hasColumn('created', 'timestamp', null, array(
				'notnull' => true
			)
		);
	}

	public function setUp()
	{
		$this->hasMany('Task as Tasks', array(
				'local' => 'id',
				'foreign' => 'tasktype'
			)
		);
	}
}
