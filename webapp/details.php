<?php
	/*
	 * Trinventum - product details' page.
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
	$product_updated = FALSE;

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else if (! isset ($_GET[TRIN_PROD_DETAIL_PARAM]))
	{
		header ('Location: main.php');
	}
	else
	{
		$product_id = $_GET[TRIN_PROD_DETAIL_PARAM];
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		$form_validators = array(
			TRIN_DB_PROD_PARAM_LENGTH => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
			TRIN_DB_PROD_PARAM_WIDTH => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
			TRIN_DB_PROD_PARAM_COUNT => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
			TRIN_DB_PROD_PARAM_COST => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
			TRIN_DB_PROD_PARAM_CATEGORY => TRIN_VALIDATION_FIELD_TYPE_NUMBER
			);
		$validation_failed_fields = trin_validate_form($_POST, $form_validators);
		if (!$db)
		{
			$error = 'Cannot connect to database';
		}
		else if (count($validation_failed_fields) != 0)
		{
			$error = 'Form validation failed - check field values: '
				. implode(', ', $validation_failed_fields);
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
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COST])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_CATEGORY])
 			&& isset ($_POST[TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product ($db,
				$product_id,
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
				$_POST[TRIN_DB_PROD_PARAM_COST],
				$_POST[TRIN_DB_PROD_PARAM_CATEGORY],
 				$_POST[TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_NAME])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_NAME])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_NAME . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_name ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_NAME],
				$_POST[TRIN_DB_PROD_PARAM_NAME . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_FILES[TRIN_DB_PROD_PARAM_PHOTO])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_PHOTO])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_PHOTO . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_photo ($db,
				$product_id,
				TRIN_DB_PROD_PARAM_PHOTO,
				$_POST[TRIN_DB_PROD_PARAM_PHOTO . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_SIZE])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_SIZE])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_SIZE . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_size ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_SIZE],
				$_POST[TRIN_DB_PROD_PARAM_SIZE . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_LENGTH])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_LENGTH])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_LENGTH . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_length ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_LENGTH],
				$_POST[TRIN_DB_PROD_PARAM_LENGTH . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_WIDTH])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_WIDTH])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_WIDTH . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_width ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_WIDTH],
				$_POST[TRIN_DB_PROD_PARAM_WIDTH . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_COLOUR])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_COLOUR])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COLOUR . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_colour ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_COLOUR],
				$_POST[TRIN_DB_PROD_PARAM_COLOUR . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_COUNT])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_COUNT])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COUNT . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_count ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_COUNT],
				$_POST[TRIN_DB_PROD_PARAM_COUNT . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_BRAND])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_BRAND])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_BRAND . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_brand ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_BRAND],
				$_POST[TRIN_DB_PROD_PARAM_BRAND . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_GENDER])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_GENDER])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_GENDER . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_gender ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_GENDER],
				$_POST[TRIN_DB_PROD_PARAM_GENDER . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_COMMENT])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_COMMENT])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COMMENT . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_comment ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_COMMENT],
				$_POST[TRIN_DB_PROD_PARAM_COMMENT . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_COST])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_COST])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COST . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_cost ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_COST],
				$_POST[TRIN_DB_PROD_PARAM_COST . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		else if (isset ($_POST[TRIN_DB_PROD_PARAM_CATEGORY])
			&& isset ($_POST[TRIN_FORM_FIELD_SUBMIT_PREFIX . TRIN_DB_PROD_PARAM_CATEGORY])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_CATEGORY . '_' . TRIN_DB_PROD_PARAM_VERSION]))
		{
			if (! trin_db_update_product_category ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_CATEGORY],
				$_POST[TRIN_DB_PROD_PARAM_CATEGORY . '_' . TRIN_DB_PROD_PARAM_VERSION]))
			{
				$error = 'Cannot update product in the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				$product_updated = TRUE;
			}
		}
		if ($product_updated)
		{
			trin_set_success_msg('Product updated successfully');
			header ('Location: ' . trin_get_self_location ());
			exit;
		}
		$update_error = $error;
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

<TITLE> Trinventum - product details </TITLE>
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

<div class="menu">
<a href="main.php">Return</a>
</div>

<p>Details of product <?php echo trin_html_escape($product_id); ?>:</p>
<?php
		$param_pd_name = '';
		$param_pd_photo = '';
		$param_pd_size = 'N/A';
		$param_pd_length = '0';
		$param_pd_width = '0';
		$param_pd_colour = '';
		$param_pd_count = '';
		$param_pd_count_total = 0;
		$param_pd_brand = '';
		$param_pd_gender = '-';
		$param_pd_comment = '';
		$param_pd_cost = '';
		$param_pd_category = '';
		$param_pd_category_id = -1;
		$param_pd_version = 0;

		$error = '';
		$have_prod = FALSE;
		if ($db)
		{
			$product_det = trin_db_get_product_details ($db,
				$product_id);
			if ($product_det !== FALSE)
			{
				$have_prod = TRUE;
				$param_pd_name = $product_det[TRIN_DB_PROD_DEF_FIELD_NAME];
				$param_pd_size = $product_det[TRIN_DB_PROD_DEF_FIELD_SIZE];
				$param_pd_length = $product_det[TRIN_DB_PROD_DEF_FIELD_LENGTH];
				$param_pd_width = $product_det[TRIN_DB_PROD_DEF_FIELD_WIDTH];
				$param_pd_colour = $product_det[TRIN_DB_PROD_DEF_FIELD_COLOUR];
				$param_pd_count = $product_det[TRIN_DB_PROD_DEF_FIELD_COUNT];
				$param_pd_count_total = $product_det[TRIN_DB_PROD_DEF_FIELD_COUNT_TOTAL];
				$param_pd_brand = $product_det[TRIN_DB_PROD_DEF_FIELD_BRAND];
				$param_pd_gender = $product_det[TRIN_DB_PROD_DEF_FIELD_GENDER];
				$param_pd_comment = $product_det[TRIN_DB_PROD_DEF_FIELD_COMMENT];
				$param_pd_category = $product_det[TRIN_DB_PROD_DEF_FIELD_CATEGORY];
				$param_pd_category_id = $product_det[TRIN_DB_PROD_DEF_FIELD_CATEGORY_ID];
				$param_pd_version = $product_det[TRIN_DB_PROD_DEF_FIELD_VERSION];
				if ($product_det[TRIN_DB_PROD_DEF_FIELD_PHOTO] !== '-')
				{
					$photo = '<a href="get_photo.php?'
						. TRIN_PROD_PHOTO_PARAM . '='
						. trin_html_escape ($product_id)
						. '" title="Click to see the original picture">'
						. $product_det[TRIN_DB_PROD_DEF_FIELD_PHOTO] . '</a>';
				}
				else
				{
					$photo = '-';
				}

				echo 	"<ul>\n <li><p>Photo: $photo</p></li>\n" .
					' <li><p>Name: ' . trin_html_escape ($param_pd_name) . "</p></li>\n" .
					' <li><p>Category: ' . trin_html_escape ($param_pd_category) . "</p></li>\n" .
					' <li><p>Brand: ' . trin_html_escape ($param_pd_brand) . "</p></li>\n" .
					' <li><p>Size: ' . trin_html_escape($param_pd_size) . "</p></li>\n" .
					' <li><p>Length: ' . trin_html_escape ($param_pd_length) . "</p></li>\n" .
					' <li><p>Width: ' . trin_html_escape ($param_pd_width) . "</p></li>\n" .
					' <li><p>Gender: ' . trin_html_escape (trin_get_gender_name($param_pd_gender)) . "</p></li>\n" .
					' <li><p>Colour: ' . trin_html_escape ($param_pd_colour) . "</p></li>\n" .
					' <li><p>Count:</p><p>'
						. nl2br(trin_html_escape(str_ireplace('<br>', "\n", $param_pd_count)))
						. "</p></li>\n" .
					' <li><p>Comment: ' . trin_html_escape ($param_pd_comment) . "</p></li>\n" .
					"</ul>\n";
			}
			else
			{
				$error = 'Cannot read product details from the database: '
					. trin_db_get_last_error ($db);
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		trin_display_error($error);

		if ((! $have_prod) && (! $error))
		{
?>
<p class="c">Product not found</p>
<?php
		} // ! $have_prod
?>

<p class="c">
Update product details:
</p>

<div class="login_box c">
<?php
		// if the product failed to be updated,
		// refresh it from the DB and make the user
		// re-enter the data, else display what the use entered
		if (! $update_error)
		{
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
				$param_pd_count_total = $_POST[TRIN_DB_PROD_PARAM_COUNT];
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
			if (isset ($_POST[TRIN_DB_PROD_PARAM_CATEGORY]))
			{
				$param_pd_category_id = $_POST[TRIN_DB_PROD_PARAM_CATEGORY];
			}
			/*
			always take the current version value
			if (isset ($_POST[TRIN_DB_PROD_PARAM_VERSION]))
			{
				$param_pd_version = $_POST[TRIN_DB_PROD_PARAM_VERSION];
			}
			*/
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
			trin_get_self_action (), 'Update product',
			TRIN_DB_PROD_PARAM_NAME, $param_pd_name,
			TRIN_DB_PROD_PARAM_PHOTO, $param_pd_photo,
			TRIN_DB_PROD_PARAM_SIZE, $param_pd_size,
			TRIN_DB_PROD_PARAM_LENGTH, $param_pd_length,
			TRIN_DB_PROD_PARAM_WIDTH, $param_pd_width,
			TRIN_DB_PROD_PARAM_COLOUR, $param_pd_colour,
			TRIN_DB_PROD_PARAM_COUNT, $param_pd_count_total,
			TRIN_DB_PROD_PARAM_BRAND, $param_pd_brand,
			TRIN_DB_PROD_PARAM_GENDER, $param_pd_gender,
			TRIN_DB_PROD_PARAM_COMMENT, $param_pd_comment,
			TRIN_DB_PROD_PARAM_COST, $param_pd_cost,
			TRIN_DB_PROD_PARAM_CATEGORY, $param_pd_category_id,
			$param_category_option_names_values,
			TRIN_DB_PROD_PARAM_VERSION, $param_pd_version,
			$validation_failed_fields, TRUE
		);
