<?php
/**
 *
 * @package Board3 Portal Testing
 * @copyright (c) Board3 Group ( www.board3.de )
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace board3\portal\tests\testframework;

abstract class ui_test_case extends \phpbb_ui_test_case
{
	static protected function setup_extensions()
	{
		return array('board3/portal');
	}
}
