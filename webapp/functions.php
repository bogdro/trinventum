<?php
	/*
	 * Trinventum - non-database functions.
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

	include_once 'constants.php';

	function trin_error_reporting ()
	{
		//error_reporting (E_ALL|E_NOTICE);
		error_reporting (0);
	}

	function trin_isset_sess($name)
	{
		return isset($_SESSION[$name]);
	}

	function trin_get_sess($name)
	{
		return $_SESSION[$name];
	}

	function trin_set_sess($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	function trin_unset_sess($name)
	{
		unset($_SESSION[$name]);
	}

	function trin_isset_post($name)
	{
		return isset($_POST[$name]);
	}

	function trin_get_post($name)
	{
		return $_POST[$name];
	}

	function trin_isset_get($name)
	{
		return isset($_GET[$name]);
	}

	function trin_get_param($name)
	{
		return $_GET[$name];
	}

	function trin_isset_server($name)
	{
		return isset($_SERVER[$name]);
	}

	function trin_get_server($name)
	{
		return $_SERVER[$name];
	}

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

	function trin_include_css ()
	{
?>
<LINK rel="stylesheet" type="text/css" href="rsrc/trinventum.css">
<?php
	}

	function trin_validate_session ()
	{
		if (trin_isset_sess (TRIN_SESS_DB_LOGIN)
			&& trin_isset_sess (TRIN_SESS_DB_PASS)
			&& trin_isset_sess (TRIN_SESS_DB_DBNAME)
			&& trin_isset_sess (TRIN_SESS_DB_HOST))
		{
			return TRUE;
		}
		return FALSE;
	}

	function trin_create_text_input ($type, $size, $name, $value,
		$validation_failed_fields, $title = '')
	{
		echo "<input type=\"$type\"\n
			value=\"" . trin_html_escape ($value) . "\"\n
			name=\"$name\"\n
			id=\"$name\"\n"
			;
		if ($size != '')
		{
			echo "size=\"$size\"\n";
		}
		if ($title != '')
		{
			echo "title=\"$title\"\n";
		}
		if (in_array ($name, $validation_failed_fields))
		{
			echo "class=\"red_frame\"\n";
		}
		echo ">\n";
	}

	function trin_create_textarea ($rows, $cols, $name, $value,
		$validation_failed_fields, $title = '')
	{
		echo "<textarea cols=\"$cols\"\n
			rows=\"$rows\"\n
			name=\"$name\"\n
			id=\"$name\"\n\n";
		if ($title != '')
		{
			echo "title=\"$title\"\n";
		}
		$tclass = 'vert_mid';
		if (in_array ($name, $validation_failed_fields))
		{
			$tclass .= ' red_frame';
		}
		echo "class=\"$tclass\">" . trin_html_escape ($value) . "</textarea>\n";
	}

	function trin_create_select ($name, $value, $option_names,
		$option_values, $validation_failed_fields, $title = '')
	{
		echo "<select name=\"$name\"
			id=\"$name\"\n\n";
		if ($title != '')
		{
			echo "title=\"$title\"\n";
		}
		if (in_array ($name, $validation_failed_fields))
		{
			echo "class=\"red_frame\"\n";
		}
		echo ">\n";

		$nopts = count($option_values);
		if ($nopts == 0)
		{
			// add a dummy/empty value (required by HTML)
			echo "<option value=\"-\">-</option>\n";
		}
		else
		{
			for ($i = 0; $i < $nopts; $i++)
			{
				echo '<option value="' . trin_html_escape ($option_values[$i]) . '"';
				if ($value == $option_values[$i])
				{
					echo ' selected="selected"';
				}
				echo '>' . trin_html_escape ($option_names[$i]) . "</option>\n";
			}
		}
		echo "</select>\n";
	}

	function trin_create_file_input ($name, $accept_type, $value,
		$validation_failed_fields, $title = '')
	{
		echo "<input type=\"file\"\n
			name=\"$name\"\n
			value=\"" . trin_html_escape ($value) . "\"\n
			id=\"$name\"\n"
			;
		if ($title != '')
		{
			echo "title=\"$title\"\n";
		}
		if ($accept_type != '')
		{
			echo "accept=\"$accept_type\"\n";
		}
		if (in_array ($name, $validation_failed_fields))
		{
			echo "class=\"red_frame\"\n";
		}
		echo ">\n";
	}

	function trin_create_reset ($name)
	{
		echo "<br><br><label for=\"reset_$name\">Reset form:</label>\n"
			. "<input type=\"reset\" id=\"reset_$name\" value=\"Reset\">\n";
	}

	function trin_create_submits ($name, $value, $add_reset,
		$title = '')
	{
		echo "<input type=\"submit\"
			value=\"" . trin_html_escape ($value) . "\"\n";
		if ($name != '')
		{
			echo "name=\"$name\"\n";
		}
		if ($title != '')
		{
			echo "title=\"$title\"\n";
		}
		echo ">\n";
		if ($add_reset === TRUE)
		{
			trin_create_reset ($name);
		}
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
		$param_cost_name, $param_cost_value,
		$param_category_name, $param_category_value,
		$param_category_option_names_values,
		$param_version_name, $param_version_value,
		$validation_failed_fields, $separate_forms
		)
	{
?>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
?>
<p>
<label for="<?php echo $param_name_name ?>">Product name:</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_name_name,
			$param_name_value, $validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_name_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_name_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_category_name ?>">Product category:</label>
</p>
<?php
		$param_category_option_names = array();
		$param_category_option_values = array();
		foreach ($param_category_option_names_values as $k => $o)
		{
			$param_category_option_names[] = $o;
			$param_category_option_values[] = $k;
		}
		trin_create_select($param_category_name, $param_category_value,
			$param_category_option_names, $param_category_option_values,
			$validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_category_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_category_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_photo_name ?>">Photo/image:</label>
</p>
<?php
		trin_create_file_input($param_photo_name, 'image/*',
			$param_photo_value, $validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_photo_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_photo_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_size_name ?>">Size (like XL, M, etc., if applicable):</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_size_name,
			$param_size_value, $validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_size_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_size_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_length_name ?>">Length (if applicable):</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_length_name,
			$param_length_value, $validation_failed_fields,
			'Only decimal values allowed, no unit names');

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_length_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_length_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_width_name ?>">Width (if applicable):</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_width_name,
			$param_width_value, $validation_failed_fields,
			'Only decimal values allowed, no unit names');

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_width_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_width_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_colour_name ?>">Colour:</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_colour_name,
			$param_colour_value, $validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_colour_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_colour_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_count_name ?>">Count (number of pieces):</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_count_name,
			$param_count_value, $validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_count_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_count_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_brand_name ?>">Brand:</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_brand_name,
			$param_brand_value, $validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_brand_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_brand_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_gender_name ?>">Gender (Male/Female/Child, if applicable):</label>
</p>
<?php
		trin_create_select($param_gender_name, $param_gender_value,
			array('M', 'F', 'C', '-'),
			array('M', 'F', 'C', '-'),
			$validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_gender_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_gender_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_comment_name ?>">Comment or description (can be empty):</label>
</p>
<?php
		trin_create_textarea('5', '20', $param_comment_name,
			$param_comment_value, $validation_failed_fields);

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_comment_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_comment_name,
				'Update', TRUE);
?>
</form>
<form enctype="multipart/form-data" action="<?php echo trin_html_escape($action); ?>" method="POST">
<?php
		}
?>
<hr>
<p>
<label for="<?php echo $param_cost_name ?>">Cost of each piece:</label>
</p>
<?php
		trin_create_text_input('text', '20', $param_cost_name,
			$param_cost_value, $validation_failed_fields,
			'Only decimal values allowed, no currency names');

		if ($separate_forms === TRUE)
		{
			trin_create_text_input('hidden', '', $param_cost_name . '_' . $param_version_name,
				$param_version_value, $validation_failed_fields);
			trin_create_submits (TRIN_FORM_FIELD_SUBMIT_PREFIX . $param_cost_name,
				'Update', TRUE);
?>
</form>
<?php
		}
		else //if ($separate_forms === FALSE)
		{
			trin_create_text_input('hidden', '', $param_version_name,
				$param_version_value, $validation_failed_fields);
?>
<hr>
<p>
<input type="submit" value="<?php echo trin_html_escape($button_title); ?>">
<?php
	trin_create_reset ("prod_def_form");
?>
</p>
</form>
<?php
		}
	}

	function trin_create_buyer_form (
		$action, $button_title,
		$param_buyer_name, $param_buyer_name_value,
		$param_buyer_address, $param_buyer_address_value,
		$param_buyer_login, $param_buyer_login_value,
		$param_buyer_email, $param_buyer_email_value,
		$param_buyer_comment, $param_buyer_comment_value,
		$param_version_name, $param_version_value,
		$validation_failed_fields
		)
	{
?>
<form action="<?php echo trin_html_escape($action); ?>" method="POST">

<p>
<span class="par_name">
<label for="<?php echo $param_buyer_name ?>">Buyer name:</label>
</span>
<span class="par_value">
<?php
		trin_create_text_input('text', '20', $param_buyer_name,
			$param_buyer_name_value, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo $param_buyer_address ?>">Buyer postal address:</label>
</span>
<span class="par_value">
<?php
		trin_create_text_input('text', '20', $param_buyer_address,
			$param_buyer_address_value, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo $param_buyer_login ?>">Buyer login (ID):</label>
</span>
<span class="par_value">
<?php
		trin_create_text_input('text', '20', $param_buyer_login,
			$param_buyer_login_value, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo $param_buyer_email ?>">Buyer e-mail address:</label>
</span>
<span class="par_value">
<?php
		trin_create_text_input('text', '20', $param_buyer_email,
			$param_buyer_email_value, $validation_failed_fields);
?>
</span>
</p>

<p>
<span class="par_name">
<label for="<?php echo $param_buyer_comment ?>">Buyer comment:</label>
</span>
<span class="par_value">
<?php
		trin_create_textarea('5', '20', $param_buyer_comment,
			$param_buyer_comment_value, $validation_failed_fields)
?>
</span>
</p>

<p class="c">
<?php
		trin_create_text_input('hidden', '', $param_version_name,
			$param_version_value, $validation_failed_fields);
?>
<input type="submit" value="<?php echo $button_title; ?>">
<?php
	trin_create_reset ("buyer_form");
?>
</p>

</form>
<?php
	}


	function trin_create_seller_form (
		$action, $button_title,
		$param_seller_name, $param_seller_name_value,
		$param_version_name, $param_version_value,
		$validation_failed_fields
		)
	{
?>
<form action="<?php echo trin_html_escape($action); ?>" method="POST">

<p>
<span class="par_name">
<label for="<?php echo $param_seller_name ?>">Seller name:</label>
</span>
<span class="par_value">
<?php
		trin_create_text_input('text', '20', $param_seller_name,
			$param_seller_name_value, $validation_failed_fields);
?>
</span>
</p>

<p class="c">
<?php
		trin_create_text_input('hidden', '', $param_version_name,
			$param_version_value, $validation_failed_fields);
?>
<input type="submit" value="<?php echo $button_title; ?>">
<?php
	trin_create_reset ("seller_form");
?>
</p>

</form>

<?php
	}

	function trin_create_category_form (
		$action, $button_title,
		$param_category_name, $param_category_name_value,
		$param_version_name, $param_version_value,
		$validation_failed_fields
		)
	{
?>
<form action="<?php echo trin_html_escape($action); ?>" method="POST">

<p>
<span class="par_name">
<label for="<?php echo $param_category_name ?>">Category name:</label>
</span>
<span class="par_value">
<?php
		trin_create_text_input('text', '20', $param_category_name,
			$param_category_name_value, $validation_failed_fields);
?>
</span>
</p>

<p class="c">
<?php
		trin_create_text_input('hidden', '', $param_version_name,
			$param_version_value, $validation_failed_fields);
?>
<input type="submit" value="<?php echo $button_title; ?>">
<?php
	trin_create_reset ("category_form");
?>
</p>

</form>

<?php
	}

	function trin_get_current_date_string ()
	{
		return date("Y-m-d H:i:s"); // better with leading zeros
		/*$curr_time = getdate ();
		return $curr_time['year'] . '-'
			. $curr_time['mon'] . '-'
			. $curr_time['mday'] . ' '
			. $curr_time['hours'] . ':'
			. $curr_time['minutes'] . ':'
			. $curr_time['seconds'];*/
	}

	function trin_get_self_action ()
	{
		$action = trin_html_escape(trin_get_server('PHP_SELF'));
		if (trin_isset_server('QUERY_STRING') && trin_get_server('QUERY_STRING') != '')
		{
			$action .= '?' . trin_html_escape(trin_get_server('QUERY_STRING'));
		}
		return $action;
	}

	function trin_get_self_location ()
	{
		$action = trin_html_escape(trin_get_server('PHP_SELF'));
		if (trin_isset_server('QUERY_STRING') && trin_get_server('QUERY_STRING') != '')
		{
			$action .= '?' . trin_html_escape(trin_get_server('QUERY_STRING'));
		}
		return $action;
	}

	function trin_get_gender_name ($abbrev)
	{
		if ($abbrev == 'M')
		{
			return 'Male';
		}
		if ($abbrev == 'F')
		{
			return 'Female';
		}
		if ($abbrev == 'C')
		{
			return 'Child';
		}
		if ($abbrev == '-')
		{
			return 'N/A';
		}
		return '?';
	}

	function trin_validate_form ($values, $validators)
	{
		$failed_fields = array();
		foreach ($validators as $field_name => $field_type)
		{
			if (isset($values[$field_name]))
			{
				if ($field_type == TRIN_VALIDATION_FIELD_TYPE_NUMBER)
				{
					$value = str_replace (',', '.', $values[$field_name]);
					if (! is_numeric ($value))
					{
						$failed_fields[] = $field_name;
					}
				}
				if ($field_type == TRIN_VALIDATION_FIELD_TYPE_REQUIRED)
				{
					if (strlen ($values[$field_name]) == 0)
					{
						$failed_fields[] = $field_name;
					}
				}
			}
			else if ($field_type == TRIN_VALIDATION_FIELD_TYPE_REQUIRED)
			{
				$failed_fields[] = $field_name;
			}
		}
		return $failed_fields;
	}

	function trin_display_error ($message)
	{
		if ($message !== '')
		{
?>
<div class="error">
Error: <?php echo $message; ?>
</div>
<?php
		}
	}

	function trin_set_success_msg ($message)
	{
		trin_set_sess(TRIN_SESS_LAST_SUCCESS, $message);
	}

	function trin_display_success ($message = '')
	{
		$msg = '';
		if ($message !== '')
		{
			$msg = $message;
		}
		if (trin_isset_sess(TRIN_SESS_LAST_SUCCESS)
			&& trin_get_sess(TRIN_SESS_LAST_SUCCESS) != '')
		{
			$msg = trin_get_sess(TRIN_SESS_LAST_SUCCESS);
		}
		if ($msg != '')
		{
?>
<div class="success">
<?php echo $msg; ?>
</div>
<?php
		}
		trin_unset_sess(TRIN_SESS_LAST_SUCCESS);
	}

	function trin_html_escape ($string)
	{
		return htmlspecialchars ($string, ENT_HTML401 | ENT_QUOTES);
	}
?>
