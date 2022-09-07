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
	$db = NULL;
	$validation_failed_fields = array();

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else
	{
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if (!$db)
		{
			$display_form = TRUE;
			$error = 'Cannot connect to database';
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_NAME])
			&& isset ($_FILES[TRIN_DB_PROD_PARAM_PHOTO])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_SIZE])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_LENGTH])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_WIDTH])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COLOUR])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COUNT])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_BRAND])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_GENDER])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COMMENT])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_CATEGORY])
 			&& isset ($_POST[TRIN_DB_PROD_PARAM_COST]))
		{
			$form_validators = array(
				TRIN_DB_PROD_PARAM_LENGTH => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
				TRIN_DB_PROD_PARAM_WIDTH => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
				TRIN_DB_PROD_PARAM_COUNT => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
				TRIN_DB_PROD_PARAM_COST => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
				TRIN_DB_PROD_PARAM_CATEGORY => TRIN_VALIDATION_FIELD_TYPE_NUMBER
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
				if (! trin_db_add_product ($db,
					$_POST[TRIN_DB_PROD_PARAM_NAME],
			       		TRIN_DB_PROD_PARAM_PHOTO,
					$_POST[TRIN_DB_PROD_PARAM_SIZE],
					$_POST[TRIN_DB_PROD_PARAM_LENGTH],
					$_POST[TRIN_DB_PROD_PARAM_WIDTH],
					$_POST[TRIN_DB_PROD_PARAM_COLOUR],
					$_POST[TRIN_DB_PROD_PARAM_COUNT],
					$_POST[TRIN_DB_PROD_PARAM_BRAND],
					$_POST[TRIN_DB_PROD_PARAM_GENDER],
					$_POST[TRIN_DB_PROD_PARAM_COMMENT],
					$_POST[TRIN_DB_PROD_PARAM_CATEGORY],
 					$_POST[TRIN_DB_PROD_PARAM_COST]))
				{
					$display_form = TRUE;
					$error = 'Cannot add product to the database: '
						. trin_db_get_last_error ($db);
				}
			}
			if (! $display_form)
			{
				trin_set_success_msg('Product added successfully');
				header ('Location: main.php');
			}
		}
		else
		{
			$display_form = TRUE;
		}

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

<TITLE> Trinventum - add new product </TITLE>

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
<div class="login_box c">
<?php
			$param_pd_name = '';
			$param_pd_photo = '';
			$param_pd_size = 'N/A';
			$param_pd_length = '0';
			$param_pd_width = '0';
			$param_pd_colour = '';
			$param_pd_count = '';
			$param_pd_brand = '';
			$param_pd_gender = '-';
			$param_pd_comment = '';
			$param_pd_cost = '';
			$param_pd_category = '';
			$param_pd_category_id = '';
			$param_pd_version = 0;

			if (isset ($_POST[TRIN_DB_PROD_PARAM_NAME]))
			{
				$param_pd_name = $_POST[TRIN_DB_PROD_PARAM_NAME];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_PHOTO]))
			{
				$param_pd_photo = $_POST[TRIN_DB_PROD_PARAM_PHOTO];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_SIZE]))
			{
				$param_pd_size = $_POST[TRIN_DB_PROD_PARAM_SIZE];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_LENGTH]))
			{
				$param_pd_length = $_POST[TRIN_DB_PROD_PARAM_LENGTH];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_WIDTH]))
			{
				$param_pd_width = $_POST[TRIN_DB_PROD_PARAM_WIDTH];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_COLOUR]))
			{
				$param_pd_colour = $_POST[TRIN_DB_PROD_PARAM_COLOUR];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_COUNT]))
			{
				$param_pd_count = $_POST[TRIN_DB_PROD_PARAM_COUNT];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_BRAND]))
			{
				$param_pd_brand = $_POST[TRIN_DB_PROD_PARAM_BRAND];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_GENDER]))
			{
				$param_pd_gender = $_POST[TRIN_DB_PROD_PARAM_GENDER];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_COMMENT]))
			{
				$param_pd_comment = $_POST[TRIN_DB_PROD_PARAM_COMMENT];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_COST]))
			{
				$param_pd_cost = $_POST[TRIN_DB_PROD_PARAM_COST];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_VERSION]))
			{
				$param_pd_version = $_POST[TRIN_DB_PROD_PARAM_VERSION];
			}
			if (isset ($_POST[TRIN_DB_PROD_PARAM_CATEGORY]))
			{
				$param_pd_category_id = $_POST[TRIN_DB_PROD_PARAM_CATEGORY];
			}

			if ($db)
			{
				$param_category_option_names_values =
					trin_db_get_product_categories_as_options ($db);
			}
			else
			{
				$param_category_option_names_values = array();
			}
			trin_create_product_def_form (
				trin_get_self_action (), 'Add product',
				TRIN_DB_PROD_PARAM_NAME, $param_pd_name,
				TRIN_DB_PROD_PARAM_PHOTO, $param_pd_photo,
				TRIN_DB_PROD_PARAM_SIZE, $param_pd_size,
				TRIN_DB_PROD_PARAM_LENGTH, $param_pd_length,
				TRIN_DB_PROD_PARAM_WIDTH, $param_pd_width,
				TRIN_DB_PROD_PARAM_COLOUR, $param_pd_colour,
				TRIN_DB_PROD_PARAM_COUNT, $param_pd_count,
				TRIN_DB_PROD_PARAM_BRAND, $param_pd_brand,
				TRIN_DB_PROD_PARAM_GENDER, $param_pd_gender,
				TRIN_DB_PROD_PARAM_COMMENT, $param_pd_comment,
				TRIN_DB_PROD_PARAM_COST, $param_pd_cost,
				TRIN_DB_PROD_PARAM_CATEGORY, $param_pd_category_id,
				$param_category_option_names_values,
				TRIN_DB_PROD_PARAM_VERSION, $param_pd_version,
				$validation_failed_fields, FALSE
			);
?>
</div>

<div class="menu">
<a href="main.php">Return</a>
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
