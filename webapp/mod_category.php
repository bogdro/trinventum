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

	trin_error_reporting();

	include_once ('db_functions.php');

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);

	$display_form = FALSE;
	$error = '';
	$validation_failed_fields = array();
	$db = NULL;

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else if (! isset ($_GET[TRIN_CAT_DETAIL_PARAM]))
	{
		header ('Location: categories.php');
	}
	else if ($_GET[TRIN_CAT_DETAIL_PARAM] == '0')
	{
		header ('Location: categories.php');
	}
	else
	{
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if (isset ($_POST[TRIN_DB_PROD_CAT_FIELD_NAME]))
		{
			$form_validators = array(
				TRIN_DB_PROD_CAT_FIELD_NAME => TRIN_VALIDATION_FIELD_TYPE_REQUIRED
				);
			$validation_failed_fields = trin_validate_form($_POST, $form_validators);
			if (count($validation_failed_fields) != 0)
			{
				$display_form = TRUE;
				$error = 'Form validation failed - check field values: '
					. implode(', ', $validation_failed_fields);
			}
			else
			{
				if (!$db)
				{
					$display_form = TRUE;
					$error = 'Cannot connect to database';
				}
				if (! trin_db_update_category ($db,
					$_GET[TRIN_CAT_DETAIL_PARAM],
					$_POST[TRIN_DB_PROD_CAT_FIELD_NAME],
					$_POST[TRIN_DB_PROD_CAT_FIELD_VERSION]))
				{
					$display_form = TRUE;
					$error = 'Cannot update category in the database: '
						. trin_db_get_last_error ($db);
				}
				else
				{
					trin_set_success_msg('Category updated successfully');
				}
			}
			if (! $display_form)
			{
				header ('Location: categories.php');
				exit;
			}
		}
		else
		{
			$display_form = TRUE;
		}

		$update_error = $error;
		if ($display_form)
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

<TITLE> Trinventum - modify product category </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
			include ('header.php');
			include ('menu.php');

			trin_display_error($error);
?>
<div class="login_box">
<?php
			$param_category_name = '';
			$param_category_version = 0;

			$category = trin_db_get_product_category_details ($db, $_GET[TRIN_CAT_DETAIL_PARAM]);
			if ($category !== FALSE)
			{
				$param_category_name = $category[TRIN_DB_PROD_CAT_FIELD_NAME];
				$param_category_version = $category[TRIN_DB_PROD_CAT_FIELD_VERSION];
			}
			else
			{
				trin_display_error ('Cannot read category details: No data');
			}

			// if the category failed to be upated,
			// refresh it from the DB and make the user
			// re-enter the data, else display what the use entered
			if (! $update_error)
			{
				if (isset ($_POST[TRIN_DB_PROD_CAT_FIELD_NAME]))
				{
					$param_category_name = $_POST[TRIN_DB_PROD_CAT_FIELD_NAME];
				}
				/*
				always take the current version value
				if (isset ($_POST[TRIN_DB_SELLER_PARAM_VERSION]))
				{
					$param_pp_version = $_POST[TRIN_DB_SELLER_PARAM_VERSION];
				}
				*/
			}

			trin_create_category_form (
				trin_get_self_action (), 'Update category',
				TRIN_DB_PROD_CAT_FIELD_NAME, $param_category_name,
				TRIN_DB_PROD_CAT_FIELD_VERSION, $param_category_version,
				$validation_failed_fields
			);
?>
</div>

<table>
<caption>Category's history of changes</caption>
<thead><tr>
 <th>Name was</th>
 <th>Change user</th>
 <th>Change time</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_cat = FALSE;
		if ($db)
		{
			$cat_his = trin_db_get_product_category_history ($db,
				$_GET[TRIN_CAT_DETAIL_PARAM]);
			if ($cat_his !== FALSE)
			{
				while (TRUE)
				{
					$next_his = trin_db_get_next_product_category_history_entry ($db, $cat_his);
					if ($next_his === FALSE)
					{
						break;
					}
					$have_cat = TRUE;
					echo '<tr class="c">' .
						'<td>' . $next_his[TRIN_DB_PROD_CAT_FIELD_NAME] . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_CAT_FIELD_USER] . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_CAT_FIELD_TIMESTAMP] . '</td></tr>'
						. "\n";
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
<tr><td colspan="3" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_cat) && (! $error))
		{
?>
<tr><td colspan="3" class="c">No category history found</td></tr>
<?php
		} // ! $have_cat
?>
</tbody>
</table>

<div class="menu">
<a href="categories.php">Return</a>
</div>

<?php
			include ('menu.php');
			include ('footer.php');
?>

</BODY></HTML>
<?php
		} //$display_form
	} // trin_validate_session()
?>
