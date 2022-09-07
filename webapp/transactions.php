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

	$error = '';
	$db = NULL;

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else
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

<TITLE> Trinventum - manage transactions </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include ('header.php');

		if (isset ($_GET[TRIN_DB_TRANS_PARAM_LIST]))
		{
?>

<div class="menu">
<a href="main.php">Return</a>
|
<a href="add_transaction.php">Register a new transaction</a>
</div>

<table>
<caption>Registered transactions</caption>
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
			$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
				$_SESSION[TRIN_SESS_DB_PASS],
				$_SESSION[TRIN_SESS_DB_DBNAME],
				$_SESSION[TRIN_SESS_DB_HOST]);
			if ($db)
			{
				$trans = trin_db_get_transactions ($db);
				if ($trans !== FALSE)
				{
					while (TRUE)
					{
						$next_tran = trin_db_get_next_transaction ($trans);
						if ($next_tran === FALSE)
						{
							break;
						}
						$have_trans = TRUE;

						$paid = 'YES';
						if ($next_tran[TRIN_DB_TRANS_PARAM_PAID] === 'f')
						{
							$paid = 'NO';
						}

						$sent = 'YES';
						if ($next_tran[TRIN_DB_TRANS_PARAM_SENT] === 'f')
						{
							$sent = 'NO';
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
						echo '<tr class="c">' .
							'<td><a href="' . $tran_link . '">' . $next_tran[TRIN_DB_TRANS_PARAM_ID] . '</a></td>' .
							'<td><a href="' . $product_def_link . '">' . $next_tran[TRIN_DB_PROD_DEF_FIELD_ID] . '</a></td>' .
							'<td><a href="' . $product_link . '">' . $next_tran[TRIN_DB_PROD_INST_FIELD_ID] . '</a></td>' .
							'<td><a href="sellers.php">' . $next_tran[TRIN_DB_SELLER_PARAM_ID] . '</a></td>' .
							'<td><a href="buyers.php">' . $next_tran[TRIN_DB_BUYER_PARAM_ID] . '</a></td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_PRICE] . '</td>' .
							'<td>' . $paid . '</td>' .
							'<td>' . $sent . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SELLDATE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_PRICE] . '</td>' .
							'<td>' . $next_tran[TRIN_DB_TRANS_PARAM_SEND_COST] . '</td></tr>'
							. "\n";
					}
				}
				else
				{
					$error = 'Cannot read transaction database: ' . pg_last_error ();
				}
			}
			else
			{
				$error = 'Cannot connect to database';
			}

			if ($error)
			{
?>
<tr><td colspan="11" class="c">Error: <?php echo $error; ?></td></tr>
<?php
			} // $error
			if ((! $have_trans) && (! $error))
			{
?>
<tr><td colspan="11" class="c">No transactions found</td></tr>
<?php
			} // ! $have_trans
?>
</tbody>
</table>

<?php
		}
		else // ! TRIN_DB_TRANS_PARAM_LIST
		{
?>

<div class="c menu">
<a href="<?php echo $_SERVER['PHP_SELF'] . '?' . TRIN_DB_TRANS_PARAM_LIST . '=1'; ?>">List all transactions</a>
|
<a href="add_transaction.php">Register a new transaction</a>
</div>

<?php
		} // TRIN_DB_TRANS_PARAM_LIST
?>

<div class="menu">
<a href="main.php">Return</a>
|
<a href="add_transaction.php">Register a new transaction</a>
</div>

<?php
		include ('footer.php');
?>

</BODY></HTML>
<?php
	} // trin_validate_session()
?>
