<?php
	/*
	 * Trinventum - product pieces' page.
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
	include_once 'inc/db_functions.php';

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);

	$validation_failed_fields = array();

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
<HEAD profile="http://www.w3.org/2005/10/profile">
<META HTTP-EQUIV="Content-Type"       CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="Content-Language"   CONTENT="en">
<?php
		trin_meta_lastmod ($t_lastmod);
		trin_include_css ();
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">

<TITLE> Trinventum - product pieces </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include 'inc/header.php';
		include 'inc/menu.php';

		$offset = 0;
		$limit = 1000000000;

		if (trin_isset_get(TRIN_DB_PROD_INST_LIST_PARAM_START))
		{
			$offset = trin_get_param(TRIN_DB_PROD_INST_LIST_PARAM_START);
		}

		if (trin_isset_get(TRIN_DB_PROD_INST_LIST_PARAM_COUNT))
		{
			$limit = trin_get_param(TRIN_DB_PROD_INST_LIST_PARAM_COUNT);
		}

		$db = trin_db_open (trin_get_sess(TRIN_SESS_DB_LOGIN),
			trin_get_sess(TRIN_SESS_DB_PASS),
			trin_get_sess(TRIN_SESS_DB_DBNAME),
			trin_get_sess(TRIN_SESS_DB_HOST));
?>

<div class="menu">
<form action="<?php echo trin_html_escape(trin_get_self_action ()); ?>" method="GET">
List
<?php
		trin_create_text_input('text', '11', TRIN_DB_PROD_INST_LIST_PARAM_COUNT,
			$limit, $validation_failed_fields);
?>
<label for="<?php echo TRIN_DB_PROD_INST_LIST_PARAM_COUNT ?>">newest product pieces</label>,
<label for="<?php echo TRIN_DB_PROD_INST_LIST_PARAM_START ?>">skipping</label>
<?php
		trin_create_text_input('text', '5', TRIN_DB_PROD_INST_LIST_PARAM_START,
			$offset, $validation_failed_fields);
?>
first ones - <input type="submit" value="Go!">
</form>
OR
<a href="<?php echo trin_html_escape(trin_get_server('PHP_SELF')) . '?' . TRIN_DB_PROD_INST_PARAM_LIST . '=1'; ?>"
>List all product pieces</a>
</div>

<?php
		if (trin_isset_get(TRIN_DB_PROD_INST_PARAM_LIST)
			|| (trin_isset_get(TRIN_DB_PROD_INST_LIST_PARAM_START)
				&& trin_isset_get(TRIN_DB_PROD_INST_LIST_PARAM_COUNT)))
		{
?>

<table>
<caption>All product pieces</caption>
<thead><tr>
 <th>ID</th>
 <th>Product type</th>
 <th>Status</th>
 <th>Cost</th>
</tr></thead>
<tbody>
<?php
			$error = '';
			$have_prod = FALSE;
			if ($db)
			{
				$products = trin_db_get_all_product_instances ($db,
					$offset, $limit);
				if ($products !== FALSE)
				{
					while (TRUE)
					{
						$next_prod = trin_db_get_next_product_instance ($db, $products);
						if ($next_prod === FALSE)
						{
							break;
						}
						$prod_link = 'details.php?' . TRIN_PROD_DETAIL_PARAM
							. '=' . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID];
						$det_link = 'ppdetails.php?' . TRIN_PROD_DETAIL_PARAM
							. '=' . $next_prod[TRIN_DB_PROD_DEF_FIELD_ID]
							. '&amp;' . TRIN_DB_PROD_INST_FIELD_ID
							. '=' . $next_prod[TRIN_DB_PROD_INST_FIELD_ID];
						$have_prod = TRUE;
						echo '<tr class="c">' .
							"<td><a href=\"$det_link\">"
								. $next_prod[TRIN_DB_PROD_INST_FIELD_ID] . '</a></td>' .
							"<td><a href=\"$prod_link\">"
								. trin_html_escape ($next_prod[TRIN_DB_PROD_DEF_FIELD_NAME]) . '</a></td>' .
							'<td>' . trin_html_escape ($next_prod[TRIN_DB_PROD_INST_FIELD_STATUS]) . '</td>' .
							'<td>' . $next_prod[TRIN_DB_PROD_INST_FIELD_COST] . '</td></tr>'
							. "\n";
					}
				}
				else
				{
					$error = 'Cannot read product database: '
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
<tr><td colspan="4" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
			} // $error
			if ((! $have_prod) && (! $error))
			{
?>
<tr><td colspan="4" class="c">No product pieces found</td></tr>
<?php
			} // ! $have_prod
?>
</tbody>
</table>
<?php
		}
?>

<hr>

<table>
<caption>Product pieces by current status</caption>
<thead><tr>
 <th>Status</th>
 <th>Count</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_prod = FALSE;
		if ($db)
		{
			$products = trin_db_count_all_products ($db);
			if ($products !== FALSE)
			{
				foreach ($products as $status => $count)
				{
					echo '<tr class="c">' .
						"<td>$status</td>" .
						"<td>$count</td></tr>\n";
					$have_prod = TRUE;
				}
			}
			else
			{
				$error = 'Cannot read product database: '
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
<tr><td colspan="2" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
		} // $error
		if ((! $have_prod) && (! $error))
		{
?>
<tr><td colspan="2" class="c">No product pieces found</td></tr>
<?php
		} // ! $have_prod
?>
</tbody>
</table>

<hr>

<?php
		for ($m = 0; $m < 12; $m++)
		{
?>
<table>
<caption>Product piece status changes -
<?php
			if ($m == 0)
			{
				echo 'this month';
			}
			else if ($m == 1)
			{
				echo 'last month';
			}
			else
			{
				echo "$m months ago";
			}
?>
</caption>
<thead><tr>
 <th>Status changed from</th>
 <th>By this many pieces</th>
</tr></thead>
<tbody>
<?php
			$error = '';
			$have_prod = FALSE;
			if ($db)
			{
				$products = trin_db_get_product_status_changes ($db, $m);
				if ($products !== FALSE)
				{
					if (count ($products) > 0)
					{
						foreach ($products as $status => $count)
						{
							echo '<tr class="c">' .
								"<td>$status</td>" .
								"<td>$count</td></tr>\n";
						}
						$have_prod = TRUE;
					}
				}
				else
				{
					$error = 'Cannot read product database: '
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
<tr><td colspan="2" class="c">Error: <?php trin_display_error ($error); ?></td></tr>
<?php
			} // $error
			if ((! $have_prod) && (! $error))
			{
?>
<tr><td colspan="2" class="c">No status changes found</td></tr>
<?php
			} // ! $have_prod
?>
</tbody>
</table>

<?php
		} // for $m
		include 'inc/menu.php';
		include 'inc/footer.php';
?>

</BODY></HTML>
<?php
	} // trin_validate_session ()
?>
