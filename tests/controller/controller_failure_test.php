<?php

/**
 *
 * @package phpBB Extension - Active Notifications
 * @copyright (c) 2016 Lucifer <https://www.anavaro.com>
 * @copyright (c) 2016 kasimi <https://kasimi.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace anavaro\activenotifications\tests\controller;

class controller_failure_test extends controller_base
{
	/**
	 * @return array
	 */
	public function controller_failure_data()
	{
		return [
			'is_guest' => [
				1,		// User ID
				true,	// Is registered
				false,	// Is bot
				true,	// Is ajax request
				403,
				'NO_AUTH_OPERATION',
			],

			'is_not_registered' => [
				2,		// User ID
				false,	// Is registered
				false,	// Is bot
				true,	// Is ajax request
				403,
				'NO_AUTH_OPERATION',
			],

			'is_bot' => [
				2,		// User ID
				true,	// Is registered
				true,	// Is bot
				true,	// Is ajax request
				403,
				'NO_AUTH_OPERATION',
			],

			'is_not_ajax' => [
				2,		// User ID
				true,	// Is registered
				false,	// Is bot
				false,	// Is ajax request
				403,
				'NO_AUTH_OPERATION',
			],
		];
	}

	/**
	 * @param $user_id
	 * @param $is_registered
	 * @param $is_bot
	 * @param $is_ajax
	 * @param $status_code
	 * @param $content
	 * @dataProvider controller_failure_data
	 */
	public function test_controller_failure($user_id, $is_registered, $is_bot, $is_ajax, $status_code, $content)
	{
		$this->assertInstanceOf('\anavaro\activenotifications\controller\main_controller', $this->activenotifications_controller);

		$this->set_user_data([
			'user_id'		=> $user_id,
			'is_registered'	=> $is_registered,
			'is_bot'		=> $is_bot,
		]);

		$this->set_config_data([
			'allow_board_notifications' => true,
		]);

		$this->request->expects($this->any())
			->method('is_ajax')
			->will($this->returnValue($is_ajax));

		$this->request->expects($this->never())
			->method('variable')
			->with('last');

		try
		{
			$this->activenotifications_controller->base();
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($content, $exception->getMessage());
		}
	}
}
