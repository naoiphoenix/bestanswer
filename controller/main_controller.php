<?php
/**
 *
 * Best Answer. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, kinerity, https://www.layer-3.org
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\bestanswer\controller;

/**
 * Best Answer main controller.
 */
class main_controller
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\language\language */
	protected $lang;

	/* @var \phpbb\log\log */
	protected $log;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\user */
	protected $user;

	/* @var string phpbb_root_path */
	protected $root_path;

	/* @var string phpEx */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth					$auth
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param \phpbb\language\language			$lang
	 * @param \phpbb\log\log					$log
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\user						$user
	 * @param string							$root_path
	 * @param string							$php_ext
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $lang, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\user $user, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->lang = $lang;
		$this->log = $log;
		$this->request = $request;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Controller for route /answer/{action}
	 *
	 * @param string $action
	 */
	public function change_post_status($action)
	{
		$post_id = $this->request->variable('p', 0);

		// Grab all the data necessary for error checking
		$sql_array = array(
			'SELECT'	=> 't.*, f.*, p.*, u.user_id, u.username, u.user_colour',

			'FROM'		=> array(
				FORUMS_TABLE	=> 'f',
				POSTS_TABLE		=> 'p',
				TOPICS_TABLE	=> 't',
				USERS_TABLE		=> 'u',
			),

			'WHERE'		=> "p.post_id = $post_id AND t.topic_id = p.topic_id AND p.poster_id = u.user_id AND f.forum_id = t.forum_id",
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$topic_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);


		$log_var = $this->auth->acl_get('m_mark_answer', $topic_data['forum_id']) ? 'mod' : 'user';

		// Mark or unmark answers
		if (confirm_box(true))
		{
			if ($action == 'unmark_answer')
			{
				$data = array(
					'answer_post_id'	=> 0,
					'answer_user_id'	=> 0,
				);

				$sql = 'UPDATE ' . TOPICS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE topic_id = ' . (int) $topic_data['topic_id'];
				$this->db->sql_query($sql);

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_answers = user_answers - 1
					WHERE user_id = ' . (int) $topic_data['user_id'];
				$this->db->sql_query($sql);

				$post_author = get_username_string('full', $topic_data['user_id'], $topic_data['username'], $topic_data['user_colour']);
				$this->log->add($log_var, $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_UNMARK_ANSWER', time(), array($topic_data['post_subject'], $post_author));
			}

			if ($action == 'mark_answer')
			{
				// If an answer is already set, we need to update the user's answer count first
				if ($topic_data['answer_post_id'])
				{
					$sql = 'SELECT p.*, u.user_id, u.username, u.user_colour
						FROM ' . POSTS_TABLE . ' p, ' . USERS_TABLE . ' u
						WHERE p.post_id = ' . (int) $topic_data['answer_post_id'] . '
							AND p.poster_id = u.user_id';
					$result = $this->db->sql_query($sql);
					$row = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
								
					$sql = 'UPDATE ' . USERS_TABLE . '
						SET user_answers = user_answers - 1
						WHERE user_id = ' . (int) $row['poster_id'];
					$this->db->sql_query($sql);

					$post_author = get_username_string('full', $row['poster_id'], $row['username'], $row['user_colour']);
					$this->log->add($log_var, $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_UNMARK_ANSWER', time(), array($topic_data['post_subject'], $post_author));
				}

				// Now, update all data
				$data = array(
					'answer_post_id'	=> (int) $post_id,
					'answer_user_id'	=> (int) $topic_data['user_id'],
				);

				$sql = 'UPDATE ' . TOPICS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE topic_id = ' . (int) $topic_data['topic_id'];
				$this->db->sql_query($sql);

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_answers = user_answers + 1
					WHERE user_id = ' . (int) $topic_data['user_id'];
				$this->db->sql_query($sql);

				$post_author = get_username_string('full', $topic_data['user_id'], $topic_data['username'], $topic_data['user_colour']);
				$this->log->add($log_var, $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_MARK_ANSWER', time(), array($topic_data['post_subject'], $post_author));
			}
		}
		else
		{
			confirm_box(false, $this->lang->lang(strtoupper($action) . '_CONFIRM'));
		}

		// Redirect back to the post
		$url = append_sid("{$this->root_path}viewtopic.{$this->php_ext}", 'p=' . (int) $post_id . '#p' . (int) $post_id);
		redirect($url);
	}
}
