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

	session_start();

	include_once ('constants.php');
	include_once ('functions.php');
	include_once ('db_functions.php');

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);

	if (! trin_validate_session ())
	{
		header ('Location: login.php');
	}
	else if (! isset ($_GET[TRIN_PROD_DETAIL_PARAM]))
	{
		header ('Location: main.php');
	}
	else
	{
		$product_id = $_GET[TRIN_PROD_DETAIL_PARAM];
		$db = trin_db_open ($_SESSION[TRIN_SESS_DB_LOGIN],
			$_SESSION[TRIN_SESS_DB_PASS],
			$_SESSION[TRIN_SESS_DB_DBNAME],
			$_SESSION[TRIN_SESS_DB_HOST]);
		if (isset ($_POST[TRIN_DB_PROD_PARAM_NAME])
			&& isset ($_FILES[TRIN_DB_PROD_PARAM_PHOTO])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_SIZE])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_LENGTH])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_WIDTH])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COLOUR])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COUNT])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_BRAND])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_GENDER])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COMMENT])
			&& isset ($_POST[TRIN_DB_PROD_PARAM_COST]))
		{
			if (!$db)
			{
				$error = 'Cannot connect to database';
			}
			if (! trin_db_update_product ($db,
				$product_id,
				$_POST[TRIN_DB_PROD_PARAM_NAME],
				TRIN_DB_PROD_PARAM_PHOTO,
				$_POST[TRIN_DB_PROD_PARAM_SIZE],
				$_POST[TRIN_DB_PROD_PARAM_LENGTH],
				$_POST[TRIN_DB_PROD_PARAM_WIDTH],
				$_POST[TRIN_DB_PROD_PARAM_COLOUR],
				$_POST[TRIN_DB_PROD_PARAM_COUNT],
				$_POST[TRIN_DB_PROD_PARAM_BRAND],
				$_POST[TRIN_DB_PROD_PARAM_GENDER],
				$_POST[TRIN_DB_PROD_PARAM_COMMENT],
				$_POST[TRIN_DB_PROD_PARAM_COST]))
			{
				$error = 'Cannot update product in the database: ' . pg_last_error ();
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

<TITLE> Trinventum - product details </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
		include ('header.php');
?>

<div class="menu">
<a href="main.php">Return</a>
</div>

<p>Details of product <?php echo $product_id; ?>:</p>
<?php
		$param_pd_name = '';
		$param_pd_photo = '';
		$param_pd_size = 'N/A';
		$param_pd_length = '0';
		$param_pd_width = '0';
		$param_pd_colour = '';
		$param_pd_count = '';
		$param_pd_brand = '';
		$param_pd_gender = '-';
		$param_pd_comment = '';
		$param_pd_cost = '';

		$error = '';
		$have_prod = FALSE;
		if ($db)
		{
			$product_det = trin_db_get_product_details ($db,
				$product_id);
			if ($product_det !== FALSE)
			{
				$have_prod = TRUE;
				$param_pd_name = $product_det[TRIN_DB_PROD_DEF_FIELD_NAME];
				$param_pd_size = $product_det[TRIN_DB_PROD_DEF_FIELD_SIZE];
				$param_pd_length = $product_det[TRIN_DB_PROD_DEF_FIELD_LENGTH];
				$param_pd_width = $product_det[TRIN_DB_PROD_DEF_FIELD_WIDTH];
				$param_pd_colour = $product_det[TRIN_DB_PROD_DEF_FIELD_COLOUR];
				$param_pd_count = $product_det[TRIN_DB_PROD_DEF_FIELD_COUNT];
				$param_pd_brand = $product_det[TRIN_DB_PROD_DEF_FIELD_BRAND];
				$param_pd_gender = $product_det[TRIN_DB_PROD_DEF_FIELD_GENDER];
				$param_pd_comment = $product_det[TRIN_DB_PROD_DEF_FIELD_COMMENT];
				if ($product_det[TRIN_DB_PROD_DEF_FIELD_PHOTO] !== '-')
				{
					$photo = '<a href="get_photo.php?' .
						TRIN_PROD_PHOTO_PARAM . '=' .
						$product_id . '">' . $product_det[TRIN_DB_PROD_DEF_FIELD_PHOTO] . '</a>';
				}
				else
				{
					$photo = '-';
				}

				echo 	"<ul><li><p>Photo: $photo</p></li>" .
					'<li><p>Name: ' . $param_pd_name . '</p></li>' .
					'<li><p>Brand: ' . $param_pd_brand . '</p></li>' .
					'<li><p>Size: ' . $param_pd_size . '</p></li>' .
					'<li><p>Length: ' . $param_pd_length . '</p></li>' .
					'<li><p>Width: ' . $param_pd_width . '</p></li>' .
					'<li><p>Gender: ' . $param_pd_gender . '</p></li>' .
					'<li><p>Colour: ' . $param_pd_colour . '</p></li>' .
					'<li><p>Count:</p><p>' . $param_pd_count . '</p></li>' .
					'<li><p>Comment: ' . $param_pd_comment . '</p></li>' .
					'</ul>';

			}
			else
			{
				$error = 'Cannot read product details from the database: ' . pg_last_error ();
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		if ($error)
		{
?>
<p class="c">Error: <?php echo $error; ?></p>
<?php
		} // $error
		if ((! $have_prod) && (! $error))
		{
?>
<p class="c">Product not found</p>
<?php
		} // ! $have_prod
?>

<p class="c">
Update details (warning - this updates ALL the given details):
</p>

<div class="login_box">
<?php
		if (isset ($_POST[TRIN_DB_PROD_PARAM_NAME]))
		{
			$param_pd_name = $_POST[TRIN_DB_PROD_PARAM_NAME];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_PHOTO]))
		{
			$param_pd_photo = $_POST[TRIN_DB_PROD_PARAM_PHOTO];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_SIZE]))
		{
			$param_pd_size = $_POST[TRIN_DB_PROD_PARAM_SIZE];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_LENGTH]))
		{
			$param_pd_length = $_POST[TRIN_DB_PROD_PARAM_LENGTH];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_WIDTH]))
		{
			$param_pd_width = $_POST[TRIN_DB_PROD_PARAM_WIDTH];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_COLOUR]))
		{
			$param_pd_colour = $_POST[TRIN_DB_PROD_PARAM_COLOUR];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_COUNT]))
		{
			$param_pd_count = $_POST[TRIN_DB_PROD_PARAM_COUNT];
		}
		else
		{
			$param_pd_count = 0; // $param_pd_count is an HTML string hard to parse now
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_BRAND]))
		{
			$param_pd_brand = $_POST[TRIN_DB_PROD_PARAM_BRAND];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_GENDER]))
		{
			$param_pd_gender = $_POST[TRIN_DB_PROD_PARAM_GENDER];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_COMMENT]))
		{
			$param_pd_comment = $_POST[TRIN_DB_PROD_PARAM_COMMENT];
		}
		if (isset ($_POST[TRIN_DB_PROD_PARAM_COST]))
		{
			$param_pd_cost = $_POST[TRIN_DB_PROD_PARAM_COST];
		}

		trin_create_product_def_form (
			trin_get_self_action (), 'Update product',
			TRIN_DB_PROD_PARAM_NAME, $param_pd_name,
			TRIN_DB_PROD_PARAM_PHOTO, $param_pd_photo,
			TRIN_DB_PROD_PARAM_SIZE, $param_pd_size,
			TRIN_DB_PROD_PARAM_LENGTH, $param_pd_length,
			TRIN_DB_PROD_PARAM_WIDTH, $param_pd_width,
			TRIN_DB_PROD_PARAM_COLOUR, $param_pd_colour,
			TRIN_DB_PROD_PARAM_COUNT, $param_pd_count,
			TRIN_DB_PROD_PARAM_BRAND, $param_pd_brand,
			TRIN_DB_PROD_PARAM_GENDER, $param_pd_gender,
			TRIN_DB_PROD_PARAM_COMMENT, $param_pd_comment,
			TRIN_DB_PROD_PARAM_COST, $param_pd_cost
		);
