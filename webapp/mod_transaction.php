<?php
	/*
	 * Trinventum - modify transaction page.
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

	$display_form = FALSE;
	$error = '';
	$validation_failed_fields = array();
	$db = NULL;
	$return_link = 'transactions.php?' . TRIN_DB_TRANS_PARAM_LIST . '=1';

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	// GET = modify link, POST = modify button
	else if ((! trin_isset_get(TRIN_DB_TRANS_PARAM_ID))
		&& (! trin_isset_post(TRIN_DB_TRANS_PARAM_ID)))
	{
		header ("Location: $return_link");
	}
	else
	{
		if (strtoupper(trin_get_server('REQUEST_METHOD')) != 'POST')
		{
			trin_unset_sess(TRIN_PROD_DETAIL_PARAM);
			trin_unset_sess(TRIN_DB_PROD_INST_FIELD_ID);
			trin_unset_sess(TRIN_ALL_PROD_NAMES);
		}
		$t_id = -1;
		if (trin_isset_get(TRIN_DB_TRANS_PARAM_ID))
		{
			$t_id = trin_get_param(TRIN_DB_TRANS_PARAM_ID);
		}
		else if (trin_isset_post(TRIN_DB_TRANS_PARAM_ID))
		{
			$t_id = trin_get_post(TRIN_DB_TRANS_PARAM_ID);
		}
		$button_title = 'Next step';
		$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
			trin_get_sess(TRIN_SESS_DB_PASS),
			trin_get_sess(TRIN_SESS_DB_DBNAME),
			trin_get_sess(TRIN_SESS_DB_HOST));
		if (trin_isset_post(TRIN_PROD_DETAIL_PARAM)
			&& trin_get_post(TRIN_PROD_DETAIL_PARAM) != '-')
		{
			trin_set_sess(TRIN_PROD_DETAIL_PARAM, trin_get_post(TRIN_PROD_DETAIL_PARAM));
		}
		if (trin_isset_post(TRIN_DB_PROD_INST_FIELD_ID)
			&& trin_get_post(TRIN_DB_PROD_INST_FIELD_ID) != '-')
		{
			trin_set_sess(TRIN_DB_PROD_INST_FIELD_ID, trin_get_post(TRIN_DB_PROD_INST_FIELD_ID));
		}
		$have_prod_detail_param =
			trin_isset_sess(TRIN_PROD_DETAIL_PARAM)
			&& trin_get_sess(TRIN_PROD_DETAIL_PARAM) != '-';
		$have_prod_inst_param =
			trin_isset_sess(TRIN_DB_PROD_INST_FIELD_ID)
			&& trin_get_sess(TRIN_DB_PROD_INST_FIELD_ID) != '-';
		$have_trans_param =
			trin_isset_post(TRIN_DB_SELLER_PARAM_ID)
			&& trin_get_post(TRIN_DB_SELLER_PARAM_ID) != '-'
			&& trin_isset_post(TRIN_DB_BUYER_PARAM_ID)
			&& trin_get_post(TRIN_DB_BUYER_PARAM_ID) != '-'
			&& trin_isset_post(TRIN_DB_TRANS_PARAM_PRICE)
			&& trin_isset_post(TRIN_DB_TRANS_PARAM_PAID)
			&& trin_isset_post(TRIN_DB_TRANS_PARAM_SENT)
			&& trin_isset_post(TRIN_DB_TRANS_PARAM_SELLDATE)
			&& trin_isset_post(TRIN_DB_TRANS_PARAM_SEND_PRICE)
			&& trin_isset_post(TRIN_DB_TRANS_PARAM_SEND_COST)
			&& trin_isset_post(TRIN_DB_TRANS_PARAM_VERSION);

		if ($have_prod_detail_param)
		{
			if ($have_prod_inst_param)
			{
				if ($have_trans_param)
				{
					$form_validators = array(
						TRIN_DB_TRANS_PARAM_PRICE => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
						TRIN_DB_TRANS_PARAM_SEND_PRICE => TRIN_VALIDATION_FIELD_TYPE_NUMBER,
						TRIN_DB_TRANS_PARAM_SEND_COST => TRIN_VALIDATION_FIELD_TYPE_NUMBER
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
						// update transaction
						if (!$db)
						{
							$display_form = TRUE;
							$error = 'Cannot connect to database';
						}
						if (trin_db_update_transaction ($db,
							$t_id,
							trin_get_sess(TRIN_DB_PROD_INST_FIELD_ID),
							trin_get_post(TRIN_DB_SELLER_PARAM_ID),
							trin_get_post(TRIN_DB_BUYER_PARAM_ID),
							trin_get_post(TRIN_DB_TRANS_PARAM_PRICE),
							trin_get_post(TRIN_DB_TRANS_PARAM_PAID),
							trin_get_post(TRIN_DB_TRANS_PARAM_SENT),
							trin_get_post(TRIN_DB_TRANS_PARAM_SELLDATE),
							trin_get_post(TRIN_DB_TRANS_PARAM_SEND_PRICE),
							trin_get_post(TRIN_DB_TRANS_PARAM_SEND_COST),
							trin_get_post(TRIN_DB_TRANS_PARAM_VERSION)))
						{
							trin_unset_sess(TRIN_PROD_DETAIL_PARAM);
							trin_unset_sess(TRIN_DB_PROD_INST_FIELD_ID);
						}
						else
						{
							$display_form = TRUE;
							$error = 'Cannot update transaction in the database: '
								. trin_db_get_last_error ($db);
						}
					}
					if (! $display_form)
					{
						trin_set_success_msg('Transaction updated successfully');
						header ("Location: $return_link");
						exit;
					}
				}
				else
				{
					// seller, buyer and the other parameters not selected
					$display_form = TRUE;
					$button_title = 'Update transaction';
				}
			}
			else
			{
				// product instance not selected - display a list of product instances
				$display_form = TRUE;
			}
		}
		else
		{
			// no product selected - display the main form
			$display_form = TRUE;
		}

		$update_error = $error;
		if ($display_form)
		{
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

<TITLE> Trinventum - update a transaction </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
			include 'inc/header.php';
			include 'inc/menu.php';

			trin_display_error($error);
?>
<div class="menu">
<a href="<?php echo $return_link; ?>">Return</a>
|
<a href="<?php echo 'mod_transaction.php?' . TRIN_DB_TRANS_PARAM_ID
	. '=' . trin_html_escape($t_id); ?>">Start anew</a>
</div>

<p>Details of transaction <?php echo trin_html_escape($t_id); ?>:</p>
<?php
			$param_trans_prod = '';
			$param_trans_seller = '';
			$param_trans_buyer = '';
			$param_trans_price = '';
			$param_trans_selldate = trin_get_current_date_string ();
			$param_trans_sell_price = '';
			$param_trans_paid = 'YES';
			$param_trans_sent = 'YES';
			$param_trans_send_price = '';
			$param_trans_send_cost = '';
			$param_trans_version = 0;

			$error = '';
			$have_trans = FALSE;

			if ($db)
			{
				$trans = trin_db_get_transaction_details ($db, $t_id);
				if ($trans !== FALSE)
				{
					$param_trans_prod = $trans[TRIN_DB_PROD_INST_FIELD_ID];
					$param_trans_seller = $trans[TRIN_DB_SELLER_PARAM_ID];
					$param_trans_buyer = $trans[TRIN_DB_BUYER_PARAM_ID];
					$param_trans_sell_price = $trans[TRIN_DB_TRANS_PARAM_PRICE];
					$param_trans_paid = $trans[TRIN_DB_TRANS_PARAM_PAID];
					if ($param_trans_paid == 't')
					{
						$param_trans_paid = 'YES';
					}
					else if ($param_trans_paid == 'f')
					{
						$param_trans_paid = 'NO';
					}
					$param_trans_sent = $trans[TRIN_DB_TRANS_PARAM_SENT];
					if ($param_trans_sent == 't')
					{
						$param_trans_sent = 'YES';
					}
					else if ($param_trans_sent == 'f')
					{
						$param_trans_sent = 'NO';
					}
					$param_trans_selldate = $trans[TRIN_DB_TRANS_PARAM_SELLDATE];
					$param_trans_send_price = $trans[TRIN_DB_TRANS_PARAM_SEND_PRICE];
					$param_trans_send_cost = $trans[TRIN_DB_TRANS_PARAM_SEND_COST];
					$param_trans_version = $trans[TRIN_DB_TRANS_PARAM_VERSION];

					$yes = '<span class="ok">YES</span>';
					$no = '<span class="nok">NO</span>';
					$have_trans = TRUE;

					$paid = $yes;
					if ($trans[TRIN_DB_TRANS_PARAM_PAID] === 'f')
					{
						$paid = $no;
					}

					$sent = $yes;
					if ($trans[TRIN_DB_TRANS_PARAM_SENT] === 'f')
					{
						$sent = $no;
					}

					$product_def_link = 'details.php?' .
						TRIN_PROD_DETAIL_PARAM . '=' .
						$trans[TRIN_DB_PROD_DEF_FIELD_ID];
					$product_link = 'ppdetails.php?' .
						TRIN_DB_PROD_INST_FIELD_ID . '=' .
						$trans[TRIN_DB_PROD_INST_FIELD_ID];
					echo "<ul>\n" .
						' <li><p>Product: <a href="' . $product_def_link . '">'
							. $trans[TRIN_DB_PROD_DEF_FIELD_ID] . ' - '
							. trin_html_escape ($trans[TRIN_DB_PROD_DEF_FIELD_NAME]) . "</a></p></li>\n" .
						' <li><p>Product piece: <a href="' . $product_link . '">'
							. $trans[TRIN_DB_PROD_INST_FIELD_ID] . "</a></p></li>\n" .
						' <li><p>Seller: <a href="sellers.php">'
							. $trans[TRIN_DB_SELLER_PARAM_ID] . ' - '
							. trin_html_escape ($trans[TRIN_DB_SELLER_PARAM_NAME]) . "</a></p></li>\n" .
						' <li><p>Buyer: <a href="buyers.php">'
							. $trans[TRIN_DB_BUYER_PARAM_ID] . ' - '
							. trin_html_escape ($trans[TRIN_DB_BUYER_PARAM_NAME]) . "</a></p></li>\n" .
						' <li><p>Price: ' . $trans[TRIN_DB_TRANS_PARAM_PRICE] . "</p></li>\n" .
						' <li><p>Paid: ' . $paid . "</p></li>\n" .
						' <li><p>Sent: ' . $sent . "</p></li>\n" .
						' <li><p>Sell date: ' . $trans[TRIN_DB_TRANS_PARAM_SELLDATE] . "</p></li>\n" .
						' <li><p>Send price: ' . $trans[TRIN_DB_TRANS_PARAM_SEND_PRICE] . "</p></li>\n" .
						' <li><p>Send cost: ' . $trans[TRIN_DB_TRANS_PARAM_SEND_COST] . "</p></li>\n"
						. "</ul>\n";
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

			if ((! $have_trans) && (! $error))
			{
				$error = 'Transaction not found';
			}

			trin_display_error ($error);
?>

<p class="c">
Update details (warning - this updates ALL the given details):
</p>
<div class="login_box c">
<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="POST">
<?php
			$error = '';

			$display_trans_params = FALSE;

			if (! $have_prod_detail_param)
			{
				// display a list of product categories
				if ($db)
				{
					$products = trin_db_get_product_defs ($db);
					if ($products !== FALSE)
					{
						echo "<p>Product type:\n";

						$product_names = array();
						$product_values = array();
						trin_set_sess(TRIN_ALL_PROD_NAMES, array());
						while (TRUE)
						{
							$next_prod = trin_db_get_next_product ($db,
								$products);
							if ($next_prod === FALSE)
							{
								break;
							}
							$product_names[] =
								$next_prod[TRIN_DB_PROD_DEF_FIELD_ID]
								. ' - '
								. $next_prod[TRIN_DB_PROD_DEF_FIELD_NAME];
							$product_values[] =
								$next_prod[TRIN_DB_PROD_DEF_FIELD_ID];

							$sess_prod_names = trin_get_sess(TRIN_ALL_PROD_NAMES);
							$sess_prod_names[$next_prod[TRIN_DB_PROD_DEF_FIELD_ID]]
								= $next_prod[TRIN_DB_PROD_DEF_FIELD_NAME];
							trin_set_sess(TRIN_ALL_PROD_NAMES, $sess_prod_names);
						}
						trin_create_select(TRIN_PROD_DETAIL_PARAM,
							'',
							$product_names,
							$product_values,
							$validation_failed_fields);
					}
					else
					{
						$error = 'Cannot read product database: '
							. trin_db_get_last_error ($db);
					}
				}
				else
				{
					$error = 'Cannot connect to database to get products';
				}
			}
			else
			{
				echo 'Product type: ' . trin_html_escape(trin_get_sess(TRIN_PROD_DETAIL_PARAM))
					. ' - ';
				if (trin_isset_sess(TRIN_ALL_PROD_NAMES))
				{
					echo trin_get_sess(TRIN_ALL_PROD_NAMES)[trin_get_sess(TRIN_PROD_DETAIL_PARAM)];
				}
				else
				{
					echo trin_html_escape ($trans[TRIN_DB_PROD_DEF_FIELD_NAME]);
				}
				echo "<br>\n";
				if (! $have_prod_inst_param)
				{
					// display a list of instances marked for selling of
					// the given product category
					if ($db && trin_isset_sess(TRIN_PROD_DETAIL_PARAM))
					{
						// get only products marked for selling
						$products = trin_db_get_product_instances_with_status ($db,
							trin_get_sess(TRIN_PROD_DETAIL_PARAM),
							TRIN_PROD_STATUS_SALE_IN_PROGRESS);
						if ($products !== FALSE)
						{
							echo "<p>Product piece:\n";

							$product_options = array();
							while (TRUE)
							{
								$next_prod = trin_db_get_next_product_instance
									($db, $products);
								if ($next_prod === FALSE)
								{
									break;
								}
								$product_options[] =
									$next_prod[TRIN_DB_PROD_INST_FIELD_ID];
							}
							trin_create_select(
								TRIN_DB_PROD_INST_FIELD_ID,
								$param_trans_prod,
								$product_options,
								$product_options,
								$validation_failed_fields);
						}
						else
						{
							$error = 'Cannot read product instance database: '
								. trin_db_get_last_error ($db);
						}
					}
					else if (!$db)
					{
						$error = 'Cannot connect to database to get product instances';
					}
				}
				else
				{
					$display_trans_params = TRUE;
				}
			}

			if ($display_trans_params)
			{
				echo 'Product piece: ' . trin_html_escape(trin_get_sess(TRIN_DB_PROD_INST_FIELD_ID)) . "<br>\n";
				// display a list of sellers & buyers
				// and the remaining fields for a transaction
				if ($db)
				{
					$buyers = trin_db_get_buyers ($db);
					if ($buyers !== FALSE)
					{
						echo "<p>Buyer:\n";

						$buyer_names = array();
						$buyer_values = array();
						while (TRUE)
						{
							$next_buyer = trin_db_get_next_buyer ($db, $buyers);
							if ($next_buyer === FALSE)
							{
								break;
							}
							$buyer_names[] =
								$next_buyer[TRIN_DB_BUYER_PARAM_NAME];
							$buyer_values[] =
								$next_buyer[TRIN_DB_BUYER_PARAM_ID];
						}
						trin_create_select(TRIN_DB_BUYER_PARAM_ID,
							$param_trans_buyer,
							$buyer_names,
							$buyer_values,
							$validation_failed_fields);
					}
					else
					{
						$error = 'Cannot read buyer database: '
							. trin_db_get_last_error ($db);
					}
				}
				else
				{
					$error = 'Cannot connect to database to get buyers';
				}

				if ($db)
				{
					$sellers = trin_db_get_sellers ($db);
					if ($sellers !== FALSE)
					{
						echo "<p>Seller:\n";

						$seller_names = array();
						$seller_values = array();
						while (TRUE)
						{
							$next_seller = trin_db_get_next_seller ($db, $sellers);
							if ($next_seller === FALSE)
							{
								break;
							}
							$seller_names[] =
								$next_seller[TRIN_DB_SELLER_PARAM_NAME];
							$seller_values[] =
								$next_seller[TRIN_DB_SELLER_PARAM_ID];
						}
						trin_create_select(TRIN_DB_SELLER_PARAM_ID,
							$param_trans_seller,
							$seller_names,
							$seller_values,
							$validation_failed_fields);
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

				// if the transaction failed to be updated,
				// refresh it from the DB and make the user
				// re-enter the data, else display what the use entered
				if (! $update_error)
				{
					if (trin_isset_post(TRIN_DB_TRANS_PARAM_PRICE))
					{
						$param_trans_sell_price = trin_get_post(TRIN_DB_TRANS_PARAM_PRICE);
					}

					if (trin_isset_post(TRIN_DB_TRANS_PARAM_PAID))
					{
						$param_trans_paid = trin_get_post(TRIN_DB_TRANS_PARAM_PAID);
					}

					if (trin_isset_post(TRIN_DB_TRANS_PARAM_SENT))
					{
						$param_trans_sent = trin_get_post(TRIN_DB_TRANS_PARAM_SENT);
					}

					if (trin_isset_post(TRIN_DB_TRANS_PARAM_SELLDATE))
					{
						$param_trans_selldate = trin_get_post(TRIN_DB_TRANS_PARAM_SELLDATE);
					}
					else if ($param_trans_selldate == '')
					{
						$param_trans_selldate = trin_get_current_date_string ();
					}

					if (trin_isset_post(TRIN_DB_TRANS_PARAM_SEND_PRICE))
					{
						$param_trans_send_price = trin_get_post(TRIN_DB_TRANS_PARAM_SEND_PRICE);
					}

					if (trin_isset_post(TRIN_DB_TRANS_PARAM_SEND_COST))
					{
						$param_trans_send_cost = trin_get_post(TRIN_DB_TRANS_PARAM_SEND_COST);
					}
					/*
					always take the current version value
					if (trin_isset_post(TRIN_DB_TRANS_PARAM_VERSION))
					{
						$param_trans_version = trin_get_post(TRIN_DB_TRANS_PARAM_VERSION);
					}
					*/
				}

				if ($param_trans_paid == 't')
				{
					$param_trans_paid = 'YES';
				}
				else if ($param_trans_paid == 'f')
				{
					$param_trans_paid = 'NO';
				}

				if ($param_trans_sent == 't')
				{
					$param_trans_sent = 'YES';
				}
				else if ($param_trans_sent == 'f')
				{
					$param_trans_sent = 'NO';
				}

				trin_display_error($error);
