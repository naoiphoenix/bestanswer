<?php
/**
 *
 * Best Answer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ANSWERS'	=> 'Ответы',

	'ENABLE_ANSWER'			=> 'Включить лучший ответ',
	'ENABLE_ANSWER_EXPLAIN'	=> 'Если включено, автор темы (если разрешено) и модераторы разделов (где разрешено) будут способны отмечать ответ как лучший.',

	'TO_POST'	=> 'Перейти к полному ответу',

	'LOG_MARK_ANSWER'	=> '<strong>Отмечено лучшим ответом</strong><br />» %1$s пользователем %2$s',
	'LOG_UNMARK_ANSWER'	=> '<strong>Снята отметка лучшего ответа</strong><br />» %1$s пользователем %2$s',

	'MARK_ANSWER'			=> 'Отметить ответ',
	'MARK_ANSWER_CONFIRM'	=> 'Вы уверены, что хотите отметить этот ответ как лучший?',

	'TOTAL_ANSWERS'	=> 'Всего ответов',

	'UNMARK_ANSWER'			=> 'Снять отметку',
	'UNMARK_ANSWER_CONFIRM'	=> 'Вы уверены, что хотите снять отметку лучшего с этого ответа?',

	'MARK_ANSWER_NOTIFICATION'			=> '%1$s отметил Ваш ответ в теме “%2$s” как лучший ответ.',
	'UNMARK_ANSWER_NOTIFICATION'		=> '%1$s снял отметку лучшего с вашего поста в теме “%2$s”.',
	'NOTIFICATION_TYPE_MARK_ANSWER'		=> 'Кто-то отметил ваш ответ в качестве лучшего.',
	'NOTIFICATION_TYPE_UNMARK_ANSWER'	=> 'Кто-то снял отметку лучшего с вашего ответа.',

	'BUTTON_MARK'	=> 'Отметить ответ',
	'BUTTON_UNMARK'	=> 'Снять отметку',
));
