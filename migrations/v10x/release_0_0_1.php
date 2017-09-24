<?php
/**
 *
 * Best Answer. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, kinerity
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\bestanswer\migrations\v10x;

use \phpbb\db\migration\container_aware_migration;

class release_0_0_1 extends \phpbb\db\migration\container_aware_migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return void
	 * @access public
	 */
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	/**
	 * Add or update schema in the database
	 *
	 * @return void
	 * @access public
	 */
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'forums'			=> array(
					'enable_bestanswer'				=> array('BOOL', 0),
				),
				$this->table_prefix . 'topics'			=> array(
					'bestanswer_id'					=> array('UINT', 0),
					'bestanswer_user_id'			=> array('UINT', 0),
				),
				$this->table_prefix . 'users'			=> array(
					'user_answers'					=> array('UINT', 0),
				),
			),
		);
	}

	/**
	 * Add or update data in the database
	 *
	 * @return void
	 * @access public
	 */
	public function update_data()
	{
		$data = array(
			// Add permissions
			array('permission.add', array('f_mark_bestanswer', false)),
			array('permission.add', array('m_mark_bestanswer', false)),
		);

		if ($this->role_exists('ROLE_FORUM_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_mark_bestanswer'));
		}

		if ($this->role_exists('ROLE_FORUM_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_FORUM_STANDARD', 'f_mark_bestanswer'));
		}

		if ($this->role_exists('ROLE_MOD_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_mark_bestanswer'));
		}

		if ($this->role_exists('ROLE_MOD_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_mark_bestanswer'));
		}

		return $data;
	}

	/**
	 * Drop schema in the database
	 *
	 * @return void
	 * @access public
	 */
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'forums'			=> array(
					'enable_bestanswer',
				),
				$this->table_prefix . 'topics'			=> array(
					'bestanswer_id',
					'bestanswer_user_id',
				),
				$this->table_prefix . 'users'			=> array(
					'user_answers',
				),
			),
		);
	}

	/**
	 * Custom function query permission roles
	 *
	 * @return void
	 * @access public
	 */
	private function role_exists($role)
	{
		$sql = 'SELECT role_id
			FROM ' . ACL_ROLES_TABLE . "
			WHERE role_name = '" . $this->db->sql_escape($role) . "'";
		$result = $this->db->sql_query_limit($sql, 1);
		$role_id = $this->db->sql_fetchfield('role_id');
		$this->db->sql_freeresult($result);

		return $role_id;
	}
}
