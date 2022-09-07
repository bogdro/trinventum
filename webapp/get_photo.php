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

	session_start ();

	include_once ('constants.php');
	include_once ('functions.php');
	include_once ('db_functions.php');

	if (trin_validate_session ())
	{
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if ($db)
		{
			// a type must be specified, but it seems that
			// any type will do, as long as it's an image type
			header ('Content-Type: image/jpeg');

			if (isset ($_GET[TRIN_PROD_PHOTO_PARAM]))
			{
				echo trin_db_get_photo ($db, $_GET[TRIN_PROD_PHOTO_PARAM]);
			}
			else if (isset ($_GET[TRIN_PROD_PHOTO_PARAM_HIS])
				&& isset ($_GET[TRIN_PROD_PHOTO_PARAM_HIS_VERSION]))
			{
				echo trin_db_get_history_photo ($db,
					$_GET[TRIN_PROD_PHOTO_PARAM_HIS],
					$_GET[TRIN_PROD_PHOTO_PARAM_HIS_VERSION]);
			}
		}
	}
?>
