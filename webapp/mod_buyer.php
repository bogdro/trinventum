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

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else if (! isset ($_GET[TRIN_DB_BUYER_PARAM_ID]))
	{
		header ('Location: buyers.php');
	}
	else
	{
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if (isset ($_POST[TRIN_DB_BUYER_PARAM_NAME])
			&& isset ($_POST[TRIN_DB_BUYER_PARAM_ADDRESS])
			&& isset ($_POST[TRIN_DB_BUYER_PARAM_LOGIN])
			&& isset ($_POST[TRIN_DB_BUYER_PARAM_EMAIL])
			&& isset ($_POST[TRIN_DB_BUYER_PARAM_COMMENT])
			)
		{
			if (!$db)
			{
				$display_form = TRUE;
				$error = 'Cannot connect to database';
			}
			if (! trin_db_update_buyer ($db,
				$_GET[TRIN_DB_BUYER_PARAM_ID],
				$_POST[TRIN_DB_BUYER_PARAM_NAME],
				$_POST[TRIN_DB_BUYER_PARAM_ADDRESS],
				$_POST[TRIN_DB_BUYER_PARAM_LOGIN],
				$_POST[TRIN_DB_BUYER_PARAM_EMAIL],
				$_POST[TRIN_DB_BUYER_PARAM_COMMENT]))
			{
				$display_form = TRUE;
				$error = 'Cannot update buyer in the database: ' . pg_last_error ();
			}
			if (! $display_form)
			{
				header ('Location: buyers.php');
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
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<META HTTP-EQUIV="X-Frame-Options"    CONTENT="DENY">
<LINK rel="stylesheet" type="text/css" href="trinventum.css">

<TITLE> Trinventum - modify buyer </TITLE>

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
<?php
			$param_buyer_name = '';
			$param_buyer_address = '';
			$param_buyer_login = '';
			$param_buyer_email = '';
			$param_buyer_comment = '';

			$buyer = trin_db_get_buyer_details ($db, $_GET[TRIN_DB_BUYER_PARAM_ID]);
			if ($buyer !== FALSE)
			{
				$param_buyer_name = $buyer[TRIN_DB_BUYER_PARAM_NAME];
				$param_buyer_address = $buyer[TRIN_DB_BUYER_PARAM_ADDRESS];
				$param_buyer_login = $buyer[TRIN_DB_BUYER_PARAM_LOGIN];
				$param_buyer_email = $buyer[TRIN_DB_BUYER_PARAM_EMAIL];
				$param_buyer_comment = $buyer[TRIN_DB_BUYER_PARAM_COMMENT];
			}

			if (isset ($_POST[TRIN_DB_BUYER_PARAM_NAME]))
			{
				$param_buyer_name = $_POST[TRIN_DB_BUYER_PARAM_NAME];
			}

			if (isset ($_POST[TRIN_DB_BUYER_PARAM_ADDRESS]))
			{
				$param_buyer_address = $_POST[TRIN_DB_BUYER_PARAM_ADDRESS];
			}

			if (isset ($_POST[TRIN_DB_BUYER_PARAM_LOGIN]))
			{
				$param_buyer_login = $_POST[TRIN_DB_BUYER_PARAM_LOGIN];
			}

			if (isset ($_POST[TRIN_DB_BUYER_PARAM_EMAIL]))
			{
				$param_buyer_email = $_POST[TRIN_DB_BUYER_PARAM_EMAIL];
			}

			if (isset ($_POST[TRIN_DB_BUYER_PARAM_COMMENT]))
			{
				$param_buyer_comment = $_POST[TRIN_DB_BUYER_PARAM_COMMENT];
			}

			trin_create_buyer_form (
				trin_get_self_action (), 'Update buyer',
				TRIN_DB_BUYER_PARAM_NAME, $param_buyer_name,
				TRIN_DB_BUYER_PARAM_ADDRESS, $param_buyer_address,
				TRIN_DB_BUYER_PARAM_LOGIN, $param_buyer_login,
				TRIN_DB_BUYER_PARAM_EMAIL, $param_buyer_email,
				TRIN_DB_BUYER_PARAM_COMMENT, $param_buyer_comment
			);
?>
</div>

<div class="menu">
<a href="buyers.php">Return</a>
</div>

<?php
			include ('footer.php');
?>

</BODY></HTML>
<?php
		} //$display_form
	} // trin_validate_session()
?>
