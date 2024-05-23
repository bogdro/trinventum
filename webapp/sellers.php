<?php
	/*
	 * Trinventum - sellers' page.
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

	trin_error_reporting();

	include_once 'inc/db_functions.php';

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
		if (trin_isset_post(TRIN_DB_SELLER_PARAM_NAME))
		{
			$form_validators = array(
				TRIN_DB_SELLER_PARAM_NAME => TRIN_VALIDATION_FIELD_TYPE_REQUIRED
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
				if (! trin_db_add_seller ($db,
					trin_get_post(TRIN_DB_SELLER_PARAM_NAME)))
				{
					$error = 'Cannot add seller to the database: '
						. trin_db_get_last_error ($db);
				}
				else
				{
					trin_set_success_msg('Seller added successfully');
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

<TITLE> Trinventum - manage sellers </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include 'inc/header.php';
		include 'menu.php';

		trin_display_error($error);
		trin_display_success();
?>
<div class="login_box">
<?php

		$param_seller_name = '';
		$param_seller_version = 0;

		// reset the form on success, leave the values on error
		if ($error && trin_isset_post(TRIN_DB_SELLER_PARAM_NAME))
		{
			$param_seller_name = trin_get_post(TRIN_DB_SELLER_PARAM_NAME);
		}

		trin_create_seller_form (
			trin_get_self_action (), 'Add seller',
			TRIN_DB_SELLER_PARAM_NAME, $param_seller_name,
			TRIN_DB_SELLER_PARAM_VERSION, $param_seller_version,
			$validation_failed_fields
		);
?>
</div>

<table>
<caption>Registered sellers</caption>
<thead><tr>
 <th>ID</th>
 <th>Name</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_seller = FALSE;
		if ($db)
		{
			$sellers = trin_db_get_sellers ($db);
			if ($sellers !== FALSE)
			{
				while (TRUE)
				{
					$next_seller = trin_db_get_next_seller ($db, $sellers);
					if ($next_seller === FALSE)
					{
						break;
					}
					$have_seller = TRUE;
					$seller_det_link = 'mod_seller.php?' . TRIN_DB_SELLER_PARAM_ID
						. '=' . $next_seller[TRIN_DB_SELLER_PARAM_ID];
					echo '<tr class="c">' .
						"<td><a href=\"$seller_det_link\">" .
							$next_seller[TRIN_DB_SELLER_PARAM_ID] . '</a></td>' .
						"<td><a href=\"$seller_det_link\">"
							. trin_html_escape ($next_seller[TRIN_DB_SELLER_PARAM_NAME])
							. '</a></td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read seller database: '
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
		if ((! $have_seller) && (! $error))
		{
?>
<tr><td colspan="2" class="c">No sellers found</td></tr>
<?php
		} // ! $have_seller
?>
</tbody>
</table>

<table>
<caption>Products sold</caption>
<thead><tr>
 <th>Seller name</th>
 <th>Product name</th>
 <th>Quantity</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_sale = FALSE;
		if ($db)
		{
			$sales = trin_db_get_seller_transactions ($db);
			if ($sales !== FALSE)
			{
				while (TRUE)
				{
					$next_sale = trin_db_get_next_seller_transaction ($db, $sales);
					if ($next_sale === FALSE)
					{
						break;
					}
					$product_link = 'details.php?' . TRIN_PROD_DETAIL_PARAM
						. '=' . $next_sale[TRIN_DB_PROD_DEF_FIELD_ID];
					$have_sale = TRUE;
					echo '<tr class="c">' .
						'<td>' . $next_sale[TRIN_DB_SELLER_PARAM_NAME] . '</td>' .
						"<td><a href=\"$product_link\">"
							. trin_html_escape ($next_sale[TRIN_DB_PROD_DEF_FIELD_NAME]) . '</a></td>' .
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
<tr><td colspan="3" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_sale) && (! $error))
		{
?>
<tr><td colspan="3" class="c">No products sold</td></tr>
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
		include 'inc/footer.php';
?>

</BODY></HTML>
<?php
	} // trin_validate_session()
?>
