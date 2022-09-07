<?php
	/*
	Copyright (C) 2015-2022 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net

	This file is part of Trinventum (Transaction and Inventory Unified Manager),
	 a software that helps manage an e-commerce business.
	Trinventum homepage: https://trinventum.sourceforge.io/

	 This program is free software: you can redistribute it and/or modify
	 it under the terms of the GNU Affero General Public License as published by
	 the Free Software Foundation, either version 3 of the License, or
	 (at your option) any later version.

	 This program is distributed in the hope that it will be useful,
	 but WITHOUT ANY WARRANTY; without even the implied warranty of
	 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 GNU Affero General Public License for more details.

	 You should have received a copy of the GNU Affero General Public License
	 along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	session_start();

	include_once ('constants.php');
	include_once ('functions.php');
	include_once ('db_functions.php');

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}

	$conn = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);

	$trin_db_ver = trin_db_get_version ($conn);
	if ((int)$trin_db_ver == 0)
	{
		// just run the full script
		$file = file_get_contents ("sql/trinventum-full.pgsql");
		if ($file !== FALSE)
		{
			if (! trin_db_query ($conn, $file))
			{
				die ("Can't update DB version from $trin_db_ver to "
					. TRIN_EXPECTED_DB_VERSION
					. ': ' . trin_db_get_last_error ());
			}
		}
		else
		{
			die ("Can't update DB version from $trin_db_ver to "
				. TRIN_EXPECTED_DB_VERSION
				. ": can't read file trinventum-full.pgsql");
		}
	}
	else if ((int)$trin_db_ver < (int)TRIN_EXPECTED_DB_VERSION)
	{
		for ($i = (int)$trin_db_ver + 1; $i <= (int)TRIN_EXPECTED_DB_VERSION; $i++)
		{
			// run the missing scripts
			$file = file_get_contents ("sql/trinventum-v$i.pgsql");
			if ($file !== FALSE)
			{
				if (! trin_db_query ($conn, 'begin'))
				{
					die ("Can't update DB version from $trin_db_ver to "
						. TRIN_EXPECTED_DB_VERSION
						. ' - cannot start transaction: ' . trin_db_get_last_error ());
				}
				if (! trin_db_query ($conn, $file))
				{
					trin_db_query ($conn, 'rollback');
					die ("Can't update DB version from $trin_db_ver to "
						. TRIN_EXPECTED_DB_VERSION
						. ': ' . trin_db_get_last_error ());
				}
				trin_db_query ($conn, 'commit');
			}
			else
			{
				die ("Can't update DB version from $trin_db_ver to "
					. TRIN_EXPECTED_DB_VERSION
					. ": can't read file trinventum-v$i.pgsql");
			}
		}
	}
	trin_db_close ($conn);

	header ('Location: main.php');
?>
