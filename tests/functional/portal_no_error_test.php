<?php
/**
*
* @package testing
* @copyright (c) 2013 Board3 Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @group functional
*/
class phpbb_functional_portal_no_error_test extends \board3\portal\tests\testframework\functional_test_case
{
	public function setUp()
	{
		parent::setUp();

		$this->login();
		$this->admin_login();
	}

	public function test_vanilla_board()
	{
		self::request('GET', 'app.php/portal');
	}

	public function data_portal_all_pages()
	{
		return array(
			array(1),
			array(2),
		);
	}

	/**
	 * @dataProvider data_portal_all_pages
	 * @dependsOn test_enable_subsilver
	 */
	public function test_portal_all_pages($style_id)
	{
		$crawler = self::request('GET', 'index.php?style=' . $style_id);
		$this->assertNotContains('Menu', $crawler->text());

		$crawler = self::request('GET', 'adm/index.php?i=-board3-portal-acp-portal_module&mode=config&sid=' . $this->sid);

		$form = $crawler->selectButton('submit')->form(array(
			'config[board3_show_all_pages]'		=> 1,
			'board3_show_all_side'		=> 0,
		));
		$crawler = self::submit($form);
		$this->assertContainsLang('CONFIG_UPDATED', $crawler->text());

		$crawler = self::request('GET', 'index.php?style=' . $style_id);
		$this->assertContains('Menu', $crawler->text());

		$crawler = self::request('GET', 'adm/index.php?i=-board3-portal-acp-portal_module&mode=config&sid=' . $this->sid);

		$form = $crawler->selectButton('submit')->form(array(
			'config[board3_show_all_pages]'		=> 1,
			'board3_show_all_side'		=> 1,
		));
		$crawler = self::submit($form);
		$this->assertContainsLang('CONFIG_UPDATED', $crawler->text());

		$crawler = self::request('GET', 'index.php?style=' . $style_id);
		$this->assertNotContains('Board Style', $crawler->text());
		$this->assertContains('User menu', $crawler->text());

		$crawler = self::request('GET', 'adm/index.php?i=-board3-portal-acp-portal_module&mode=config&sid=' . $this->sid);

		$form = $crawler->selectButton('submit')->form(array(
			'config[board3_show_all_pages]'		=> 0,
			'board3_show_all_side'		=> 0,
		));
		$crawler = self::submit($form);
		$this->assertContainsLang('CONFIG_UPDATED', $crawler->text());

		$crawler = self::request('GET', 'index.php?style=' . $style_id);
		$this->assertNotContains('Menu', $crawler->text());
	}
}