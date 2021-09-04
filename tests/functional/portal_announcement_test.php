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
class phpbb_functional_portal_announcement_test extends \board3\portal\tests\testframework\functional_test_case
{
	public function setUp(): void
	{
		parent::setUp();
		$this->purge_cache();

		$this->login();
		$this->admin_login();
	}

	public function test_with_announce()
	{
		// Create topic as announcement
		$data = $this->create_topic(2, 'Portal-announce', 'This is an announcement for the portal', array(
			'topic_type'	=> POST_ANNOUNCE,
		));

		if (isset($data))
		{
			// no errors should appear on portal
			self::request('GET', 'app.php/portal');
		}
	}

	public function test_with_global()
	{
		// Create topic as announcement
		$data = $this->create_topic(2, 'Portal-announce-global', 'This is a global announcement for the portal', array(
			'topic_type'	=> POST_GLOBAL,
		));

		if (isset($data))
		{
			// no errors should appear on portal
			self::request('GET', 'app.php/portal');
		}
	}

	/**
	* @depends test_with_announce
	*/
	public function test_after_announce()
	{
		$this->logout();
		self::request('GET', 'app.php/portal');
	}

	public function test_shortened_message()
	{
		$this->purge_cache();

		// Create topic as announcement
		$data = $this->create_topic(2, 'Portal-announce-global', str_repeat('This is a global announcement for the portal', 6), array(
			'topic_type'	=> POST_GLOBAL,
		));

		if (isset($data))
		{
			// no errors should appear on portal
			$crawler = self::request('GET', 'app.php/portal');
			$this->assertStringContainsString('This is a global announc ...', $crawler->text());
		}
	}
}
