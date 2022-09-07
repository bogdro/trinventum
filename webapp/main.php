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
<HEAD>
<META HTTP-EQUIV="Content-Type"       CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="Content-Language"   CONTENT="en">
<?php
		trin_meta_lastmod ($t_lastmod);
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<META HTTP-EQUIV="X-Frame-Options"    CONTENT="DENY">
<LINK rel="stylesheet" type="text/css" href="trinventum.css">

<TITLE> Trinventum - main </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include ('header.php');
		include ('menu.php');
?>
<table>
<caption>Products</caption>
<thead><tr>
 <th>ID</th>
 <th>Photo</th>
 <th>Name</th>
 <th>Brand</th>
 <th>Size</th>
 <th>Gender</th>
 <th>Colour</th>
 <th>Count</th>
 <th>Comment</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_prod = FALSE;
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if ($db)
		{
			$products = trin_db_get_product_defs ($db);
			if ($products !== FALSE)
			{
				while (TRUE)
				{
					$next_prod = trin_db_get_next_product ($db, $products);
					if ($next_prod === FALSE)
					{
						break;
					}
					$have_prod = TRUE;
					$prod_det_link = 'details.php?' . TRIN_PROD_DETAIL_PARAM
						. '=' . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID];
					echo '<tr class="c">' .
						"<td><a href=\"$prod_det_link\">" . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID] . '</a></td>' .
						"<td><a href=\"$prod_det_link\">" . $next_prod[TRIN_DB_PROD_DEF_FIELD_PHOTO] . '</a></td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_NAME] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_BRAND] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_SIZE] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_GENDER] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_COLOUR] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_COUNT] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_COMMENT] . '</td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product database: ' . pg_last_error ();
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		if ($error)
		{
?>
<tr><td colspan="9" class="c">Error: <?php echo $error; ?></td></tr>
<?php
		} // $error
		if ((! $have_prod) && (! $error))
		{
?>
<tr><td colspan="9" class="c">No products defined</td></tr>
<?php
		} // ! $have_prod
?>
</tbody>
</table>

<?php
		include ('menu.php');
		include ('footer.php');
?>

</BODY></HTML>
<?php
	} // trin_validate_session ()
?>
