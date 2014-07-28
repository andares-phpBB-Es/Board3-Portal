<?php
/**
*
* @package Quickedit
* @copyright (c) 2014 Marc Alexander ( www.m-a-styles.de )
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace board3\portal\tests\event;

require_once dirname(__FILE__) . '/../../../../../../../tests/template/template_test_case.php';

class listener_test extends \phpbb_template_template_test_case
{
	/** @var \board3\portal\event\listener */
	protected $listener;

	static public $hidden_fields = array();

	public function setup()
	{
		parent::setUp();

		$this->setup_listener();

		global $phpbb_dispatcher;

		$phpbb_dispatcher = new \phpbb\event\dispatcher(new \phpbb_mock_container_builder());
		$this->phpbb_dispatcher = $phpbb_dispatcher;
	}

	public function setup_listener()
	{
		$this->user = $this->getMock('\phpbb\user');
		$this->user->expects($this->any())
			->method('lang')
			->will($this->returnValue('foo'));

		$manager = new \phpbb_mock_extension_manager(dirname(__FILE__) . '/', array());
		$finder = new \phpbb\finder(
			new \phpbb\filesystem(),
			dirname(__FILE__) . '/',
			new \phpbb_mock_cache()
		);
		$finder->set_extensions(array_keys($manager->all_enabled()));

		$this->config = new \phpbb\config\config(array('enable_mod_rewrite' => '1'));
		$provider = new \phpbb\controller\provider();
		$provider->find_routing_files($finder);
		$provider->find(dirname(__FILE__) . '/');
		$this->controller_helper = new \phpbb_mock_controller_helper($this->template, $this->user, $this->config, $provider, $manager, '', 'php', dirname(__FILE__) . '/');

		$this->path_helper = new \phpbb\path_helper(
			new \phpbb\symfony_request(
				new \phpbb_mock_request()
			),
			new \phpbb\filesystem(),
			new \phpbb_mock_request(),
			$this->phpbb_root_path,
			$this->php_ext
		);

		$this->listener = new \board3\portal\event\listener(
			$this->controller_helper,
			$this->path_helper,
			$this->template,
			$this->user,
			'php'
		);
	}

	public function test_construct()
	{
		$this->setup_listener();
		$this->assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
	}

	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.user_setup',
			'core.viewonline_overwrite_location',
			'core.page_header',
		), array_keys(\board3\portal\event\listener::getSubscribedEvents()));
	}

	public function test_viewonline_page()
	{
		$this->phpbb_dispatcher->addListener('core.viewonline_overwrite_location', array($this->listener, 'viewonline_page'));
		$on_page = array(
			'foobar',
			'app',
		);
		$row = array(
			'session_page'	=> 'app.php/portal',
		);
		$location = 'foo';
		$location_url = 'bar.php';

		$vars = array(
			'on_page',
			'row',
			'location',
			'location_url',
		);

		$result = $this->phpbb_dispatcher->trigger_event('core.viewonline_overwrite_location', compact($vars));

		$this->assertEquals('foo', $location);
	}

	public function test_add_portal_link()
	{
		$this->phpbb_dispatcher->addListener('core.page_header', array($this->listener, 'add_portal_link'));

		$vars = array();

		$result = $this->phpbb_dispatcher->trigger_event('core.page_header', compact($vars));

		$this->assertEmpty($result);

		$this->user->data['session_page'] = '/app.php/portal';
		$result = $this->phpbb_dispatcher->trigger_event('core.page_header', compact($vars));

		$this->assertEmpty($result);
	}

	public function test_load_portal_language()
	{
		$this->phpbb_dispatcher->addListener('core.user_setup', array($this->listener, 'load_portal_language'));

		$lang_set_ext = array(array(
			'ext_name' => 'foo/bar',
			'lang_set' => 'bar',
		));
		$vars = array('lang_set_ext');

		$result = $this->phpbb_dispatcher->trigger_event('core.user_setup', compact($vars));

		$this->assertEquals(array(
		array(
			'ext_name' => 'foo/bar',
			'lang_set' => 'bar',
		),
		array(
			'ext_name' => 'board3/portal',
			'lang_set' => 'portal',
		)), $result['lang_set_ext']);
	}
}
