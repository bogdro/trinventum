<?php
	/*
	 * Trinventum - transaction adding page.
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
		$button_title = 'Next step';
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		$have_prod_detail_param =
			isset ($_POST[TRIN_PROD_DETAIL_PARAM])
			&& $_POST[TRIN_PROD_DETAIL_PARAM] != '-';
		$have_prod_inst_param =
			isset ($_POST[TRIN_DB_PROD_INST_FIELD_ID])
			&& $_POST[TRIN_DB_PROD_INST_FIELD_ID] != '-';
		if ($have_prod_detail_param)
		{
			if ($have_prod_inst_param)
			{
				if (isset ($_POST[TRIN_DB_SELLER_PARAM_ID])
					&& isset ($_POST[TRIN_DB_BUYER_PARAM_ID])
					&& isset ($_POST[TRIN_DB_TRANS_PARAM_PRICE])
					&& isset ($_POST[TRIN_DB_TRANS_PARAM_PAID])
					&& isset ($_POST[TRIN_DB_TRANS_PARAM_SENT])
					&& isset ($_POST[TRIN_DB_TRANS_PARAM_SELLDATE])
					&& isset ($_POST[TRIN_DB_TRANS_PARAM_SEND_PRICE])
					&& isset ($_POST[TRIN_DB_TRANS_PARAM_SEND_COST])
					)
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
						// register transaction
						if (!$db)
						{
							$display_form = TRUE;
							$error = 'Cannot connect to database';
						}
						if (! trin_db_add_transaction ($db,
							$_POST[TRIN_DB_PROD_INST_FIELD_ID],
							$_POST[TRIN_DB_SELLER_PARAM_ID],
							$_POST[TRIN_DB_BUYER_PARAM_ID],
							$_POST[TRIN_DB_TRANS_PARAM_PRICE],
							$_POST[TRIN_DB_TRANS_PARAM_PAID],
							$_POST[TRIN_DB_TRANS_PARAM_SENT],
							$_POST[TRIN_DB_TRANS_PARAM_SELLDATE],
							$_POST[TRIN_DB_TRANS_PARAM_SEND_PRICE],
							$_POST[TRIN_DB_TRANS_PARAM_SEND_COST]))
						{
							$display_form = TRUE;
							$error = 'Cannot add transaction to the database: '
								. trin_db_get_last_error ($db);
						}
					}
					if (! $display_form)
					{
						trin_set_success_msg('Transaction added successfully');
						header ('Location: transactions.php');
						exit;
					}
				}
				else
				{
					// seller, buyer and the other parameters not selected
					$display_form = TRUE;
					$button_title = 'Add transaction';
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

<TITLE> Trinventum - register a transaction </TITLE>
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
						echo '<p><label for="' . TRIN_PROD_DETAIL_PARAM . '">Product type:</label>' . "\n";

						$product_names = array();
						$product_values = array();
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
				echo 'Product type: ' . trin_html_escape ($_POST[TRIN_PROD_DETAIL_PARAM]) . "<br>\n";
				trin_create_text_input('hidden',
					'',
					TRIN_PROD_DETAIL_PARAM,
					$_POST[TRIN_PROD_DETAIL_PARAM],
					$validation_failed_fields);
				if (! $have_prod_inst_param)
				{
					// display a list of instances marked for selling of
					// the given product category
					if ($db && isset ($_POST[TRIN_PROD_DETAIL_PARAM]))
					{
						// get only products marked for selling
						$products = trin_db_get_product_instances_with_status ($db,
							$_POST[TRIN_PROD_DETAIL_PARAM],
							TRIN_PROD_STATUS_SALE_IN_PROGRESS);
						if ($products !== FALSE)
						{
							echo '<p><label for="' . TRIN_DB_PROD_INST_FIELD_ID . '">Product piece:</label>' . "\n";

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
							trin_create_select(TRIN_DB_PROD_INST_FIELD_ID,
								'',
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
					trin_create_text_input('hidden',
						'',
						TRIN_DB_PROD_INST_FIELD_ID,
						$_POST[TRIN_DB_PROD_INST_FIELD_ID],
						$validation_failed_fields);
					$display_trans_params = TRUE;
				}
			}

			if ($display_trans_params)
			{
				echo 'Product piece: ' . trin_html_escape($_POST[TRIN_DB_PROD_INST_FIELD_ID]) . "<br>\n";
				// display a list of sellers & buyers
				// and the remaining fields for a transaction
				if ($db)
				{
					$buyers = trin_db_get_buyers ($db);
					if ($buyers !== FALSE)
					{
						echo '<p><label for="' . TRIN_DB_BUYER_PARAM_ID . '">Buyer:</label>' . "\n";

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
							'',
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
						echo '<p><label for="' . TRIN_DB_SELLER_PARAM_ID . '">Seller:</label>' . "\n";

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
							'',
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

				$param_trans_sell_price = '';
				$param_trans_paid = '';
				$param_trans_sent = 'YES';
				$param_trans_send_price = '';
				$param_trans_send_cost = '';

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_PRICE]))
				{
					$param_trans_sell_price = $_POST[TRIN_DB_TRANS_PARAM_PRICE];
				}

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_PAID]))
				{
					$param_trans_paid = $_POST[TRIN_DB_TRANS_PARAM_PAID];
				}

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_SENT]))
				{
					$param_trans_sent = $_POST[TRIN_DB_TRANS_PARAM_SENT];
				}

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_SELLDATE]))
				{
					$param_trans_selldate = $_POST[TRIN_DB_TRANS_PARAM_SELLDATE];
				}
				else
				{
					/*
					$curr_time = getdate ();
					$param_trans_selldate = $curr_time['year'] . '-'
						. $curr_time['mon'] . '-'
						. $curr_time['mday'] . ' '
						. $curr_time['hours'] . ':'
						. $curr_time['minutes'] . ':'
						. $curr_time['seconds'];
					*/
					$param_trans_selldate = date('Y-m-d H:i:s');
				}

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_SEND_PRICE]))
				{
					$param_trans_send_price = $_POST[TRIN_DB_TRANS_PARAM_SEND_PRICE];
				}

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_SEND_COST]))
				{
					$param_trans_send_cost = $_POST[TRIN_DB_TRANS_PARAM_SEND_COST];
				}

				if ($param_trans_paid == 'true')
				{
					$param_trans_paid = 'YES';
				}
				else
				{
					$param_trans_paid = 'NO';
				}

				if ($param_trans_sent == 'true')
				{
					$param_trans_sent = 'YES';
				}
				else
				{
					$param_trans_sent = 'NO';
				}

				trin_display_error($error);
