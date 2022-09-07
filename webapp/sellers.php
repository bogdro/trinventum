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
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if (isset ($_POST[TRIN_DB_SELLER_PARAM_NAME]))
		{
			if (!$db)
			{
				$error = 'Cannot connect to database';
			}
			if (! trin_db_add_seller ($db,
				$_POST[TRIN_DB_SELLER_PARAM_NAME]))
			{
				$error = 'Cannot add seller to the database: ' . pg_last_error ();
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

<TITLE> Trinventum - manage sellers </TITLE>

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

		$param_seller_name = '';

		if (isset ($_POST[TRIN_DB_SELLER_PARAM_NAME]))
		{
			$param_seller_name = $_POST[TRIN_DB_SELLER_PARAM_NAME];
		}

		trin_create_seller_form (
			trin_get_self_action (), 'Add seller',
			TRIN_DB_SELLER_PARAM_NAME, $param_seller_name
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
					$next_seller = trin_db_get_next_seller ($sellers);
					if ($next_seller === FALSE)
					{
						break;
					}
					$have_seller = TRUE;
					$seller_det_link = 'mod_seller.php?' . TRIN_DB_SELLER_PARAM_ID
						. '=' . $next_seller[TRIN_DB_SELLER_PARAM_ID];
					echo '<tr class="c">' .
						"<td><a href=\"$seller_det_link\">" . $next_seller[TRIN_DB_SELLER_PARAM_ID] . '</a></td>' .
						"<td><a href=\"$seller_det_link\">" . $next_seller[TRIN_DB_SELLER_PARAM_NAME] . '</a></td></tr>'
						. "\n";
				}
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

		if ($error)
		{
?>
<tr><td colspan="3" class="c">Error: <?php echo $error; ?></td></tr>
<?php
		} // $error
		if ((! $have_seller) && (! $error))
		{
?>
<tr><td colspan="3" class="c">No sellers found</td></tr>
<?php
		} // ! $have_seller
?>
</tbody>
</table>

<div class="menu">
<a href="main.php">Return</a>
</div>

<?php
		include ('footer.php');
?>

</BODY></HTML>
<?php
	} // trin_validate_session()
?>
