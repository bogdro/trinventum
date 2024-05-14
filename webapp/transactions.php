<?php
	/*
	 * Trinventum - transactions' page.
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
	$db = NULL;
	$validation_failed_fields = array();
	$use_mod_button = TRUE;

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else
	{
		$form_validators = array(
			TRIN_DB_TRANS_LIST_PARAM_START => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
			TRIN_DB_TRANS_LIST_PARAM_COUNT => TRIN_VALIDATION_FIELD_TYPE_NUMBER
			);
		$validation_failed_fields = trin_validate_form($_GET, $form_validators);
		if (count($validation_failed_fields) != 0)
		{
			$display_form = TRUE;
			$error = 'Form validation failed - check field values: '
				. implode(', ', $validation_failed_fields);
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

<TITLE> Trinventum - manage transactions </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include 'header.php';
		include 'menu.php';

		trin_display_success();

		$offset = 0;
		$limit = 1000000000;

		if (trin_isset_get(TRIN_DB_TRANS_LIST_PARAM_START))
		{
			$offset = trin_get_param(TRIN_DB_TRANS_LIST_PARAM_START);
		}

		if (trin_isset_get(TRIN_DB_TRANS_LIST_PARAM_COUNT))
		{
			$limit = trin_get_param(TRIN_DB_TRANS_LIST_PARAM_COUNT);
		}

		if ($use_mod_button)
		{
			$ncols = 13;
		}
		else
		{
			$ncols = 12;
		}
?>

<div class="menu">
<a href="main.php">Return</a>
|
<a href="add_transaction.php">Register a new transaction</a>
</div>

<div class="menu">
<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="GET">
List
<?php
		trin_create_text_input('text', '11', TRIN_DB_TRANS_LIST_PARAM_COUNT,
			$limit, $validation_failed_fields);
?>
<label for="<?php echo TRIN_DB_TRANS_LIST_PARAM_COUNT ?>">newest transactions</label>,
<label for="<?php echo TRIN_DB_TRANS_LIST_PARAM_START ?>">skipping</label>
<?php
		trin_create_text_input('text', '5', TRIN_DB_TRANS_LIST_PARAM_START,
			$offset, $validation_failed_fields);
?>
first ones - <input type="submit" value="Go!">
</form>
OR
<a href="<?php echo trin_html_escape(trin_get_server('PHP_SELF')) . '?' . TRIN_DB_TRANS_PARAM_LIST . '=1'; ?>"
>List all transactions</a>
</div>

<?php
		if (trin_isset_get(TRIN_DB_TRANS_PARAM_LIST)
			|| (trin_isset_get(TRIN_DB_TRANS_LIST_PARAM_START)
				&& trin_isset_get(TRIN_DB_TRANS_LIST_PARAM_COUNT)))
		{
?>

<table>
<caption>Registered transactions</caption>
<thead><tr>
 <th>ID</th>
<?php
	if ($use_mod_button)
	{
?>
 <th>Modify</th>
<?php
	} // $use_mod_button
?>
 <th>Product type</th>
 <th>Product piece</th>
 <th>Seller</th>
 <th>Buyer</th>
 <th>Price</th>
 <th>Paid</th>
 <th>Sent</th>
 <th>Sell date</th>
 <th>Send price</th>
 <th>Send cost</th>
 <th>Delete</th>
</tr></thead>
<tbody>
<?php
			$error = '';
			$have_trans = FALSE;
			$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
				trin_get_sess(TRIN_SESS_DB_PASS),
				trin_get_sess(TRIN_SESS_DB_DBNAME),
				trin_get_sess(TRIN_SESS_DB_HOST));
			if ($db)
			{
				$trans = trin_db_get_transactions ($db, $offset, $limit);
				if ($trans !== FALSE)
				{
					$yes = '<span class="ok">YES</span>';
					$no = '<span class="nok">NO</span>';
					while (TRUE)
					{
						$next_tran = trin_db_get_next_transaction ($db, $trans);
						if ($next_tran === FALSE)
						{
							break;
						}
						$have_trans = TRUE;

						$paid = $yes;
						if ($next_tran[TRIN_DB_TRANS_PARAM_PAID] === 'f')
						{
							$paid = $no;
						}

						$sent = $yes;
						if ($next_tran[TRIN_DB_TRANS_PARAM_SENT] === 'f')
						{
							$sent = $no;
						}

						$product_def_link = 'details.php?' .
							TRIN_PROD_DETAIL_PARAM . '=' .
							$next_tran[TRIN_DB_PROD_DEF_FIELD_ID];
						$product_link = 'ppdetails.php?' .
							TRIN_DB_PROD_INST_FIELD_ID . '=' .
							$next_tran[TRIN_DB_PROD_INST_FIELD_ID];
						$tran_link = 'mod_transaction.php?' .
							TRIN_DB_TRANS_PARAM_ID . '=' .
							$next_tran[TRIN_DB_TRANS_PARAM_ID];
						echo '<tr class="c">';
						if ($use_mod_button)
						{
							echo '<td>' . $next_tran[TRIN_DB_TRANS_PARAM_ID] . '</td>';
							echo "<td>\n" .
							"<form action=\"$tran_link\" method=\"POST\">\n"
							. ' <input type="hidden" name="' . TRIN_DB_TRANS_PARAM_ID
							. '" value="'
							. $next_tran[TRIN_DB_TRANS_PARAM_ID] . "\">\n"
							. ' <input type="hidden" name="' . TRIN_PROD_DETAIL_PARAM
							. '" value="'
							. $next_tran[TRIN_DB_PROD_DEF_FIELD_ID] . "\">\n"
							. ' <input type="hidden" name="' . TRIN_DB_PROD_INST_FIELD_ID
							. '" value="'
							. $next_tran[TRIN_DB_PROD_INST_FIELD_ID] . "\">\n"
							. ' <input type="submit" value="Modify"></form></td>';
						}
						else
						{
							echo '<td><a href="' . $tran_link . '">'
								. $next_tran[TRIN_DB_TRANS_PARAM_ID] . '</a></td>';
						}
						echo '<td><a href="' . $product_def_link . '">'
							. trin_html_escape ($next_tran[TRIN_DB_PROD_DEF_FIELD_NAME])
							. '</a></td>' .
							'<td><a href="' . $product_link . '">'
								. $next_tran[TRIN_DB_PROD_INST_FIELD_ID]
								. '</a></td>' .
							'<td><a href="mod_seller.php?' . TRIN_DB_SELLER_PARAM_ID
								. '=' . $next_tran[TRIN_DB_SELLER_PARAM_ID] . '">'
								. trin_html_escape
									($next_tran[TRIN_DB_SELLER_PARAM_NAME])
								. '</a></td>' .
							'<td><a href="mod_buyer.php?' . TRIN_DB_BUYER_PARAM_ID
								. '=' . $next_tran[TRIN_DB_BUYER_PARAM_ID] . '">'
								. trin_html_escape
									($next_tran[TRIN_DB_BUYER_PARAM_NAME])
								. '</a></td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_PRICE] . '</td>' .
							'<td>' . $paid . '</td>' .
							'<td>' . $sent . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SELLDATE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_PRICE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_COST] . '</td>' .
							"<td>\n" .
							"<form action=\"del_transaction.php\" method=\"POST\">\n" .
							' <input type="hidden" name="' . TRIN_DB_TRANS_PARAM_ID
							. '" value="' .
							$next_tran[TRIN_DB_TRANS_PARAM_ID] . "\">\n" .
							' <input type="submit" value="Delete"></form><hr></td></tr>'
							. "\n";
					}
				}
				else
				{
					$error = 'Cannot read transaction database: '
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
<tr><td colspan="<?php echo $ncols; ?>" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
			} // $error
			if ((! $have_trans) && (! $error))
			{
?>
<tr><td colspan="<?php echo $ncols; ?>" class="c">No transactions found</td></tr>
<?php
			} // ! $have_trans
?>
</tbody>
</table>

<table>
<caption>Deleted transactions with history</caption>
<thead><tr>
 <th>ID</th>
 <th>Product type</th>
 <th>Product piece</th>
 <th>Seller</th>
 <th>Buyer</th>
 <th>Price</th>
 <th>Paid</th>
 <th>Sent</th>
 <th>Sell date</th>
 <th>Send price</th>
 <th>Send cost</th>
</tr></thead>
<tbody>
<?php
			$error = '';
			$have_trans = FALSE;
			$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
				trin_get_sess(TRIN_SESS_DB_PASS),
				trin_get_sess(TRIN_SESS_DB_DBNAME),
				trin_get_sess(TRIN_SESS_DB_HOST));
			if ($db)
			{
				$trans = trin_db_get_deleted_transactions ($db, $offset, $limit);
				if ($trans !== FALSE)
				{
					$yes = '<span class="ok">YES</span>';
					$no = '<span class="nok">NO</span>';
					while (TRUE)
					{
						$next_tran = trin_db_get_next_transaction ($db, $trans);
						if ($next_tran === FALSE)
						{
							break;
						}
						$have_trans = TRUE;

						$paid = $yes;
						if ($next_tran[TRIN_DB_TRANS_PARAM_PAID] === 'f')
						{
							$paid = $no;
						}

						$sent = $yes;
						if ($next_tran[TRIN_DB_TRANS_PARAM_SENT] === 'f')
						{
							$sent = $no;
						}

						$product_def_link = 'details.php?' .
							TRIN_PROD_DETAIL_PARAM . '=' .
							$next_tran[TRIN_DB_PROD_DEF_FIELD_ID];
						$product_link = 'ppdetails.php?' .
							TRIN_DB_PROD_INST_FIELD_ID . '=' .
							$next_tran[TRIN_DB_PROD_INST_FIELD_ID];
						echo '<tr class="c">';
						echo '<td>' . $next_tran[TRIN_DB_TRANS_PARAM_ID] . '</td>';
						echo '<td><a href="' . $product_def_link . '">'
								. trin_html_escape
									($next_tran[TRIN_DB_PROD_DEF_FIELD_NAME])
								. '</a></td>' .
							'<td><a href="' . $product_link . '">'
							. $next_tran[TRIN_DB_PROD_INST_FIELD_ID] . '</a></td>' .
							'<td><a href="mod_seller.php?' . TRIN_DB_SELLER_PARAM_ID
								. '=' . $next_tran[TRIN_DB_SELLER_PARAM_ID] . '">'
								. trin_html_escape
									($next_tran[TRIN_DB_SELLER_PARAM_NAME])
								. '</a></td>' .
							'<td><a href="mod_buyer.php?' . TRIN_DB_BUYER_PARAM_ID
								. '=' . $next_tran[TRIN_DB_BUYER_PARAM_ID] . '">'
								. trin_html_escape
									($next_tran[TRIN_DB_BUYER_PARAM_NAME])
								. '</a></td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_PRICE] . '</td>' .
							'<td>' . $paid . '</td>' .
							'<td>' . $sent . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SELLDATE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_PRICE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_COST] . '</td>' .
							"</tr>\n";
					}
				}
				else
				{
					$error = 'Cannot read transaction history database: '
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
<tr><td colspan="11" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
			} // $error
			if ((! $have_trans) && (! $error))
			{
?>
<tr><td colspan="11" class="c">No deleted transactions found</td></tr>
<?php
			} // ! $have_trans
?>
</tbody>
</table>
<?php
		} // parameters set
?>

<div class="menu">
<a href="main.php">Return</a>
|
<a href="add_transaction.php">Register a new transaction</a>
</div>

<?php
		include 'menu.php';
		include 'footer.php';
?>

</BODY></HTML>
<?php
	} // trin_validate_session()
?>