?>
<p>
<label for="<?php echo TRIN_DB_TRANS_PARAM_PRICE ?>">Sell price:</label>
<?php
				trin_create_text_input('text', '20',
					TRIN_DB_TRANS_PARAM_PRICE,
					$param_trans_sell_price,
					$validation_failed_fields,
					'Only decimal values allowed, no currency names');
?>
</p>

<p>
<label for="<?php echo TRIN_DB_TRANS_PARAM_PAID ?>">Was the product paid for:</label>
<?php
				trin_create_select(TRIN_DB_TRANS_PARAM_PAID,
					$param_trans_paid,
					array('YES', 'NO'),
					array('YES', 'NO'),
					$validation_failed_fields)
?>
</p>

<p>
<label for="<?php echo TRIN_DB_TRANS_PARAM_SENT ?>">Was the product sent:</label>
<?php
				trin_create_select(TRIN_DB_TRANS_PARAM_SENT,
					$param_trans_sent,
					array('YES', 'NO'),
					array('YES', 'NO'),
					$validation_failed_fields)
?>
</p>

<p>
<label for="<?php echo TRIN_DB_TRANS_PARAM_SELLDATE ?>">Sell date:</label>
<?php
				trin_create_text_input('text', '20',
					TRIN_DB_TRANS_PARAM_SELLDATE,
					$param_trans_selldate,
					$validation_failed_fields);
?>
</p>

<p>
<label for="<?php echo TRIN_DB_TRANS_PARAM_SEND_PRICE ?>">Send price:</label>
<?php
				trin_create_text_input('text', '20',
					TRIN_DB_TRANS_PARAM_SEND_PRICE,
					$param_trans_send_price,
					$validation_failed_fields,
					'Only decimal values allowed, no currency names');
?>
</p>

<p>
<label for="<?php echo TRIN_DB_TRANS_PARAM_SEND_COST ?>">Send cost:</label>
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
<input type="submit" value="<?php echo $button_title; ?>">
<?php
	trin_create_reset ("reset_tx");
?>
</p>

</form>
</div>

<div class="menu">
<a href="transactions.php">Return</a>
|
<a href="add_transaction.php">Start anew</a>
</div>

<?php
			include 'menu.php';
			include 'footer.php';
?>

</BODY></HTML>
<?php
		} //$display_form
	} // trin_validate_session()
?>
