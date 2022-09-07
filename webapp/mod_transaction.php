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

	error_reporting (E_ALL|E_NOTICE);
	session_start();

	include_once ('constants.php');
	include_once ('functions.php');
	include_once ('db_functions.php');

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);

	$display_form = FALSE;
	$error = '';
	$db = NULL;
	$return_link = 'transactions.php?' . TRIN_DB_TRANS_PARAM_LIST . '=1';

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else if (! isset ($_GET[TRIN_DB_TRANS_PARAM_ID]))
	{
		header ("Location: $return_link");
	}
	else
	{
		$button_title = 'Next step';
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if (isset ($_POST[TRIN_PROD_DETAIL_PARAM]))
		{
			if (isset ($_POST[TRIN_DB_PROD_INST_FIELD_ID]))
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
					// update transaction
					if (!$db)
					{
						$display_form = TRUE;
						$error = 'Cannot connect to database';
					}
					if (! trin_db_update_transaction ($db,
						$_GET[TRIN_DB_TRANS_PARAM_ID],
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
						$error = 'Cannot update transaction in the database: '
							. pg_last_error ();
					}
					if (! $display_form)
					{
						header ("Location: $return_link");
					}
				}
				else
				{
					// seller, buer and the other parameters not selected
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
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<META HTTP-EQUIV="X-Frame-Options"    CONTENT="DENY">
<LINK rel="stylesheet" type="text/css" href="trinventum.css">

<TITLE> Trinventum - update a transaction </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
			include ('header.php');

			if ($error !== '')
			{
?>
Error: <?php echo $error.'<br>'; ?><br>
<?php
			}
?>
<div class="login_box">
<form action="<?php echo trin_get_self_action (); ?>" method="POST">
<?php
			$error = '';
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

			if ($db)
			{
				$trans = trin_db_get_transaction_details ($db,
					$_GET[TRIN_DB_TRANS_PARAM_ID]);
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
			}

			$display_trans_params = FALSE;

			if (!isset ($_POST[TRIN_PROD_DETAIL_PARAM]))
			{
				// display a list of product categories
				if ($db)
				{
					$products = trin_db_get_product_defs ($db);
					if ($products !== FALSE)
					{
						$have_prod = FALSE;
						echo '<p>Product type: <select name="'
							. TRIN_PROD_DETAIL_PARAM . "\">\n";
						while (TRUE)
						{
							$next_prod = trin_db_get_next_product ($db,
								$products);
							if ($next_prod === FALSE)
							{
								break;
							}
							$have_prod = TRUE;
							echo '<option value="' . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID]
								. '">' . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID] . ' - '
								. $next_prod[TRIN_DB_PROD_DEF_FIELD_NAME] .
								"</option>\n";
						}
						if (! $have_prod)
						{
							// no data found, but add a dummy option required by HTML!
							echo '<option value="-">-</option>';
						}
						echo "</select></p>\n";
					}
					else
					{
						$error = 'Cannot read product database: ' . pg_last_error ();
					}
				}
				else
				{
					$error = 'Cannot connect to database to get products';
				}
			}
			else
			{
				echo '<input type="hidden" name="' . TRIN_PROD_DETAIL_PARAM .
					'" value="' . $_POST[TRIN_PROD_DETAIL_PARAM] . "\">\n";
				if (!isset ($_POST[TRIN_DB_PROD_INST_FIELD_ID]))
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
							$have_prod = FALSE;
							echo '<p>Product piece: <select name="'
								. TRIN_DB_PROD_INST_FIELD_ID . "\">\n";
							while (TRUE)
							{
								$next_prod = trin_db_get_next_product_instance
									($products);
								if ($next_prod === FALSE)
								{
									break;
								}
								$have_prod = TRUE;
								echo '<option value="' . $next_prod[TRIN_DB_PROD_INST_FIELD_ID]
									. '"' .
									(($param_trans_prod == $next_prod[TRIN_DB_PROD_INST_FIELD_ID])?
										' selected="selected"' : '')
									. '>' . $next_prod[TRIN_DB_PROD_INST_FIELD_ID] .
									"</option>\n";
							}
							if (! $have_prod)
							{
								// no data found, but add a dummy
								// option required by HTML!
								echo '<option value="-">-</option>';
							}
							echo "</select></p>\n";
						}
						else
						{
							$error = 'Cannot read product instance database: ' . pg_last_error ();
						}
					}
					else if (!$db)
					{
						$error = 'Cannot connect to database to get product instances';
					}
				}
				else
				{
					echo '<input type="hidden" name="' . TRIN_DB_PROD_INST_FIELD_ID .
						'" value="' . $_POST[TRIN_DB_PROD_INST_FIELD_ID] . "\">\n";
					$display_trans_params = TRUE;
				}
			}

			if ($display_trans_params)
			{
				// display a list of sellers & buyers
				// and the remaining fields for a transaction
				if ($db)
				{
					$buyers = trin_db_get_buyers ($db);
					if ($buyers !== FALSE)
					{
						$have_buyer = FALSE;
						echo '<p>Buyer: <select name="' . TRIN_DB_BUYER_PARAM_ID
							. "\">\n";
						while (TRUE)
						{
							$next_buyer = trin_db_get_next_buyer ($buyers);
							if ($next_buyer === FALSE)
							{
								break;
							}
							$have_buyer = TRUE;
							echo '<option value="' . $next_buyer[TRIN_DB_BUYER_PARAM_ID]
								. '"' .
								(($param_trans_buyer == $next_buyer[TRIN_DB_BUYER_PARAM_ID])?
									' selected="selected"' : '')
								. '>' . $next_buyer[TRIN_DB_BUYER_PARAM_NAME] .
								"</option>\n";
						}
						if (! $have_buyer)
						{
							// no data found, but add a dummy
							// option required by HTML!
							echo '<option value="-">-</option>';
						}
						echo "</select></p>\n";
					}
					else
					{
						$error = 'Cannot read buyer database: ' . pg_last_error ();
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
						$have_seller = FALSE;
						echo '<p>Seller: <select name="' . TRIN_DB_SELLER_PARAM_ID
							. "\">\n";
						while (TRUE)
						{
							$next_seller = trin_db_get_next_seller ($sellers);
							if ($next_seller === FALSE)
							{
								break;
							}
							$have_seller = TRUE;
							echo '<option value="' . $next_seller[TRIN_DB_SELLER_PARAM_ID]
								. '"' .
								(($param_trans_seller == $next_seller[TRIN_DB_SELLER_PARAM_ID])?
									' selected="selected"' : '')
								. '>' . $next_seller[TRIN_DB_SELLER_PARAM_NAME] .
								"</option>\n";
						}
						if (! $have_seller)
						{
							// no data found, but add a dummy
							// option required by HTML!
							echo '<option value="-">-</option>';
						}
						echo "</select></p>\n";
					}
					else
					{
						$error = 'Cannot read seller database: ' . pg_last_error ();
					}
				}
				else
				{
					$error = 'Cannot connect to database';
				}

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
				else if ($param_trans_selldate == '')
				{
					$param_trans_selldate = trin_get_current_date_string ();
				}

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_SEND_PRICE]))
				{
					$param_trans_send_price = $_POST[TRIN_DB_TRANS_PARAM_SEND_PRICE];
				}

				if (isset ($_POST[TRIN_DB_TRANS_PARAM_SEND_COST]))
				{
					$param_trans_send_cost = $_POST[TRIN_DB_TRANS_PARAM_SEND_COST];
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
?>
<p>
Sell price:
<input type="text" size="20"
	value="<?php echo $param_trans_sell_price; ?>"
	name="<?php echo TRIN_DB_TRANS_PARAM_PRICE; ?>">
</p>

<p>
Was the product paid for:
<select name="<?php echo TRIN_DB_TRANS_PARAM_PAID; ?>">

<option value="true"
<?php
				if ($param_trans_paid == 'YES')
				{
?>
	selected="selected"
<?php
				}
?>
>YES</option>

<option value="false"
<?php
				if ($param_trans_paid == 'NO')
				{
?>
	selected="selected"
<?php
				}
?>
>NO</option>
</select>

<p>
Was the product sent:
<select name="<?php echo TRIN_DB_TRANS_PARAM_SENT; ?>">

<option value="true"
<?php
				if ($param_trans_sent == 'YES')
				{
?>
	selected="selected"
<?php
				}
?>
>YES</option>

<option value="false"
<?php
				if ($param_trans_sent == 'NO')
				{
?>
	selected="selected"
<?php
				}
?>
>NO</option>
</select>

<p>
Sell date:
<input type="text" size="20"
	value="<?php echo $param_trans_selldate; ?>"
	name="<?php echo TRIN_DB_TRANS_PARAM_SELLDATE; ?>">
</p>

<p>
Send price:
<input type="text" size="20"
	value="<?php echo $param_trans_send_price; ?>"
	name="<?php echo TRIN_DB_TRANS_PARAM_SEND_PRICE; ?>">
</p>

<p>
Send cost:
<input type="text" size="20"
	value="<?php echo $param_trans_send_cost; ?>"
	name="<?php echo TRIN_DB_TRANS_PARAM_SEND_COST; ?>">
</p>

<?php
			} // $display_trans_params
?>

<p>
<input type="submit" value="<?php echo $button_title; ?>"> <input type="reset" value="Reset">
</p>

<?php
			if ($error !== '')
			{
?>
Error: <?php echo $error.'<br>'; ?><br>
<?php
			}
?>
</form>
</div>

<div class="menu">
<a href="<?php echo $return_link; ?>">Return</a>
|
<a href="<?php echo 'mod_transaction.php?' . TRIN_DB_TRANS_PARAM_ID
	. '=' . $_GET[TRIN_DB_TRANS_PARAM_ID]; ?>">Start anew</a>
</div>

<?php
			include ('footer.php');
?>

</BODY></HTML>
<?php
		} //$display_form
	} // trin_validate_session()
?>
