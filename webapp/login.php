<?php
	/*
	 * Trinventum - the login page.
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

	if (trin_isset_post(TRIN_SESS_DB_LOGIN)
		&& trin_isset_post(TRIN_SESS_DB_PASS)
		&& trin_isset_post(TRIN_SESS_DB_DBNAME)
		&& trin_isset_post(TRIN_SESS_DB_HOST)
		&& trin_isset_post(TRIN_SESS_DB_PORT))
	{
		$db = trin_db_open (trin_get_post(TRIN_SESS_DB_LOGIN),
			trin_get_post(TRIN_SESS_DB_PASS),
			trin_get_post(TRIN_SESS_DB_DBNAME),
			trin_get_post(TRIN_SESS_DB_HOST),
			trin_get_post(TRIN_SESS_DB_PORT));
		if (!$db)
		{
			$display_form = TRUE;
			$error = 'Cannot connect to database';
		}
		else if (! trin_db_check ($db))
		{
			$display_form = TRUE;
			$error = 'Cannot check the database: ' . trin_db_get_last_error ($db);
			trin_db_close ($db);
		}
		if (! $display_form)
		{
			trin_set_sess(TRIN_SESS_DB_LOGIN, trin_get_post(TRIN_SESS_DB_LOGIN));
			trin_set_sess(TRIN_SESS_DB_PASS, trin_get_post(TRIN_SESS_DB_PASS));
			trin_set_sess(TRIN_SESS_DB_DBNAME, trin_get_post(TRIN_SESS_DB_DBNAME));
			trin_set_sess(TRIN_SESS_DB_HOST, trin_get_post(TRIN_SESS_DB_HOST));
			trin_set_sess(TRIN_SESS_DB_PORT, trin_get_post(TRIN_SESS_DB_PORT));
			header ('Location: db_check.php');
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
<HEAD profile="http://www.w3.org/2005/10/profile">
<META HTTP-EQUIV="Content-Type"       CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="Content-Language"   CONTENT="en">
<?php
		trin_meta_lastmod ($t_lastmod);
		trin_include_css ();
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">

<TITLE> Trinventum - login </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		trin_display_error($error);

		$param_db_login = '';
		$param_db_pass = '';
		$param_db_dbname = 'trinventum';
		$param_db_host = 'localhost';
		$param_db_port = '';

		if (trin_isset_post(TRIN_SESS_DB_LOGIN))
		{
			$param_db_login = trin_get_post(TRIN_SESS_DB_LOGIN);
		}

		if (trin_isset_post(TRIN_SESS_DB_PASS))
		{
			$param_db_pass = trin_get_post(TRIN_SESS_DB_PASS);
		}

		if (trin_isset_post(TRIN_SESS_DB_DBNAME))
		{
			$param_db_dbname = trin_get_post(TRIN_SESS_DB_DBNAME);
		}

		if (trin_isset_post(TRIN_SESS_DB_HOST))
		{
			$param_db_host = trin_get_post(TRIN_SESS_DB_HOST);
		}

		if (trin_isset_post(TRIN_SESS_DB_PORT))
		{
			$param_db_port = trin_get_post(TRIN_SESS_DB_PORT);
		}
?>

<h1 class="title_box c">
<img src="rsrc/img/trinventum-icon.svg" class="vert_mid" alt="[Trinventum logo]">
Trinventum e-commerce management software
</h1>

<div class="login_box">
<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="POST">

<p class="c">
Database connection parameters:
</p>

<p>
<span class="par_name">
<label for="<?php echo TRIN_SESS_DB_LOGIN ?>">Username:</label>
</span>
<span class="par_value">
<?php
	trin_create_text_input('text', '20', TRIN_SESS_DB_LOGIN,
		$param_db_login, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo TRIN_SESS_DB_PASS ?>">Password:</label>
</span>
<span class="par_value">
<?php
	trin_create_text_input('password', '20', TRIN_SESS_DB_PASS,
		$param_db_pass, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo TRIN_SESS_DB_HOST ?>">Server address:</label>
</span>
<span class="par_value">
<?php
	trin_create_text_input('text', '20', TRIN_SESS_DB_HOST,
		$param_db_host, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo TRIN_SESS_DB_PORT ?>">Server port:</label>
</span>
<span class="par_value">
<?php
	trin_create_text_input('text', '20', TRIN_SESS_DB_PORT,
		$param_db_port, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo TRIN_SESS_DB_DBNAME ?>">Database name:</label>
</span>
<span class="par_value">
<?php
	trin_create_text_input('text', '20', TRIN_SESS_DB_DBNAME,
		$param_db_dbname, $validation_failed_fields);
?>
</span>
</p>

<p class="c">
<input type="submit" value="Login">
<?php
	trin_create_reset ("login_form");
?>
</p>

</form>
</div>

<div class="c">
<a href="help.php">Help</a>
</div>

<?php
		include 'inc/footer.php';
?>

</BODY></HTML>
<?php
	}
?>
