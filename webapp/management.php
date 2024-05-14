<?php
	/*
	 * Trinventum - database management page.
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

	$error = '';
	$db = NULL;
	$validation_failed_fields = array();

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else
	{
		if (strtoupper(trin_get_server('REQUEST_METHOD')) != 'POST')
		{
			trin_unset_sess(TRIN_FORM_SUBMIT_DB_DESTROY);
		}
		if (trin_isset_post(TRIN_FORM_SUBMIT_DB_DESTROY))
		{
			trin_set_sess(TRIN_FORM_SUBMIT_DB_DESTROY, trin_get_post(TRIN_FORM_SUBMIT_DB_DESTROY));
		}
		if (trin_isset_sess(TRIN_FORM_SUBMIT_DB_DESTROY)
			&& trin_isset_post(TRIN_FORM_SUBMIT_DB_DESTROY2))
		{
			// destroy and logout if successful
			$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
				trin_get_sess(TRIN_SESS_DB_PASS),
				trin_get_sess(TRIN_SESS_DB_DBNAME),
				trin_get_sess(TRIN_SESS_DB_HOST));
			if (!$db)
			{
				$display_form = TRUE;
				$error = 'Cannot connect to database';
			}
			else if (! trin_db_destroy_schema ($db))
			{
				$display_form = TRUE;
				$error = 'Cannot destroy the database: '
					. trin_db_get_last_error ($db);
			}
			else
			{
				header ('Location: logout.php');
			}
		}
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

<TITLE> Trinventum - management </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include 'header.php';
		include 'menu.php';

		trin_display_error($error);

		if (count ($_POST) == 0 ||
			trin_isset_post(TRIN_FORM_PARAM_DB_QUERY))
		{
?>

<!-- ========================== Database query =========================== -->

<h2 class="c">Database query</h2>

<p>
Use this form to perform generic database queries.
</p>

<p class="warning">
WARNING<br><br>
Anything you type is sent directly to the database.<br> No validation is done,
no syntax checking is performed.<br> Some statements can cause damage to the database.
<br>
Use with care.
</p>

<p>
Sample queries:
</p>
<ul>
 <li>List all product types:
	<pre>
	select * from trinventum.product_definitions order by pd_id;</pre>
	</li>
 <li>List all product pieces:
	<pre>
	select * from trinventum.products order by p_id;</pre>
	</li>
 <li>List all product types and their pieces:
	<pre>
	select * from trinventum.product_definitions
	join trinventum.products on p_pd_id = pd_id
	order by pd_id;</pre>
	</li>
 <li>List all sellers:
	<pre>
	select * from trinventum.sellers order by s_id;</pre>
	</li>
 <li>List all buyers:
	<pre>
	select * from trinventum.buyers order by b_id;</pre>
	</li>
 <li>List all transactions:
	<pre>
	select * from trinventum.transactions order by t_id;</pre>
	</li>
 <li>List all transactions with their product, seller and buyer details (limited to 100 transactions):
	<pre>
	select t.t_id, p.p_pd_id, t.t_product_id, pd.pd_name, t.t_seller, s.s_name,
	t.t_buyer, b.b_name, t.t_price, t.t_paid,
	t.t_sent, t.t_sell_date, t.t_send_price, t.t_send_cost
	from trinventum.transactions t join trinventum.products p on p.p_id = t.t_product_id
	join trinventum.product_definitions pd on pd.pd_id = p.p_pd_id
	join trinventum.buyers b on b.b_id  = t.t_buyer
	join trinventum.sellers s on s.s_id  = t.t_seller
	order by t_id desc offset 0 limit 100</pre>
	</li>
 <li>List product counts:
	<pre>
	select p_status::text as p_status, count(*) as p_count from trinventum.products
	group by p_status
	union all select 'Total' as p_status,
	count(*) as p_count from trinventum.products order by p_status</pre>
	</li>
 <li>List product buys:
	<pre>
	select b_id, b_name, count(*) as b_count from trinventum.transactions
	join trinventum.buyers on b_id = t_buyer where t_product_id in
	(select p_id from trinventum.products)
	group by b_id, b_name order by count(*) desc</pre>
	</li>
 <li>List product sales:
	<pre>
	select s_id, s_name, count(*) as s_count from trinventum.transactions
	join trinventum.sellers on s_id = t_seller where t_product_id in
	(select p_id from trinventum.products)
	group by s_id, s_name order by count(*) desc</pre>
	</li>
 <li>List all transactions from the last year:
	<pre>
	select t.t_id, p.p_pd_id, t.t_product_id, pd.pd_name, t.t_seller, s.s_name,
	t.t_buyer, b.b_name, t.t_price, t.t_paid,
	t.t_sent, t.t_sell_date, t.t_send_price, t.t_send_cost
	from trinventum.transactions t join trinventum.products p on p.p_id = t.t_product_id
	join trinventum.product_definitions pd on pd.pd_id = p.p_pd_id
	join trinventum.buyers b on b.b_id  = t.t_buyer
	join trinventum.sellers s on s.s_id  = t.t_seller
	where t_sell_date > now()::date - 365</pre>
	</li>
 <li>Calculate the selling amounts of all transactions from the last year:
	<pre>
	select sum(t_price)
	from trinventum.transactions
	where t_sell_date > now()::date - 365</pre>
	</li>
 <li>Calculate the selling amount and profit of all transactions
 	from the last year (sum of selling amounts
 	plus the profit from sending minus the costs of product pieces):
	<pre>
	select sum(t.t_price) as sell_value,
	sum(t.t_price) + sum(t.t_send_price) - sum(t.t_send_cost) - sum(p.p_cost) as profit
	from trinventum.transactions t
	join trinventum.products p on p.p_id = t.t_product_id
	where t.t_sell_date > current_date - 365</pre>
	</li>
</ul>

<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="POST" class="c">
<label for="<?php echo TRIN_FORM_PARAM_DB_QUERY ?>">Query:</label>
<?php
			$param_db_query_value = '';
			if (trin_isset_post(TRIN_FORM_PARAM_DB_QUERY))
			{
				$param_db_query_value =
					trin_get_post(TRIN_FORM_PARAM_DB_QUERY);
			}

			trin_create_textarea ('15', '70', TRIN_FORM_PARAM_DB_QUERY,
				$param_db_query_value, $validation_failed_fields)
?>

<input type="submit" value="Query database">
</form>

<?php
			if (trin_isset_post(TRIN_FORM_PARAM_DB_QUERY))
			{
				$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
					trin_get_sess(TRIN_SESS_DB_PASS),
					trin_get_sess(TRIN_SESS_DB_DBNAME),
					trin_get_sess(TRIN_SESS_DB_HOST));
				if (!$db)
				{
					$error = 'Cannot connect to database';
				}
				$res = trin_db_query ($db,
					trin_get_post(TRIN_FORM_PARAM_DB_QUERY));
				if (! $res)
				{
					$error = 'Error querying the database: '
						. trin_db_get_last_error ($db);
				}
				trin_display_error($error);
				if ($res)
				{
					$nrows = trin_db_query_get_numrows ($res);
					echo '<p>Number of rows returned: ' . $nrows . '</p>';
					$ncolumns = trin_db_query_get_numfields ($res);
					$coltypes = array();

					echo "<table>\n<thead><tr>\n";
					for ($i = 0; $i < $ncolumns; $i++)
					{
						$coltype = trin_db_query_get_column_type ($res, $i);
						echo '<th>'
							. trin_db_query_get_column_name ($res, $i)
							. "<br>$coltype"
							. "<hr></th>\n";
						$coltypes[] = $coltype;
					}
					echo "</tr></thead>\n<tbody>\n";
					for ($r = 0; $r < $nrows; $r++)
					{
						$row = trin_db_query_get_next_row ($res);
						if ($row !== FALSE)
						{
							echo '<tr>';
							for ($i = 0; $i < $ncolumns; $i++)
							{
								echo '<td>';

								if ($coltypes[$i] == 'bytea'
									|| $coltypes[$i] == 'blob')
								{
									if ($row[$i] === NULL)
									{
										echo "NULL";
									}
									else
									{
										echo "-- Binary data --";
									}
								}
								else
								{
									echo $row[$i];
								}
								if ($i == $ncolumns - 1)
								{
									echo '<hr>';
								}
								echo '</td>';
							}
							echo "</tr>\n";
						}
					}

					echo "</tbody>\n</table>\n";

				}
			}
?>

<hr>

<!-- ========================== Connection parameters =========================== -->

<h2 class="c">Connection parameters</h2>

<?php
			$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
				trin_get_sess(TRIN_SESS_DB_PASS),
				trin_get_sess(TRIN_SESS_DB_DBNAME),
				trin_get_sess(TRIN_SESS_DB_HOST));

			echo '<p>Database login: ' . trin_get_sess(TRIN_SESS_DB_LOGIN) . "</p>\n";

			echo '<p>Database name: ' . trin_get_sess(TRIN_SESS_DB_DBNAME) . "</p>\n";

			echo '<p>Database host: ' . trin_get_sess(TRIN_SESS_DB_HOST) . "</p>\n";

			echo '<p>Connection options, if any: ' . pg_options ($db) . "</p>\n";

			echo '<p>Backend PID for this call: ' . pg_get_pid ($db) . "</p>\n";

			echo "<p>Parameters:</p>\n<ul>\n";
			echo ' <li>server_version = ' . pg_parameter_status ($db, 'server_version') . "</li>\n";
			echo ' <li>server_encoding = ' . pg_parameter_status ($db, 'server_encoding') . "</li>\n";
			echo ' <li>client_encoding = ' . pg_parameter_status ($db, 'client_encoding') . "</li>\n";
			echo ' <li>is_superuser = ' . pg_parameter_status ($db, 'is_superuser') . "</li>\n";
			echo ' <li>session_authorization = ' . pg_parameter_status ($db, 'session_authorization') . "</li>\n";
			echo ' <li>DateStyle = ' . pg_parameter_status ($db, 'DateStyle') . "</li>\n";
			echo ' <li>TimeZone = ' . pg_parameter_status ($db, 'TimeZone') . "</li>\n";
			echo ' <li>integer_datetimes = ' . pg_parameter_status ($db, 'integer_datetimes') . "</li>\n";
			echo "</ul>\n";

			echo '<p>Host and port (if applicable): ' . pg_host ($db)
				. ':' . pg_port ($db) . "</p>\n";

			echo "<p>Version information:</p>\n<pre>";
			print_r (pg_version ($db));
			echo "</pre>\n";

			//echo '<p>Client encoding: ' . pg_client_encoding ($db) . "</p>\n"; // same as in "Parameters"
?>

<!-- ========================== Database destroy =========================== -->

<hr>

<?php
// <!-- ========================== Database destroy confirmation =========================== -->


		} // count ($_POST) == 0 || isset (trin_get_post(TRIN_FORM_PARAM_DB_QUERY)
		if (! trin_isset_sess(TRIN_FORM_SUBMIT_DB_DESTROY))
		{
?>

<h2 class="c nok">Database destroy</h2>

<p class="error">
This operation deletes all structures and data in the database and cannot be reversed.
</p>

<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="POST" class="c">
<input type="submit" name="<?php echo TRIN_FORM_SUBMIT_DB_DESTROY; ?>" value="Destroy database">
</form>

<hr>

<?php

		} // ! trin_isset_sess(TRIN_FORM_SUBMIT_DB_DESTROY)
		if (trin_isset_sess(TRIN_FORM_SUBMIT_DB_DESTROY)
			&& ! trin_isset_post(TRIN_FORM_SUBMIT_DB_DESTROY2))
		{
			// double-check
?>
<div class="error c">
<p>
<em class="b">WARNING</em>:
this operation deletes all structures and data in the database and cannot be reversed.
</p>
<p>
Only a database dump can restore the database (see <a href="help.php" lang="en">Help</a>
on how to create a database dump).
</p>
<p>
If you proceed, you will be logged-out as the application will no longer be usable.<br>
After logging-in again, the database structures will be re-created, but with no data.
</p>

<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="POST" class="c">
<input type="submit" name="<?php echo TRIN_FORM_SUBMIT_DB_DESTROY2; ?>" value="Proceed">
</form>

</div>

<?php
		}

		include 'menu.php';
		include 'footer.php';
?>

</BODY></HTML>
<?php
	} // trin_validate_session()
?>
