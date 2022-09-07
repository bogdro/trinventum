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
	else if (! isset ($_GET[TRIN_CAT_DETAIL_PARAM]))
	{
		header ('Location: main.php');
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
		trin_include_css ();
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<META HTTP-EQUIV="X-Frame-Options"    CONTENT="DENY">

<TITLE> Trinventum - category products </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include ('header.php');
		include ('menu.php');

		trin_display_success();

		$cat_name = '';
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if ($db)
		{
			$cat = trin_db_get_product_category_details ($db,
				$_GET[TRIN_CAT_DETAIL_PARAM]);
			if ($cat !== FALSE)
			{
				$cat_name = $cat[TRIN_DB_PROD_CAT_FIELD_NAME];
			}
		}
?>

<table>
<caption>Products of category "<?php echo $cat_name; ?>"</caption>
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
		if ($db)
		{
			$products = trin_db_get_product_defs_of_category ($db, $_GET[TRIN_CAT_DETAIL_PARAM]);
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
					$counts = trin_db_count_products($db, $next_prod[TRIN_DB_PROD_DEF_FIELD_ID]);
					$rowclass = 'c';
					if ((!isset ($counts['READY'])) || ($counts['READY'] == 0))
					{
						if ((!isset ($counts['SELLING'])) || ($counts['SELLING'] == 0))
						{
							$rowclass .= ' nopieces';
						}
						else
						{
							$rowclass .= ' noready';
						}
					}
					$prod_det_link = 'details.php?' . TRIN_PROD_DETAIL_PARAM
						. '=' . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID];
					echo "<tr class=\"$rowclass\">" .
						"<td><a href=\"$prod_det_link\">" . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID] . '</a></td>' .
						"<td><a href=\"$prod_det_link\">" . $next_prod[TRIN_DB_PROD_DEF_FIELD_PHOTO] . '</a></td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_NAME] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_BRAND] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_SIZE] . '</td>' .
						'<td>' . trin_get_gender_name($next_prod[TRIN_DB_PROD_DEF_FIELD_GENDER]) . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_COLOUR] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_COUNT] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_DEF_FIELD_COMMENT] . '<hr></td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product database: ' . trin_db_get_last_error ($db);
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		if ($error)
		{
?>
<tr><td colspan="9" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_prod) && (! $error))
		{
?>
<tr><td colspan="9" class="c">No products for the given category defined</td></tr>
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
