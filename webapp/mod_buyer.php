<?php
	/*
	 * Trinventum - modify buyer page.
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
	$validation_failed_fields = array();
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
			&& isset ($_POST[TRIN_DB_BUYER_PARAM_VERSION])
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
				$_POST[TRIN_DB_BUYER_PARAM_COMMENT],
				$_POST[TRIN_DB_BUYER_PARAM_VERSION]))
			{
				$display_form = TRUE;
				$error = 'Cannot update buyer in the database: '
					. trin_db_get_last_error ($db);
			}
			if (! $display_form)
			{
				trin_set_success_msg('Buyer updated successfully');
				header ('Location: buyers.php');
				exit;
			}
		}
		else
		{
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

<TITLE> Trinventum - modify buyer </TITLE>
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
<div class="login_box">
<?php
			$param_buyer_name = '';
			$param_buyer_address = '';
			$param_buyer_login = '';
			$param_buyer_email = '';
			$param_buyer_comment = '';
			$param_buyer_version = 0;

			$buyer = trin_db_get_buyer_details ($db, $_GET[TRIN_DB_BUYER_PARAM_ID]);
			if ($buyer !== FALSE)
			{
				$param_buyer_name = $buyer[TRIN_DB_BUYER_PARAM_NAME];
				$param_buyer_address = $buyer[TRIN_DB_BUYER_PARAM_ADDRESS];
				$param_buyer_login = $buyer[TRIN_DB_BUYER_PARAM_LOGIN];
				$param_buyer_email = $buyer[TRIN_DB_BUYER_PARAM_EMAIL];
				$param_buyer_comment = $buyer[TRIN_DB_BUYER_PARAM_COMMENT];
				$param_buyer_version = $buyer[TRIN_DB_BUYER_PARAM_VERSION];
			}
			else
			{
				trin_display_error ('Cannot read buyer details: No data');
			}

			// if the buyer failed to be updated,
			// refresh it from the DB and make the user
			// re-enter the data, else display what the use entered
			if (! $update_error)
			{
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
				/*
				always take the current version value
				if (isset ($_POST[TRIN_DB_BUYER_PARAM_VERSION]))
				{
					$param_buyer_version = $_POST[TRIN_DB_BUYER_PARAM_VERSION];
				}
				*/
			}

			trin_create_buyer_form (
				trin_get_self_action (), 'Update buyer',
				TRIN_DB_BUYER_PARAM_NAME, $param_buyer_name,
				TRIN_DB_BUYER_PARAM_ADDRESS, $param_buyer_address,
				TRIN_DB_BUYER_PARAM_LOGIN, $param_buyer_login,
				TRIN_DB_BUYER_PARAM_EMAIL, $param_buyer_email,
				TRIN_DB_BUYER_PARAM_COMMENT, $param_buyer_comment,
				TRIN_DB_BUYER_PARAM_VERSION, $param_buyer_version,
				$validation_failed_fields
			);
?>
</div>

<table>
<caption>Buyer's history of changes</caption>
<thead><tr>
 <th>Name was</th>
 <th>Address was</th>
 <th>Login was</th>
 <th>Email was</th>
 <th>Comment was</th>
 <th>Change user</th>
 <th>Change time</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_his = FALSE;
		if ($db)
		{
			$buyer_his = trin_db_get_buyer_history ($db,
				$_GET[TRIN_DB_BUYER_PARAM_ID]);
			if ($buyer_his !== FALSE)
			{
				while (TRUE)
				{
					$next_his = trin_db_get_next_buyer_hist_entry ($db, $buyer_his);
					if ($next_his === FALSE)
					{
						break;
					}
					$have_his = TRUE;
					$buyer_email = '<a href="mailto:'
						. $next_his[TRIN_DB_BUYER_PARAM_EMAIL]
						. '">'
						. $next_his[TRIN_DB_BUYER_PARAM_EMAIL]
						. '</a>';
					echo '<tr class="c">' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_BUYER_PARAM_NAME]) . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_BUYER_PARAM_ADDRESS]) . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_BUYER_PARAM_LOGIN]) . '</td>' .
						'<td>' . $buyer_email . '</td>' .
						'<td>' . trin_html_escape ($next_his[TRIN_DB_BUYER_PARAM_COMMENT]) . '</td>' .
						'<td>' . $next_his[TRIN_DB_BUYER_PARAM_USER] . '</td>' .
						'<td>' . $next_his[TRIN_DB_BUYER_PARAM_TIMESTAMP] . '</td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read buyer database: '
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
<tr><td colspan="7" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_his) && (! $error))
		{
?>
<tr><td colspan="7" class="c">No buyer history found</td></tr>
<?php
		} // ! $have_prod
?>
</tbody>
</table>

<div class="menu">
<a href="buyers.php">Return</a>
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
