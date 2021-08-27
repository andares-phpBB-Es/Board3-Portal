<?php
/**
*
* @package testing
* @copyright (c) Board3 Group ( www.board3.de )
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_functions_simple_test extends \PHPUnit\Framework\TestCase
{
	public function test_ap_validate()
	{
		$this->assertEquals('<br/>woot<br/><ul><li>test</li></ul>', ap_validate('<br />woot<br/><ul><li>test</li><br /></ul>'));
	}
}