?>
</div>

<table>
<caption>Product pieces</caption>
<thead><tr>
 <th>ID</th>
 <th>Status</th>
 <th>Cost</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_prod = FALSE;
		if ($db)
		{
			$products = trin_db_get_product_instances ($db, $product_id);
			if ($products !== FALSE)
			{
				while (TRUE)
				{
					$next_prod = trin_db_get_next_product_instance ($db, $products);
					if ($next_prod === FALSE)
					{
						break;
					}
					$det_link = 'ppdetails.php?' . TRIN_PROD_DETAIL_PARAM
						. '=' . $_GET[TRIN_PROD_DETAIL_PARAM]
						. '&' . TRIN_DB_PROD_INST_FIELD_ID
						. '=' . $next_prod[TRIN_DB_PROD_INST_FIELD_ID];
					$have_prod = TRUE;
					echo '<tr class="c">' .
						'<td><a href="' . trin_html_escape($det_link) . '">'
							. $next_prod[TRIN_DB_PROD_INST_FIELD_ID] . '</a></td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_INST_FIELD_STATUS] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_INST_FIELD_COST] . '</td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product database: '
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
		if ((! $have_prod) && (! $error))
		{
?>
<tr><td colspan="3" class="c">No product pieces found</td></tr>
<?php
		} // ! $have_prod
?>
</tbody>
</table>

<table>
<caption>Product buys</caption>
<thead><tr>
 <th>Buyer</th>
 <th>Quantity</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_buy = FALSE;
		if ($db)
		{
			$buys = trin_db_get_product_buys ($db, $product_id);
			if ($buys !== FALSE)
			{
				while (TRUE)
				{
					$next_buy = trin_db_get_next_product_buy ($db, $buys);
					if ($next_buy === FALSE)
					{
						break;
					}
					$buyer_link = 'mod_buyer.php?' . TRIN_DB_BUYER_PARAM_ID
						. '=' . $next_buy[TRIN_DB_BUYER_PARAM_ID];
					$have_buy = TRUE;
					echo '<tr class="c">' .
						"<td><a href=\"$buyer_link\">"
							. trin_html_escape ($next_buy[TRIN_DB_BUYER_PARAM_NAME]) . '</a></td>' .
						'<td>' . $next_buy[TRIN_DB_TRANS_PARAM_COUNT] . '</td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product buys\' database: '
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
		if ((! $have_buy) && (! $error))
		{
?>
<tr><td colspan="2" class="c">No product buys found</td></tr>
<?php
		} // ! $have_sale
?>
</tbody>
</table>

<table>
<caption>Product sales</caption>
<thead><tr>
 <th>Seller</th>
 <th>Quantity</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_sale = FALSE;
		if ($db)
		{
			$sales = trin_db_get_product_sales ($db, $product_id);
			if ($sales !== FALSE)
			{
				while (TRUE)
				{
					$next_sale = trin_db_get_next_product_sale ($db, $sales);
					if ($next_sale === FALSE)
					{
						break;
					}
					$seller_link = 'mod_seller.php?' . TRIN_DB_SELLER_PARAM_ID
						. '=' . $next_sale[TRIN_DB_SELLER_PARAM_ID];
					$have_sale = TRUE;
					echo '<tr class="c">' .
						"<td><a href=\"$seller_link\">"
							. trin_html_escape ($next_sale[TRIN_DB_SELLER_PARAM_NAME]) . '</a></td>' .
						'<td>' . $next_sale[TRIN_DB_TRANS_PARAM_COUNT] . '</td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product sale database: '
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
		if ((! $have_sale) && (! $error))
		{
?>
<tr><td colspan="2" class="c">No product sales found</td></tr>
<?php
		} // ! $have_sale
?>
</tbody>
</table>

<table>
<caption>Product's history of changes</caption>
<thead><tr>
 <th>Photo was</th>
 <th>Name was</th>
 <th>Brand was</th>
 <th>Size was</th>
 <th>Gender was</th>
 <th>Colour was</th>
 <th>Comment was</th>
 <th>Category was</th>
 <th>Change user</th>
 <th>Change time</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_his = FALSE;
		if ($db)
		{
			$his = trin_db_get_product_history ($db, $product_id);
			if ($his !== FALSE)
			{
				while (TRUE)
				{
					$next_his = trin_db_get_next_product_history_entry ($db, $his);
					if ($next_his === FALSE)
					{
						break;
					}
					$have_his = TRUE;
					echo '<tr class="c">' .
						'<td>' . $next_his[TRIN_DB_PROD_DEF_FIELD_PHOTO] . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_PROD_DEF_FIELD_NAME]) . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_PROD_DEF_FIELD_BRAND]) . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_DEF_FIELD_SIZE] . '</td>' .
						'<td>' . trin_html_escape (trin_get_gender_name($next_his[TRIN_DB_PROD_DEF_FIELD_GENDER])) . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_PROD_DEF_FIELD_COLOUR]) . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_PROD_DEF_FIELD_COMMENT]) . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_PROD_DEF_FIELD_CATEGORY]) . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_DEF_FIELD_USER] . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_DEF_FIELD_TIMESTAMP] . '<hr></td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product history database: '
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
<tr><td colspan="10" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_his) && (! $error))
		{
?>
<tr><td colspan="10" class="c">No product history found</td></tr>
<?php
		} // ! $have_sale
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
	} // trin_validate_session ()
?>
