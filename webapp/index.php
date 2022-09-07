<?php
	/*
	 * Trinventum - the default page.
	 *
	 * Copyright (C) 2015-2022 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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

	session_start ();

	include_once ('constants.php');
	include_once ('functions.php');

	/*
	include_once ('db_functions.php');

	function sess_open ($path, $name) {}

	function sess_close ()
	{
		trin_db_close ($_SESSION[TRIN_SESS_DB_CONN]);
	}

	function sess_read ($id) {}

	function sess_write ($id, $data) {}

	function sess_destroy ($id)
	{
		trin_db_close ($_SESSION[TRIN_SESS_DB_CONN]);
	}

	function sess_gc ($sesslt) {}

	session_set_save_handler ('sess_open', 'sess_close',
		'sess_read', 'sess_write', 'sess_destroy',
		'sess_gc');
	*/

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else
	{
		header ('Location: main.php');
	}
?>