?>
<p>
Sell price:
<?php
				trin_create_text_input('text', '20',
					TRIN_DB_TRANS_PARAM_PRICE,
					$param_trans_sell_price,
					$validation_failed_fields,
					'Only decimal values allowed, no currency names');
?>
</p>

<p>
Was the product paid for:
<?php
				trin_create_select(TRIN_DB_TRANS_PARAM_PAID,
					$param_trans_paid,
					array('YES', 'NO'),
					array('YES', 'NO'),
					$validation_failed_fields)
?>
</p>

<p>
Was the product sent:
<?php
				trin_create_select(TRIN_DB_TRANS_PARAM_SENT,
					$param_trans_sent,
					array('YES', 'NO'),
					array('YES', 'NO'),
					$validation_failed_fields)
?>
</p>

<p>
Sell date:
<?php
				trin_create_text_input('text', '20',
					TRIN_DB_TRANS_PARAM_SELLDATE,
					$param_trans_selldate,
					$validation_failed_fields);
?>
</p>

<p>
Send price:
<?php
				trin_create_text_input('text', '20',
					TRIN_DB_TRANS_PARAM_SEND_PRICE,
					$param_trans_send_price,
					$validation_failed_fields,
					'Only decimal values allowed, no currency names');
?>
</p>

<p>
Send cost:
<?php
				trin_create_text_input('text', '20',
					TRIN_DB_TRANS_PARAM_SEND_COST,
					$param_trans_send_cost,
					$validation_failed_fields,
					'Only decimal values allowed, no currency names');
