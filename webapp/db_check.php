<?php
	/*
	 * Trinventum - database check and upgrade script.
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
	include_once 'db_functions.php';

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	$error_msg = '';
	$warning_msg = '';
	// disable errors if the version-check table doesn't exist (fresh install):
	error_reporting (0);

	$conn = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
	if ($conn)
	{
		$trin_db_ver = trin_db_get_version ($conn);
		if ((int)$trin_db_ver == 0)
		{
			// just run the full script
			$file = file_get_contents ("sql/trinventum-full.pgsql");
			if ($file !== FALSE)
			{
				if (! trin_db_query ($conn, 'begin'))
				{
					$error_msg = "Can't update database version from $trin_db_ver to "
						. TRIN_EXPECTED_DB_VERSION
						. ' - cannot start transaction: '
						. trin_db_get_last_error ($conn);
				}
				else
				{
					if (! trin_db_query ($conn, $file))
					{
						$error_msg = "Can't update database version from $trin_db_ver to "
							. TRIN_EXPECTED_DB_VERSION
							. ': ' . trin_db_get_last_error ($conn);
					}
					else
					{
						trin_db_query ($conn, 'commit');
					}
				}
			}
			else
			{
				$error_msg = "Can't update database version from $trin_db_ver to "
					. TRIN_EXPECTED_DB_VERSION
					. ": can't read file sql/trinventum-full.pgsql";
			}
		}
		else if ((int)$trin_db_ver < (int)TRIN_EXPECTED_DB_VERSION)
		{
			for ($i = (int)$trin_db_ver + 1; $i <= (int)TRIN_EXPECTED_DB_VERSION; $i++)
			{
				// run the missing scripts
				$file = file_get_contents ("sql/trinventum-v$i.pgsql");
				if ($file !== FALSE)
				{
					if (! trin_db_query ($conn, 'begin'))
					{
						$error_msg = "Can't update database version from $trin_db_ver to "
							. TRIN_EXPECTED_DB_VERSION
							. ' - cannot start transaction: '
							. trin_db_get_last_error ($conn);
						break;
					}
					if (! trin_db_query ($conn, $file))
					{
						$error_msg = "Can't update database version from $trin_db_ver to "
							. TRIN_EXPECTED_DB_VERSION
							. ': ' . trin_db_get_last_error ($conn);
						trin_db_query ($conn, 'rollback');
						break;
					}
					else
					{
						trin_db_query ($conn, 'commit');
					}
				}
				else
				{
					$error_msg = "Can't update database version from $trin_db_ver to "
						. TRIN_EXPECTED_DB_VERSION
						. ": can't read file sql/trinventum-v$i.pgsql";
					break;
				}
			}
		}
		else if ((int)$trin_db_ver > (int)TRIN_EXPECTED_DB_VERSION)
		{
			$warning_msg = "Database version $trin_db_ver is newer than the expected version "
				. TRIN_EXPECTED_DB_VERSION
				. '.<br>The application may behave improperly.<br>'
				. 'You can <a href="main.php">continue</a> or '
				. '<a href="logout.php">logout and install the correct application version</a>.';
		}
		trin_db_close ($conn);
	}
	else
	{
		$error_msg = "Can't update database version from $trin_db_ver to "
			. TRIN_EXPECTED_DB_VERSION
			. ": can't connect to database";
	}

	if ($error_msg == '' && $warning_msg == '')
	{
		header ('Location: main.php');
	}
	else
	{
		if ($error_msg != '')
		{
			// Errors can't be skipped over. Make it impossible to continue
			session_destroy ();
		}

		$t_lastmod = getlastmod ();
		trin_header_lastmod ($t_lastmod);
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

<TITLE> Trinventum - database prepare problem </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>
<h1 class="c">Database prepare problem</h1>
<?php
		trin_display_error($error_msg);
		if ($warning_msg != '')
		{
?>
<p class="warning">
<?php echo $warning_msg; ?>
</p>

<?php
		}
		if ($error_msg != '')
		{
?>

<p>
Can't continue, because the database would be inconsistent with what the application expects.
You are logged-out now.
</p>
<p>
Fix the problem and <a href="login.php" hreflang="en">log-in again</a>.
</p>

<?php
		}
		include 'footer.php';
?>

</BODY></HTML>
<?php
	}
?>
