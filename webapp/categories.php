<?php
	/*
	 * Trinventum - categories' page.
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

	include_once 'constants.php';
	include_once 'functions.php';

	trin_error_reporting();

	include_once 'db_functions.php';

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);

	$error = '';
	$validation_failed_fields = array();
	$db = NULL;

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else
	{
		$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
			trin_get_sess(TRIN_SESS_DB_PASS),
			trin_get_sess(TRIN_SESS_DB_DBNAME),
			trin_get_sess(TRIN_SESS_DB_HOST));
		if (trin_isset_post(TRIN_DB_PROD_CAT_FIELD_NAME))
		{
			$form_validators = array(
				TRIN_DB_PROD_CAT_FIELD_NAME => TRIN_VALIDATION_FIELD_TYPE_REQUIRED
				);
			$validation_failed_fields = trin_validate_form($_POST, $form_validators);
			if (count($validation_failed_fields) != 0)
			{
				$error = 'Form validation failed - check field values: '
					. implode(', ', $validation_failed_fields);
			}
			else
			{
				if (!$db)
				{
					$error = 'Cannot connect to database';
				}
				if (! trin_db_add_product_category ($db,
					trin_get_post(TRIN_DB_PROD_CAT_FIELD_NAME)))
				{
					$error = 'Cannot add category to the database: '
						. trin_db_get_last_error ($db);
				}
				else
				{
					trin_set_success_msg('Category added successfully');
					header ('Location: ' . trin_get_self_location ());
					exit;
				}
			}
		}
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

<TITLE> Trinventum - manage product categories </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include 'header.php';
		include 'menu.php';

		trin_display_error($error);
		trin_display_success();
?>
<div class="login_box">
<?php

		$param_category_name = '';
		$param_category_version = 0;

		// reset the form on success, leave the values on error
		if ($error && trin_isset_post(TRIN_DB_PROD_CAT_FIELD_NAME))
		{
			$param_category_name = trin_get_post(TRIN_DB_PROD_CAT_FIELD_NAME);
		}

		trin_create_category_form (
			trin_get_self_action (), 'Add category',
			TRIN_DB_PROD_CAT_FIELD_NAME, $param_category_name,
			TRIN_DB_PROD_CAT_FIELD_VERSION, $param_category_version,
			$validation_failed_fields
		);
?>
</div>

<table>
<caption>Registered categories</caption>
<thead><tr>
 <th>ID</th>
 <th>Name</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_category = FALSE;
		if ($db)
		{
			$categories = trin_db_get_product_categories ($db);
			if ($categories !== FALSE)
			{
				while (TRUE)
				{
					$next_category = trin_db_get_next_product_category ($db, $categories);
					if ($next_category === FALSE)
					{
						break;
					}
					$have_category = TRUE;
					if ($next_category[TRIN_DB_PROD_CAT_FIELD_ID] != 0)
					{
						$category_det_link = 'mod_category.php?' . TRIN_CAT_DETAIL_PARAM
							. '=' . $next_category[TRIN_DB_PROD_CAT_FIELD_ID];
						echo '<tr class="c">' .
							"<td><a href=\"$category_det_link\">"
								. $next_category[TRIN_DB_PROD_CAT_FIELD_ID]
								. '</a></td>' .
							"<td><a href=\"$category_det_link\">"
								. trin_html_escape (
									$next_category[TRIN_DB_PROD_CAT_FIELD_NAME]
								)
								. '</a></td></tr>'
							. "\n";
					}
					else
					{
						echo '<tr class="c">' .
							'<td>' . $next_category[TRIN_DB_PROD_CAT_FIELD_ID] . '</td>' .
							'<td>' .
							trin_html_escape ($next_category[TRIN_DB_PROD_CAT_FIELD_NAME])
							. '</td></tr>'
							. "\n";
					}
				}
			}
			else
			{
				$error = 'Cannot read category database: '
					. trin_db_get_last_error ($db);
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		if ($error)
		{
?>
<tr><td colspan="2" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_category) && (! $error))
		{
?>
<tr><td colspan="2" class="c">No categories found</td></tr>
<?php
		} // ! $have_category
?>
</tbody>
</table>

<div class="menu">
<a href="main.php">Return</a>
</div>

<?php
		include 'menu.php';
		include 'footer.php';
?>

</BODY></HTML>
<?php
	} // trin_validate_session()
?>
