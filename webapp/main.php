<?php
	/*
	 * Trinventum - the main page.
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

	session_start();

	include_once 'inc/constants.php';
	include_once 'inc/functions.php';
	include_once 'inc/db_functions.php';

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<HTML lang="en">
<HEAD profile="http://www.w3.org/2005/10/profile">
<META HTTP-EQUIV="Content-Type"       CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="Content-Language"   CONTENT="en">
<?php
		trin_meta_lastmod ($t_lastmod);
		trin_include_css ();
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">

<TITLE> Trinventum - main </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include 'inc/header.php';
		include 'inc/menu.php';

		trin_display_success();
?>

<p>Choose a product category to view:</p>
<?php
		$error = '';
		$have_cat = FALSE;
		$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
			trin_get_sess(TRIN_SESS_DB_PASS),
			trin_get_sess(TRIN_SESS_DB_DBNAME),
			trin_get_sess(TRIN_SESS_DB_HOST));
		if ($db)
		{
			$categories = trin_db_get_product_categories ($db);
			if ($categories !== FALSE)
			{
				echo "<ul>\n";
				//$cat_det_link = 'cat_products.php?' . TRIN_CAT_DETAIL_PARAM . '=0';
				//echo "<li><a href=\"$cat_det_link\">Uncategorised</a></li>\n";
				while (TRUE)
				{
					$next_cat = trin_db_get_next_product_category ($db, $categories);
					if ($next_cat === FALSE)
					{
						break;
					}
					$have_cat = TRUE;
					$cat_det_link = 'cat_products.php?' . TRIN_CAT_DETAIL_PARAM
						. '=' . $next_cat[TRIN_DB_PROD_CAT_FIELD_ID];
					echo "<li><a href=\"$cat_det_link\">"
						. trin_html_escape ($next_cat[TRIN_DB_PROD_CAT_FIELD_NAME])
						. "</a></li>\n";
				}
				echo "</ul>\n";
			}
			else
			{
				$error = 'Cannot read category database: ' . trin_db_get_last_error ($db);
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		if ($error)
		{
			trin_display_error($error);
		}
?>

or <a href="all_products.php">view all product types</a>.

<?php
		include 'inc/menu.php';
		include 'inc/footer.php';
?>

</BODY></HTML>
<?php
	} // trin_validate_session ()
?>
