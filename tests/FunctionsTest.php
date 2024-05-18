<?php
	/*
	 * Trinventum - a test for non-database functions.
	 *
	 * Copyright (C) 2024 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
	 *
	 * This file is part of Trinventum (Transaction and Inventory Unified Manager),
	 *  a software that helps manage an e-commerce business.
	 * Trinventum homepage: https://trinventum.sourceforge.io/
	 *
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU Affero General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU Affero General Public License for more details.
	 *
	 * You should have received a copy of the GNU Affero General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	declare(strict_types=1);
	include_once 'webapp/functions.php';

	use PHPUnit\Framework\TestCase;

	final class FunctionsTest extends TestCase
	{
		public function test_trin_error_reporting(): void
		{
			trin_error_reporting();
			$err = error_reporting();
			$this->assertSame(0, $err);
		}

		public function test_trin_get_gender_name(): void
		{
			$this->assertSame('Male', trin_get_gender_name('M'));
			$this->assertSame('Female', trin_get_gender_name('F'));
			$this->assertSame('Child', trin_get_gender_name('C'));
			$this->assertSame('N/A', trin_get_gender_name('-'));
			$this->assertSame('?', trin_get_gender_name('b'));
		}

		public function test_trin_html_escape(): void
		{
			$this->assertSame('&amp;&lt;&gt;', trin_html_escape('&<>'));
		}

		public function test_trin_validate_session(): void
		{
			$this->assertFalse(trin_validate_session());
		}

		public function test_trin_set_success_msg(): void
		{
			$msg = 'abc';
			trin_set_success_msg($msg);
			$this->assertSame($msg, $_SESSION[TRIN_SESS_LAST_SUCCESS]);
		}

		public function test_trin_session(): void
		{
			$key = 'sess_key';
			$value ='sess_value';
			$this->assertFalse(trin_isset_sess($key));
			trin_set_sess($key, $value);
			$this->assertTrue(trin_isset_sess($key));
			$this->assertSame($value, trin_get_sess($key));
			trin_unset_sess($key);
			$this->assertFalse(trin_isset_sess($key));
		}

		public function test_trin_post(): void
		{
			$key = 'post_key';
			$value ='post_value';
			$this->assertFalse(trin_isset_post($key));
			$_POST[$key] = $value;
			$this->assertTrue(trin_isset_post($key));
			$this->assertSame($value, trin_get_post($key));
			unset($_POST[$key]);
			$this->assertFalse(trin_isset_post($key));
		}

		public function test_trin_get(): void
		{
			$key = 'post_key';
			$value ='post_value';
			$this->assertFalse(trin_isset_get($key));
			$_GET[$key] = $value;
			$this->assertTrue(trin_isset_get($key));
			$this->assertSame($value, trin_get_param($key));
			unset($_GET[$key]);
			$this->assertFalse(trin_isset_get($key));
		}

		public function test_trin_server(): void
		{
			$key = 'server_key';
			$value ='server_value';
			$this->assertFalse(trin_isset_server($key));
			$_SERVER[$key] = $value;
			$this->assertTrue(trin_isset_server($key));
			$this->assertSame($value, trin_get_server($key));
			unset($_SERVER[$key]);
			$this->assertFalse(trin_isset_server($key));
		}

		public function test_trin_get_self_action(): void
		{
			$self_php = 'test.php';
			$query_str = 'a=1&b=2';
			$_SERVER['PHP_SELF'] = $self_php;
			unset($_SERVER['QUERY_STRING']);
			$this->assertSame($self_php, trin_get_self_action());
			$_SERVER['QUERY_STRING'] = $query_str;
			$this->assertSame($self_php . '?' . trin_html_escape($query_str), trin_get_self_action());
		}

		public function test_trin_get_self_location(): void
		{
			$self_php = 'test.php';
			$query_str = 'a=1&b=2';
			$_SERVER['PHP_SELF'] = $self_php;
			unset($_SERVER['QUERY_STRING']);
			$this->assertSame($self_php, trin_get_self_location());
			$_SERVER['QUERY_STRING'] = $query_str;
			$this->assertSame($self_php . '?' . trin_html_escape($query_str), trin_get_self_location());
		}
	}
?>