?>
</p>

<?php
			} // $display_trans_params
?>

<p>
<?php
		trin_create_text_input('hidden', '', TRIN_DB_TRANS_PARAM_VERSION,
			$param_trans_version, $validation_failed_fields);
?>
<input type="submit" value="<?php echo $button_title; ?>">
<?php
	trin_create_reset ("mod_transaction");
?>
</p>

</form>
</div>

<table>
<caption>Transaction's history of changes</caption>
<thead><tr>
 <th>Product type was</th>
 <th>Product piece was</th>
 <th>Seller was</th>
 <th>Buyer was</th>
 <th>Price was</th>
 <th>Paid was</th>
 <th>Sent was</th>
 <th>Sell date was</th>
 <th>Send price was</th>
 <th>Send cost was</th>
 <th>Change user</th>
 <th>Change time</th>
</tr></thead>
<tbody>
<?php
			$error = '';
			$have_trans = FALSE;
			if ($db)
			{
				$trans = trin_db_get_transaction_history ($db, $t_id);
				if ($trans !== FALSE)
				{
					$yes = '<span class="ok">YES</span>';
					$no = '<span class="nok">NO</span>';
					while (TRUE)
					{
						$next_tran = trin_db_get_next_transaction_hist_entry ($db, $trans);
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
						echo '<tr class="c">' .
							'<td><a href="' . $product_def_link . '">' . trin_html_escape
								($next_tran[TRIN_DB_PROD_DEF_FIELD_NAME])
								. '</a></td>' .
							'<td><a href="' . $product_link . '">' . 
							$next_tran[TRIN_DB_PROD_INST_FIELD_ID] . '</a></td>' .
							'<td><a href="sellers.php">' . trin_html_escape
								($next_tran[TRIN_DB_SELLER_PARAM_NAME]) . '</a></td>' .
							'<td><a href="buyers.php">' . trin_html_escape
								($next_tran[TRIN_DB_BUYER_PARAM_NAME]) . '</a></td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_PRICE] . '</td>' .
							'<td>' . $paid . '</td>' .
							'<td>' . $sent . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SELLDATE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_PRICE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_COST] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_USER] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_TIMESTAMP] . '<hr></td></tr>'
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
<tr><td colspan="13" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
			} // $error
			if ((! $have_trans) && (! $error))
			{
?>
<tr><td colspan="13" class="c">No history for this transaction found</td></tr>
<?php
			} // ! $have_trans
?>
</tbody>
</table>

<div class="menu">
<a href="<?php echo $return_link; ?>">Return</a>
|
<a href="<?php echo 'mod_transaction.php?' . TRIN_DB_TRANS_PARAM_ID
	. '=' . trin_html_escape($t_id); ?>">Start anew</a>
</div>

<?php
			include 'inc/menu.php';
			include 'inc/footer.php';
?>

</BODY></HTML>
<?php
		} //$display_form
	} // trin_validate_session()
?>
