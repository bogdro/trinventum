<?php
	/*
	 * Trinventum - product piece details.
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

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else if (! isset ($_GET[TRIN_DB_PROD_INST_FIELD_ID]))
	{
		header ('Location: main.php');
	}
	else
	{
		$product_inst_id = $_GET[TRIN_DB_PROD_INST_FIELD_ID];
		$return_link = 'main.php';
		if (isset ($_GET[TRIN_PROD_DETAIL_PARAM]))
		{
			$return_link = 'details.php?'
				. TRIN_PROD_DETAIL_PARAM . '=' . $_GET[TRIN_PROD_DETAIL_PARAM];
		}
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if (isset ($_POST[TRIN_DB_PROD_INST_FIELD_STATUS])
			&& isset ($_POST[TRIN_DB_PROD_INST_FIELD_COST])
			&& isset ($_POST[TRIN_DB_PROD_INST_FIELD_VERSION]))
		{
			$form_validators = array(
				TRIN_DB_PROD_INST_FIELD_COST => TRIN_VALIDATION_FIELD_TYPE_NUMBER
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
				if (! trin_db_update_product_instance ($db,
					$product_inst_id,
					$_POST[TRIN_DB_PROD_INST_FIELD_STATUS],
					$_POST[TRIN_DB_PROD_INST_FIELD_COST],
					$_POST[TRIN_DB_PROD_INST_FIELD_VERSION]))
				{
					$error = 'Cannot update product piece in the database: '
						. trin_db_get_last_error ($db);
				}
				else
				{
					trin_set_success_msg('Product piece updated successfully');
					header ("Location: $return_link");
					exit;
				}
			}
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

<TITLE> Trinventum - product piece details </TITLE>
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
?>
<div class="menu">
<a href="<?php echo trin_html_escape($return_link); ?>">Return</a>
</div>

<p>Details of product piece <?php echo trin_html_escape($product_inst_id); ?>:</p>
<?php
		$param_pp_status = '';
		$param_pp_cost = '';
		$param_pp_version = 0;

		$error = '';
		$have_prod = FALSE;
		if ($db)
		{
			$product_det = trin_db_get_product_instance_details ($db,
				$product_inst_id);
			if ($product_det !== FALSE)
			{
				$have_prod = TRUE;
				$param_pp_status = $product_det[TRIN_DB_PROD_INST_FIELD_STATUS];
				$param_pp_cost = $product_det[TRIN_DB_PROD_INST_FIELD_COST];
				$param_pp_version = $product_det[TRIN_DB_PROD_INST_FIELD_VERSION];

				echo 	'<p>Status: ' . trin_html_escape ($param_pp_status) . '</p>' .
					'<p>Cost: ' . $param_pp_cost . '</p>'
					;
			}
			else
			{
				$error = 'Cannot read product instance details from the database: '
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
Update product piece details (warning - this updates ALL the given details):
</p>

<div class="login_box c">
<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="POST">
<?php
		// if the product piece failed to be updated,
		// refresh it from the DB and make the user
		// re-enter the data, else display what the use entered
		if (! $update_error)
		{
			if (isset ($_POST[TRIN_DB_PROD_INST_FIELD_STATUS]))
			{
				$param_pp_status = $_POST[TRIN_DB_PROD_INST_FIELD_STATUS];
			}
			if (isset ($_POST[TRIN_DB_PROD_INST_FIELD_COST]))
			{
				$param_pp_cost = $_POST[TRIN_DB_PROD_INST_FIELD_COST];
			}
			/*
			always take the current version value
			if (isset ($_POST[TRIN_DB_PROD_INST_FIELD_VERSION]))
			{
				$param_pp_version = $_POST[TRIN_DB_PROD_INST_FIELD_VERSION];
			}
			*/
		}

		if ($param_pp_status == TRIN_PROD_STATUS_SOLD)
		{
			trin_create_text_input('hidden', '',
				TRIN_DB_PROD_INST_FIELD_STATUS,
				$param_pp_status, $validation_failed_fields);
		}
		else // ! SOLD
		{
?>
<p>
Status:
<?php
			trin_create_select(TRIN_DB_PROD_INST_FIELD_STATUS,
				$param_pp_status,
				array(TRIN_PROD_STATUS_READY,
					TRIN_PROD_STATUS_SALE_IN_PROGRESS),
				array(TRIN_PROD_STATUS_READY,
					TRIN_PROD_STATUS_SALE_IN_PROGRESS),
				$validation_failed_fields)
?>
</p>
<?php
		} // SOLD
?>

<p>
Cost:
<?php
		trin_create_text_input('hidden', '', TRIN_DB_PROD_INST_FIELD_VERSION,
			$param_pp_version, $validation_failed_fields);
		trin_create_text_input('text', '20', TRIN_DB_PROD_INST_FIELD_COST,
			$param_pp_cost, $validation_failed_fields);
?>
</p>

<p>
<input type="submit" value="Update product piece">
<label for="reset"></label>
<input type="reset" id="reset" value="Reset">
</p>

</form>
</div>

<table>
<caption>Product piece's history of changes</caption>
<thead><tr>
 <th>Status was</th>
 <th>Cost was</th>
 <th>Change user</th>
 <th>Change time</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_prod = FALSE;
		if ($db)
		{
			$product_his = trin_db_get_product_instance_history ($db, $product_inst_id);
			if ($product_his !== FALSE)
			{
				while (TRUE)
				{
					$next_his = trin_db_get_next_product_instance_hist_entry ($db, $product_his);
					if ($next_his === FALSE)
					{
						break;
					}
					$have_prod = TRUE;
					echo '<tr class="c">' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_PROD_INST_FIELD_STATUS]) . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_INST_FIELD_COST] . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_INST_FIELD_USER] . '</td>' .
						'<td>' . $next_his[TRIN_DB_PROD_INST_FIELD_TIMESTAMP] . '</td></tr>'
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
<tr><td colspan="4" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_prod) && (! $error))
		{
?>
<tr><td colspan="4" class="c">No product piece history found</td></tr>
<?php
		} // ! $have_prod
?>
</tbody>
</table>

<div class="menu">
<a href="<?php echo trin_html_escape($return_link); ?>">Return</a>
</div>

<?php
		include 'menu.php';
		include 'footer.php';
?>

</BODY></HTML>
<?php
	} // trin_validate_session ()
?>