?>
</div>

<table>
<caption>Product pieces</caption>
<thead><tr>
 <th>ID</th>
 <th>Status</th>
 <th>Cost</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_prod = FALSE;
		if ($db)
		{
			$products = trin_db_get_product_instances ($db, $product_id);
			if ($products !== FALSE)
			{
				while (TRUE)
				{
					$next_prod = trin_db_get_next_product_instance ($products);
					if ($next_prod === FALSE)
					{
						break;
					}
					$det_link = 'ppdetails.php?' . TRIN_PROD_DETAIL_PARAM
						. '=' . $_GET[TRIN_PROD_DETAIL_PARAM]
						. '&amp;' . TRIN_DB_PROD_INST_FIELD_ID
						. '=' . $next_prod[TRIN_DB_PROD_INST_FIELD_ID];
					$have_prod = TRUE;
					echo '<tr class="c">' .
						"<td><a href=\"$det_link\">"
							. $next_prod[TRIN_DB_PROD_INST_FIELD_ID] . '</a></td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_INST_FIELD_STATUS] . '</td>' .
						'<td>' . $next_prod[TRIN_DB_PROD_INST_FIELD_COST] . '</td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product database: ' . pg_last_error ();
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
		if ((! $have_prod) && (! $error))
		{
?>
<tr><td colspan="3" class="c">No product pieces found</td></tr>
<?php
		} // ! $have_prod
?>
</tbody>
</table>

<table>
<caption>Product sellings</caption>
<thead><tr>
 <th>Buyer</th>
 <th>Quantity</th>
</tr></thead>
<tbody>
<?php
		$error = '';
		$have_sale = FALSE;
		if ($db)
		{
			$sales = trin_db_get_product_sales ($db, $product_id);
			if ($sales !== FALSE)
			{
				while (TRUE)
				{
					$next_sale = trin_db_get_next_product_sale ($sales);
					if ($next_sale === FALSE)
					{
						break;
					}
					$buyer_link = 'mod_buyer.php?' . TRIN_DB_BUYER_PARAM_ID
						. '=' . $next_sale[TRIN_DB_BUYER_PARAM_ID];
					$have_sale = TRUE;
					echo '<tr class="c">' .
						"<td><a href=\"$buyer_link\">"
							. $next_sale[TRIN_DB_BUYER_PARAM_ID] . '</a></td>' .
						'<td>' . $next_sale[TRIN_DB_TRANS_PARAM_COUNT] . '</td></tr>'
						. "\n";
				}
			}
			else
			{
				$error = 'Cannot read product sale database: ' . pg_last_error ();
			}
		}
		else
		{
			$error = 'Cannot connect to database';
		}

		if ($error)
		{
?>
<tr><td colspan="2" class="c">Error: <?php echo $error; ?></td></tr>
<?php
		} // $error
		if ((! $have_sale) && (! $error))
		{
?>
<tr><td colspan="2" class="c">No product sales found</td></tr>
<?php
		} // ! $have_sale
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
	} // trin_validate_session ()
?>
