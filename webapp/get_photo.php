<?php
	/*
	 * Trinventum - a script to get a product's photo.
	 *
	 * Copyright (C) 2015-2024 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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

	include_once 'constants.php';
	include_once 'functions.php';
	include_once 'db_functions.php';

	if (trin_validate_session ())
	{
		$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
			trin_get_sess(TRIN_SESS_DB_PASS),
			trin_get_sess(TRIN_SESS_DB_DBNAME),
			trin_get_sess(TRIN_SESS_DB_HOST));
		if ($db)
		{
			// a type must be specified, but it seems that
			// any type will do, as long as it's an image type
			header ('Content-Type: image/jpeg');

			if (trin_isset_get(TRIN_PROD_PHOTO_PARAM))
			{
				echo trin_db_get_photo ($db, trin_get_param(TRIN_PROD_PHOTO_PARAM));
			}
			else if (trin_isset_get(TRIN_PROD_PHOTO_PARAM_HIS)
				&& trin_isset_get(TRIN_PROD_PHOTO_PARAM_HIS_VERSION))
			{
				echo trin_db_get_history_photo ($db,
					trin_get_param(TRIN_PROD_PHOTO_PARAM_HIS),
					trin_get_param(TRIN_PROD_PHOTO_PARAM_HIS_VERSION));
			}
		}
	}
?>
