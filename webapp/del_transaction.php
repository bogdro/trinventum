<?php
	/*
	 * Trinventum - transaction deletion page.
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
		if (trin_isset_post(TRIN_DB_TRANS_PARAM_ID))
		{
			// delete transaction
			if (!$db)
			{
				$display_form = TRUE;
				$error = 'Cannot connect to database';
			}
			if (! trin_db_delete_transaction ($db,
				trin_get_post(TRIN_DB_TRANS_PARAM_ID)))
			{
				$display_form = TRUE;
				$error = 'Cannot delete transaction from the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				trin_set_success_msg('Transaction deleted successfully');
			}
		}
		if (! $display_form)
		{
			header ('Location: transactions.php?' . TRIN_DB_TRANS_PARAM_LIST . '=1');
			exit;
		}
		else
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

<TITLE> Trinventum - delete a transaction </TITLE>
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
?>

<div class="menu">
<a href=<?php echo 'transactions.php?' . TRIN_DB_TRANS_PARAM_LIST . '=1'; ?>>Return</a>
</div>

<?php
			include 'menu.php';
			include 'inc/footer.php';
?>

</BODY></HTML>
<?php
		} //$display_form
	} // trin_validate_session()
?>
