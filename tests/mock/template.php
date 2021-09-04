<?php
/**
*
* @package testing
* @copyright (c) Board3 Group ( www.board3.de )
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace board3\portal\tests\mock;

class template
{
	protected $data = array();

	protected $test_case;

	public function __construct($test_case)
	{
		$this->test_case = $test_case;
	}

	public function assign_block_vars($row, $values)
	{
		$this->test_case->assertEquals(true, is_array($values));

		if (!isset($this->data[$row]))
		{
			$this->data[$row] = array();
		}

		$index = (sizeof($this->data[$row])) ? sizeof($this->data[$row]) : 0;
		foreach ($values as $key => $column)
		{
			$this->test_case->assertArrayNotHasKey($key, $this->data[$row]);
			if (!isset($this->data[$row][$index]))
			{
				$this->data[$row][$index] = array();
			}
			$this->data[$row][$index][$key] = $column;
		}
	}

	public function assign_vars($vars)
	{
		$this->data = array_merge($this->data, $vars);
	}

	public function assign_var($key, $var)
	{
		$this->data[$key] = $var;
	}

	public function assert_equals($data, $row)
	{
		foreach ($data as $key => $value)
		{
			$this->test_case->assertEquals($value, $this->data[$row][$key]);
		}
	}

	public function assert_same($expected, $row)
	{
		$this->test_case->assertSame($expected, $this->data[$row]);
	}

	public function assert_not_exist($row)
	{
		$this->test_case->assertArrayNotHasKey($row, $this->data);
	}

	public function delete_var($key)
	{
		unset($this->data[$key]);
	}

	public function get_row($row)
	{
		return $this->data[$row] ?? null;
	}
}
