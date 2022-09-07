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
	else if (! isset ($_GET[TRIN_DB_PROD_INST_FIELD_ID]))
	{
		header ('Location: main.php');
	}
	else
	{
		$error = '';
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
			&& isset ($_POST[TRIN_DB_PROD_INST_FIELD_COST]))
		{
			if (!$db)
			{
				$error = 'Cannot connect to database';
			}
			if (! trin_db_update_product_instance ($db,
				$product_inst_id,
				$_POST[TRIN_DB_PROD_INST_FIELD_STATUS],
				$_POST[TRIN_DB_PROD_INST_FIELD_COST]))
			{
				$error = 'Cannot update product instance in the database: '
					. pg_last_error ();
			}
			else
			{
				header ("Location: $return_link");
			}
		}
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

<TITLE> Trinventum - product piece details </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include ('header.php');
?>
<?php
		if ($error)
		{
?>
<p class="c">Error: <?php echo $error; ?></p>
<?php
		} // $error
?>
<div class="menu">
<a href="<?php echo $return_link; ?>">Return</a>
</div>

<p>Details of product piece <?php echo $product_inst_id; ?>:</p>
<?php
		$param_pp_status = '';
		$param_pp_cost = '';

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

				echo 	'<p>Status: ' . $param_pp_status . '</p>' .
					'<p>Cost: ' . $param_pp_cost . '</p>'
					;
			}
			else
			{
				$error = 'Cannot read product instance details from the database: '
					. pg_last_error ();
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		if ($error)
		{
?>
<p class="c">Error: <?php echo $error; ?></p>
<?php
		} // $error
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

<div class="login_box">
<?php
		if (isset ($_POST[TRIN_DB_PROD_INST_FIELD_STATUS]))
		{
			$param_pp_status = $_POST[TRIN_DB_PROD_INST_FIELD_STATUS];
		}
		if (isset ($_POST[TRIN_DB_PROD_INST_FIELD_COST]))
		{
			$param_pp_cost = $_POST[TRIN_DB_PROD_INST_FIELD_COST];
		}
?>
<form action="<?php echo trin_get_self_action (); ?>" method="POST">

<?php
		if ($param_pp_status == TRIN_PROD_STATUS_SOLD)
		{
?>
<input type="hidden" name="<?php echo TRIN_DB_PROD_INST_FIELD_STATUS; ?>"
	value="<?php echo $param_pp_status; ?>">
<?php
		}
		else // ! SOLD
		{
?>
<p>
Status:
<select name="<?php echo TRIN_DB_PROD_INST_FIELD_STATUS; ?>">

<option value="<?php echo TRIN_PROD_STATUS_READY; ?>"
<?php
			if ($param_pp_status == TRIN_PROD_STATUS_READY)
			{
?>
	selected="selected"
<?php
			}
?>
><?php echo TRIN_PROD_STATUS_READY; ?></option>

<option value="<?php echo TRIN_PROD_STATUS_SALE_IN_PROGRESS; ?>"
<?php
			if ($param_pp_status == TRIN_PROD_STATUS_SALE_IN_PROGRESS)
			{
?>
	selected="selected"
<?php
			}
?>
><?php echo TRIN_PROD_STATUS_SALE_IN_PROGRESS; ?></option>
</select>
</p>
<?php
		} // SOLD
?>

<p>
Cost:
<input type="text" size="20"
	value="<?php echo $param_pp_cost; ?>" name="<?php echo TRIN_DB_PROD_INST_FIELD_COST; ?>">
</p>

<p>
<input type="submit" value="Update product piece"> <input type="reset" value="Reset">
</p>

</form>
</div>

<div class="menu">
<a href="<?php echo $return_link; ?>">Return</a>
</div>

<?php
		include ('footer.php');
?>

</BODY></HTML>
<?php
	} // trin_validate_session ()
?>
