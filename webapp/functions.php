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

	include_once ('constants.php');

	function trin_header_lastmod ($last_mod)
	{
		// must be gmdate(), and not date(), because the HTTP specification says this
		$mod_header = gmdate (TRIN_HTTP_DATE_FORMAT, $last_mod);
		header ("Last-Modified: $mod_header");
	}

	function trin_meta_lastmod ($last_mod)
	{
		// must be gmdate(), and not date(), because the HTTP specification says this
		$mod_header = gmdate (TRIN_HTTP_DATE_FORMAT, $last_mod);
?>
<META HTTP-EQUIV="Last-Modified"      CONTENT="<?php echo $mod_header; ?>">
<?php
	}

	function trin_validate_session ()
	{
		if (isset ($_SESSION[TRIN_SESS_DB_LOGIN])
			&& isset ($_SESSION[TRIN_SESS_DB_PASS])
			&& isset ($_SESSION[TRIN_SESS_DB_DBNAME])
			&& isset ($_SESSION[TRIN_SESS_DB_HOST]))
		{
			return TRUE;
		}
		return FALSE;
	}

	function trin_create_product_def_form (
		$action, $button_title,
		$param_name_name, $param_name_value,
		$param_photo_name, $param_photo_value,
		$param_size_name, $param_size_value,
		$param_length_name, $param_length_value,
		$param_width_name, $param_width_value,
		$param_colour_name, $param_colour_value,
		$param_count_name, $param_count_value,
		$param_brand_name, $param_brand_value,
		$param_gender_name, $param_gender_value,
		$param_comment_name, $param_comment_value,
		$param_cost_name, $param_cost_value
		)
	{
?>

<form enctype="multipart/form-data"
action="<?php echo $action; ?>" method="POST">

<p>
Product name:
<input type="text" size="20"
	value="<?php echo $param_name_value; ?>" name="<?php echo $param_name_name; ?>">
</p>

<p>
Photo/image:
<input type="file" size="50"
	value="<?php echo $param_photo_value; ?>" name="<?php echo $param_photo_name; ?>">
</p>

<p>
Size (like XL, M, etc., if applicable):
<input type="text" size="20"
	value="<?php echo $param_size_value; ?>" name="<?php echo $param_size_name; ?>">
</p>

<p>
Length (if applicable):
<input type="text" size="20"
	value="<?php echo $param_length_value; ?>" name="<?php echo $param_length_name; ?>">
</p>

<p>
Width (if applicable):
<input type="text" size="20"
	value="<?php echo $param_width_value; ?>" name="<?php echo $param_width_name; ?>">
</p>

<p>
Colour:
<input type="text" size="20"
	value="<?php echo $param_colour_value; ?>" name="<?php echo $param_colour_name; ?>">
</p>

<p>
Count (number of pieces in store):
<input type="text" size="20"
	value="<?php echo $param_count_value; ?>" name="<?php echo $param_count_name; ?>">
</p>

<p>
Brand:
<input type="text" size="20"
	value="<?php echo $param_brand_value; ?>" name="<?php echo $param_brand_name; ?>">
</p>

<p>
Gender (Male/Female/Child, if applicable):
<select name="<?php echo $param_gender_name; ?>">

<option value="M"
<?php
		if ($param_gender_value == 'M')
		{
?>
	selected="selected"
<?php
		}
?>
>M</option>

<option value="F"
<?php
		if ($param_gender_value == 'F')
		{
?>
	selected="selected"
<?php
		}
?>
>F</option>

<option value="C"
<?php
		if ($param_gender_value == 'C')
		{
?>
	selected="selected"
<?php
		}
?>
>C</option>

<option value="-"
<?php
		if ($param_gender_value == '-')
		{
?>
	selected="selected"
<?php
		}
?>
>-</option>

</select>
</p>

<p>
Comment or description (can be empty):
<input type="text" size="20"
	value="<?php echo $param_comment_value; ?>" name="<?php echo $param_comment_name; ?>">
</p>

<p>
Cost:
<input type="text" size="20"
	value="<?php echo $param_cost_value; ?>" name="<?php echo $param_cost_name; ?>">
</p>

<p>
<input type="submit" value="<?php echo $button_title; ?>"> <input type="reset" value="Reset">
</p>

</form>
<?php
	}

	function trin_create_buyer_form (
		$action, $button_title,
		$param_buyer_name, $param_buyer_name_value,
		$param_buyer_address, $param_buyer_address_value,
		$param_buyer_login, $param_buyer_login_value,
		$param_buyer_email, $param_buyer_email_value,
		$param_buyer_comment, $param_buyer_comment_value
		)
	{
?>
<form action="<?php echo $action; ?>" method="POST">

<p>
Buyer name:
<input type="text" size="20"
	value="<?php echo $param_buyer_name_value; ?>" name="<?php echo $param_buyer_name; ?>">
</p>

<p>
Buyer postal address:
<input type="text" size="20"
	value="<?php echo $param_buyer_address_value; ?>" name="<?php echo $param_buyer_address; ?>">
</p>

<p>
Buyer login (ID):
<input type="text" size="20"
	value="<?php echo $param_buyer_login_value; ?>" name="<?php echo $param_buyer_login; ?>">
</p>

<p>
Buyer e-mail address:
<input type="text" size="20"
	value="<?php echo $param_buyer_email_value; ?>" name="<?php echo $param_buyer_email; ?>">
</p>

<p>
Buyer comment:
<input type="text" size="20"
	value="<?php echo $param_buyer_comment_value; ?>" name="<?php echo $param_buyer_comment; ?>">
</p>

<p>
<input type="submit" value="<?php echo $button_title; ?>"> <input type="reset" value="Reset">
</p>

</form>
<?php
	}


	function trin_create_seller_form (
		$action, $button_title,
		$param_seller_name, $param_seller_name_value
		)
	{
?>
<form action="<?php echo $action; ?>" method="POST">

<p>
Seller name:
<input type="text" size="20"
	value="<?php echo $param_seller_name_value; ?>" name="<?php echo $param_seller_name; ?>">
</p>

<p>
<input type="submit" value="<?php echo $button_title; ?>"> <input type="reset" value="Reset">
</p>

</form>

<?php
	}

	function trin_get_current_date_string ()
	{
		$curr_time = getdate ();
		return $curr_time['year'] . '-'
			. $curr_time['mon'] . '-'
			. $curr_time['mday'] . ' '
			. $curr_time['hours'] . ':'
			. $curr_time['minutes'] . ':'
			. $curr_time['seconds'];
	}

	function trin_get_self_action ()
	{
		$action = $_SERVER['PHP_SELF'];
		if (isset ($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '')
		{
			$action .= '?' . str_replace ('&', '&amp;', $_SERVER['QUERY_STRING']);
		}
		return $action;
	}
?>
