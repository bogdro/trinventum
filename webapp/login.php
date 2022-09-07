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

	//error_reporting (E_ALL|E_NOTICE); // crashes the db_check if first use
	session_start();

	include_once ('constants.php');
	include_once ('functions.php');
	include_once ('db_functions.php');

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);

	$display_form = FALSE;
	$error = '';
	$db = NULL;

	if (isset ($_POST[TRIN_SESS_DB_LOGIN])
		&& isset ($_POST[TRIN_SESS_DB_PASS])
		&& isset ($_POST[TRIN_SESS_DB_DBNAME])
		&& isset ($_POST[TRIN_SESS_DB_HOST]))
	{
		$db = trin_db_open ($_POST[TRIN_SESS_DB_LOGIN],
			$_POST[TRIN_SESS_DB_PASS],
			$_POST[TRIN_SESS_DB_DBNAME],
			$_POST[TRIN_SESS_DB_HOST]);
		if (!$db)
		{
			$display_form = TRUE;
			$error = 'Cannot connect to database';
		}
		else if (! trin_db_check ($db))
		{
			$display_form = TRUE;
			$error = 'Cannot check the database: ' . pg_last_error ();
			trin_db_close ($db);
		}
		if (! $display_form)
		{
			$_SESSION[TRIN_SESS_DB_LOGIN] = $_POST[TRIN_SESS_DB_LOGIN];
			$_SESSION[TRIN_SESS_DB_PASS] = $_POST[TRIN_SESS_DB_PASS];
			$_SESSION[TRIN_SESS_DB_DBNAME] = $_POST[TRIN_SESS_DB_DBNAME];
			$_SESSION[TRIN_SESS_DB_HOST] = $_POST[TRIN_SESS_DB_HOST];
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
<HEAD>
<META HTTP-EQUIV="Content-Type"       CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="Content-Language"   CONTENT="en">
<?php
		trin_meta_lastmod ($t_lastmod);
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<META HTTP-EQUIV="X-Frame-Options"    CONTENT="DENY">
<LINK rel="stylesheet" type="text/css" href="trinventum.css">

<TITLE> Trinventum - login </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		if ($error !== '')
		{
?>
Error: <?php echo $error.'<br>'; ?><br>
<?php
		}

		$param_db_login = '';
		$param_db_pass = '';
		$param_db_dbname = 'trinventum';
		$param_db_host = 'localhost';

		if (isset ($_POST[TRIN_SESS_DB_LOGIN]))
		{
			$param_db_login = $_POST[TRIN_SESS_DB_LOGIN];
		}

		if (isset ($_POST[TRIN_SESS_DB_PASS]))
		{
			$param_db_pass = $_POST[TRIN_SESS_DB_PASS];
		}

		if (isset ($_POST[TRIN_SESS_DB_DBNAME]))
		{
			$param_db_dbname = $_POST[TRIN_SESS_DB_DBNAME];
		}

		if (isset ($_POST[TRIN_SESS_DB_HOST]))
		{
			$param_db_host = $_POST[TRIN_SESS_DB_HOST];
		}
?>

<div class="login_box c">
Trinventum e-commerce management software
</div>

<div class="login_box">
<form action="<?php echo trin_get_self_action (); ?>" method="POST">

<p class="c">
Database connection parameters:
</p>

<p class="c">
Username:
<input type="text" size="20"
	value="<?php echo $param_db_login; ?>" name="<?php echo TRIN_SESS_DB_LOGIN; ?>">
</p>

<p class="c">
Password:
<input type="password" size="20"
	value="<?php echo $param_db_pass; ?>" name="<?php echo TRIN_SESS_DB_PASS; ?>">
</p>

<p class="c">
Server address:
<input type="text" size="20"
	value="<?php echo $param_db_host; ?>" name="<?php echo TRIN_SESS_DB_HOST; ?>">
</p>

<p class="c">
Database name:
<input type="text" size="20"
	value="<?php echo $param_db_dbname; ?>" name="<?php echo TRIN_SESS_DB_DBNAME; ?>">
</p>

<p class="c">
<input type="submit" value="Login"> <input type="reset" value="Reset">
</p>

</form>
</div>

<div class="c">
<a href="help.php">Help</a>
</div>

<?php
		include ('footer.php');
?>

</BODY></HTML>
<?php
	}
?>
