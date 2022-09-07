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

	define ('TRIN_QUERY_DB_CHECK', 'select now()');
	define ('TRIN_QUERY_DB_VERSION_CHECK',
		'select db_version from trinventum.versions');

	define ('TRIN_QUERY_GET_PRODUCT_DEFS',
		'select pd_id, pd_photo, pd_name, pd_size, pd_length, pd_width, pd_gender,
		pd_colour, pd_comment, pd_brand
		from trinventum.product_definitions order by pd_id asc');
	define ('TRIN_QUERY_GET_PRODUCT_PHOTO',
		'select pd_photo from trinventum.product_definitions where pd_id = $1');
	define ('TRIN_QUERY_GET_PRODUCT_DET',
		'select pd_id, pd_photo, pd_name, pd_size, pd_length, pd_width, pd_gender,
		pd_colour, pd_comment, pd_brand
		from trinventum.product_definitions where pd_id = $1');
	define ('TRIN_QUERY_GET_PRODUCT_COUNTS',
		'select p_status::text, count(*) from trinventum.products where p_pd_id = $1 group by p_status
		union all select \'' . TRIN_PROD_COUNT_COLUMN_TOTAL . '\' as p_status,
		count(*) from trinventum.products where p_pd_id = $1 order by p_status;');
	define ('TRIN_QUERY_GET_PRODUCT_SALES',
		'select t_buyer, count(*) from trinventum.transactions where t_product_id in
		(select p_id from trinventum.products where p_pd_id = $1) group by t_buyer');

	define ('TRIN_QUERY_GET_PRODUCT_NEXT_ID',
		"select nextval('trinventum.seq_pd_id')");
	define ('TRIN_QUERY_ADD_PRODUCT_DEF',
		"insert into trinventum.product_definitions (pd_id, pd_photo, pd_name, pd_size,
		pd_length, pd_width, pd_gender, pd_colour, pd_comment, pd_brand)
		values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)");
	define ('TRIN_QUERY_ADD_PRODUCT_INSTANCE',
		"insert into trinventum.products (p_pd_id, p_status, p_cost)
		values ($1, $2, $3)");

	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF',
		"update trinventum.product_definitions set pd_photo = $2, pd_name = $3,
		pd_size = $4, pd_length = $5, pd_width = $6, pd_gender = $7,
		pd_colour = $8, pd_comment = $9, pd_brand = $10
		where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_INSTANCE',
		"update trinventum.products set p_status = $2, p_cost = $3 where p_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_INSTANCE_COST',
		"update trinventum.products set p_cost = $2 where p_pd_id = $1");
	define ('TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS',
		"update trinventum.products set p_status = $2 where p_id = $1");
	define ('TRIN_QUERY_GET_PRODUCT_INSTANCES',
		'select p_id, p_status, p_cost
		from trinventum.products where p_pd_id = $1 order by p_id asc');
	define ('TRIN_QUERY_GET_PRODUCT_INSTANCES_WITH_STATUS',
		'select p_id, p_status, p_cost
		from trinventum.products where p_pd_id = $1 and p_status = $2 order by p_id asc');
	define ('TRIN_QUERY_GET_PRODUCT_INSTANCE_DET',
		'select p_id, p_status, p_cost
		from trinventum.products where p_id = $1');

	define ('TRIN_QUERY_GET_SELLERS',
		'select s_id, s_name from trinventum.sellers order by s_id asc');
	define ('TRIN_QUERY_ADD_SELLER',
		'insert into trinventum.sellers (s_name) values ($1)');
	define ('TRIN_QUERY_GET_SELLER_DET',
		'select s_id, s_name from trinventum.sellers where s_id = $1');
	define ('TRIN_QUERY_UPDATE_SELLER',
		"update trinventum.sellers set s_name = $2 where s_id = $1");

	define ('TRIN_QUERY_GET_BUYERS',
		'select b_id, b_name, b_postal_address, b_login, b_email_address, b_comment
		from trinventum.buyers order by b_id asc');
	define ('TRIN_QUERY_ADD_BUYER',
		'insert into trinventum.buyers (b_name, b_postal_address, b_login,
		b_email_address, b_comment) values ($1, $2, $3, $4, $5)');
	define ('TRIN_QUERY_GET_BUYER_DET',
		'select b_id, b_name, b_postal_address, b_login, b_email_address, b_comment
		from trinventum.buyers where b_id = $1');
	define ('TRIN_QUERY_UPDATE_BUYER',
		"update trinventum.buyers set b_name = $2, b_postal_address = $3,
		b_login = $4, b_email_address = $5, b_comment = $6 where b_id = $1");

	define ('TRIN_QUERY_GET_TRANSACTIONS',
		'select t.t_id, p.p_pd_id, t.t_product_id, t.t_seller, t.t_buyer, t.t_price, t.t_paid,
		t.t_sent, t.t_sell_date, t.t_send_price, t.t_send_cost
		from trinventum.transactions t join trinventum.products p on p.p_id = t.t_product_id
		order by t_id asc');
	define ('TRIN_QUERY_ADD_TRANSACTION',
		'insert into trinventum.transactions (t_product_id, t_seller, t_buyer,
		t_price, t_paid, t_sent, t_sell_date, t_send_price, t_send_cost)
		values ($1, $2, $3, $4, $5, $6, $7, $8, $9)');
	define ('TRIN_QUERY_UPDATE_TRANSACTION',
		'update trinventum.transactions set t_product_id = $2, t_seller = $3,
		t_buyer = $4, t_price = $5, t_paid = $6, t_sent = $7, t_sell_date = $8,
		t_send_price = $9, t_send_cost = $10 where t_id = $1');
	define ('TRIN_QUERY_GET_TRANSACTION_DET',
		'select t_id, t_product_id, t_seller, t_buyer, t_price, t_paid,
		t_sent, t_sell_date, t_send_price, t_send_cost
		from trinventum.transactions where t_id = $1');

	// ===============================================================

	function trin_db_open ($login, $pass, $dbname, $host)
	{
		return pg_connect ("host=$host dbname=$dbname user=$login password=$pass");
	}

	function trin_db_close ($conn)
	{
		pg_close ($conn);
	}

	function trin_db_check ($conn)
	{
		$result = pg_query ($conn, TRIN_QUERY_DB_CHECK);
		return ($result !== FALSE);
	}

	function trin_db_get_version ($conn)
	{
		$result = pg_query ($conn, TRIN_QUERY_DB_VERSION_CHECK);
		if ($result !== FALSE)
		{
			$row = pg_fetch_row ($result);
			if ($row !== FALSE)
			{
				return (int)$row[0];
			}
		}
		return 0;
	}

	function trin_db_get_product_defs ($conn)
	{
		return pg_query ($conn, TRIN_QUERY_GET_PRODUCT_DEFS);
	}

	function trin_create_photo_img_tag($prod_id)
	{
		return '<img src="get_photo.php?' .
			TRIN_PROD_PHOTO_PARAM . '=' . $prod_id . '" alt="-"
			class="prod_image">';
	}

	function trin_db_get_next_product ($conn, $products)
	{
		$result = array ();
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COUNT] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = '';

		$product = pg_fetch_row ($products);
		if ($product !== FALSE)
		{
			$pd_id = $product[0];
			$result[TRIN_DB_PROD_DEF_FIELD_ID] = $pd_id;
			if (//pg_field_is_null ($products, 'pd_photo') == 0
				$product[1] !== NULL)
			{
				$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] =
					trin_create_photo_img_tag ($pd_id);
			}
			else
			{
				$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '-';
			}
			$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $product[2];
			$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = $product[3];
			if ((float)$product[4] > 0.0) // pd_length
			{
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE] .= "<br>Length: $product[4]";
			}
			if ((float)$product[5] > 0.0) // pd_width
			{
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE] .= "<br>Width: $product[5]";
			}
			$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = $product[6];
			$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = $product[7];
			$product_count_res = trin_db_count_products ($conn, $pd_id);
			$count_html = '';
			foreach ($product_count_res as $status => $count)
			{
				$count_html .= "$status: $count<br>";
			}
			$result[TRIN_DB_PROD_DEF_FIELD_COUNT] = $count_html;
			$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = $product[8];
			$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = $product[9];
			return $result;
		}
		return FALSE;
	}

	function trin_db_get_photo ($db, $id)
	{
		$photo_result = pg_query_params ($db, TRIN_QUERY_GET_PRODUCT_PHOTO, array($id));
		if ($photo_result !== FALSE)
		{
			if (! pg_field_is_null ($photo_result, 0, 'pd_photo'))
			{
				$photo_data = pg_fetch_row ($photo_result);
				if ($photo_data !== FALSE)
				{
					// contrary to what the manual says, it seems
					// that the database value not only has to be
					// un-escaped TWICE, but it also still has the
					// single quotes doubled (i.e., escaped), so they
					// must be taken care of, too. This may be the
					// effect of using parameterized statements
// 					return 	pg_unescape_bytea
// 						 (pg_unescape_bytea
// 						  (str_replace ("''", "'", $photo_data[0])));
					//return base64_decode($photo_data[0]);
					return base64_decode(pg_unescape_bytea($photo_data[0]));
				}
			}
		}
		return '';
	}

	function trin_db_add_product ($db, $param_pd_name, $param_pd_photo,
		$param_pd_size, $param_pd_length, $param_pd_width,
		$param_pd_colour, $param_pd_count, $param_pd_brand,
		$param_pd_gender, $param_pd_comment, $param_pd_cost)
	{
		if (is_uploaded_file ($_FILES[$param_pd_photo]['tmp_name']))
		{
// 			$photo_data = pg_escape_bytea (
// 				file_get_contents ($_FILES[$param_pd_photo]['tmp_name']));
			$photo_data = base64_encode (
				file_get_contents ($_FILES[$param_pd_photo]['tmp_name']));
		}
		else
		{
			$photo_data = NULL;
		}

		$param_pd_length = str_replace (',', '.', $param_pd_length);
		$param_pd_width = str_replace (',', '.', $param_pd_width);
		$param_pd_cost = str_replace (',', '.', $param_pd_cost);

		$nextseq_res = pg_query ($db, TRIN_QUERY_GET_PRODUCT_NEXT_ID);
		if ($nextseq_res === FALSE)
		{
			return FALSE;
		}
		$nextseq_val = pg_fetch_row ($nextseq_res);
		if ($nextseq_val === FALSE)
		{
			return FALSE;
		}
		$pd_id = $nextseq_val[0];

		$res = pg_query ($db, 'begin');
		if ($res === FALSE)
		{
			return FALSE;
		}

		// add the product definition:
		$result = pg_query_params ($db, TRIN_QUERY_ADD_PRODUCT_DEF,
			array ($pd_id, $photo_data, $param_pd_name, $param_pd_size,
			$param_pd_length, $param_pd_width, $param_pd_gender,
			$param_pd_colour, $param_pd_comment, $param_pd_brand));

		// add entries in trinventum.products (product instances):
		if ($result !== FALSE)
		{
			$success = TRUE;

			for ($i = 0; $i < (int)$param_pd_count; $i++)
			{
				$result = pg_query_params ($db, TRIN_QUERY_ADD_PRODUCT_INSTANCE,
					array ($pd_id, TRIN_PROD_STATUS_READY, $param_pd_cost));
				if ($result === FALSE)
				{
					$success = FALSE;
					break;
				}
			}
			if ($success)
			{
				pg_query ($db, 'commit');
				return TRUE;
			}
			else
			{
				//pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		else
		{
			//pg_query ($db, 'rollback');
			return FALSE;
		}
	}

	function trin_db_get_product_details ($db, $id)
	{
		$result = array ();
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COUNT] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = '';

		$product_res = pg_query_params ($db,
			TRIN_QUERY_GET_PRODUCT_DET, array ($id));

		if ($product_res !== FALSE)
		{
			$product = pg_fetch_row ($product_res);
			if ($product !== FALSE)
			{
				$pd_id = $product[0];
				$result[TRIN_DB_PROD_DEF_FIELD_ID] = $pd_id;
				if (//pg_field_is_null ($product_res, 'pd_photo') == 0
					$product[1] !== NULL)
				{
					$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] =
						trin_create_photo_img_tag ($pd_id);
				}
				else
				{
					$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '-';
				}
				$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $product[2];
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = $product[3];
				$result[TRIN_DB_PROD_DEF_FIELD_LENGTH] = $product[4];
				$result[TRIN_DB_PROD_DEF_FIELD_WIDTH] = $product[5];
				$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = $product[6];
				$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = $product[7];
				$product_count_res = trin_db_count_products ($db, $pd_id);
				$count_html = '';
				foreach ($product_count_res as $status => $count)
				{
					$count_html .= "$status: $count<br>";
				}

				$result[TRIN_DB_PROD_DEF_FIELD_COUNT] = $count_html;
				$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = $product[8];
				$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = $product[9];
				return $result;
			}
		}
		return FALSE;
	}

	function trin_db_update_product ($db, $pd_id, $param_pd_name, $param_pd_photo,
		$param_pd_size, $param_pd_length, $param_pd_width,
		$param_pd_colour, $param_pd_count, $param_pd_brand,
		$param_pd_gender, $param_pd_comment, $param_pd_cost)
	{
		if (is_uploaded_file ($_FILES[$param_pd_photo]['tmp_name']))
		{
// 			$photo_data = pg_escape_bytea (
// 				file_get_contents ($_FILES[$param_pd_photo]['tmp_name']));
			$photo_data = base64_encode (
				file_get_contents ($_FILES[$param_pd_photo]['tmp_name']));
		}
		else
		{
			$photo_data = NULL;
		}

		$param_pd_length = str_replace (',', '.', $param_pd_length);
		$param_pd_width = str_replace (',', '.', $param_pd_width);
		$param_pd_cost = str_replace (',', '.', $param_pd_cost);

		$res = pg_query ($db, 'begin');
		if ($res === FALSE)
		{
			return FALSE;
		}

		// get the current product instance count:
		$old_pd_count = 0;
		$product_count_res = trin_db_count_products ($db, $pd_id);
		foreach ($product_count_res as $status => $count)
		{
			if ($status === TRIN_PROD_COUNT_COLUMN_TOTAL)
			{
				$old_pd_count = (int)$count;
			}
		}
		if ($old_pd_count > (int)$param_pd_count)
		{
			$param_pd_count = $old_pd_count;
		}
		// update the product definition:
		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF,
			array ($pd_id, $photo_data, $param_pd_name, $param_pd_size,
			$param_pd_length, $param_pd_width, $param_pd_gender,
			$param_pd_colour, $param_pd_comment, $param_pd_brand));

		// update entries in trinventum.products (product instances):
		if ($result !== FALSE)
		{
			$success = TRUE;

			for ($i = 0; $i < (int)$param_pd_count; $i++)
			{
				$result = pg_query_params ($db,
					TRIN_QUERY_UPDATE_PRODUCT_INSTANCE_COST,
					array ($pd_id, $param_pd_cost));
				if ($result === FALSE)
				{
					$success = FALSE;
					break;
				}
			}
			if ($old_pd_count < (int)$param_pd_count)
			{
				// new product instances to be added:
				for ($i = 0; $i < (int)$param_pd_count - $old_pd_count; $i++)
				{
					$result = pg_query_params ($db,
						TRIN_QUERY_ADD_PRODUCT_INSTANCE,
						array ($pd_id, TRIN_PROD_STATUS_READY,
							$param_pd_cost));
					if ($result === FALSE)
					{
						$success = FALSE;
						break;
					}
				}
			}
			if ($success)
			{
				pg_query ($db, 'commit');
				return TRUE;
			}
			else
			{
				//pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		else
		{
			//pg_query ($db, 'rollback');
			return FALSE;
		}
	}

	function trin_db_count_products ($conn, $id)
	{
		$result = array ();
		$count_result = pg_query_params ($conn,
			TRIN_QUERY_GET_PRODUCT_COUNTS, array ($id));
		if ($count_result !== FALSE)
		{
			while (TRUE)
			{
				$row = pg_fetch_row ($count_result);
				if ($row === FALSE)
				{
					break;
				}
				$result[$row[0]] = $row[1];
			}
		}
		return $result;
	}

	function trin_db_get_product_instances ($conn, $pd_id)
	{
		return pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_INSTANCES,
			array ($pd_id));
	}

	function trin_db_get_product_instances_with_status ($conn, $pd_id, $pd_status)
	{
		return pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_INSTANCES_WITH_STATUS,
			array ($pd_id, $pd_status));
	}

	function trin_db_get_next_product_instance ($product_insts)
	{
		$result = array ();
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_STATUS] = '';
		$result[TRIN_DB_PROD_INST_FIELD_COST] = '';

		$product = pg_fetch_row ($product_insts);
		if ($product !== FALSE)
		{
			$result[TRIN_DB_PROD_INST_FIELD_ID] = $product[0];
			$result[TRIN_DB_PROD_INST_FIELD_STATUS] = $product[1];
			$result[TRIN_DB_PROD_INST_FIELD_COST] = $product[2];

			return $result;
		}
		return FALSE;
	}

	function trin_db_get_product_instance_details ($db, $p_id)
	{
		$result = array ();
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_STATUS] = '';
		$result[TRIN_DB_PROD_INST_FIELD_COST] = '';

		$pinst_res = pg_query_params ($db,
			TRIN_QUERY_GET_PRODUCT_INSTANCE_DET, array ($p_id));

		if ($pinst_res !== FALSE)
		{
			$p_inst = pg_fetch_row ($pinst_res);
			if ($p_inst !== FALSE)
			{
				$result[TRIN_DB_PROD_INST_FIELD_ID] = $p_inst[0];
				$result[TRIN_DB_PROD_INST_FIELD_STATUS] = $p_inst[1];
				$result[TRIN_DB_PROD_INST_FIELD_COST] = $p_inst[2];
				return $result;
			}
		}
		return FALSE;
	}

	function trin_db_update_product_instance ($db, $p_id, $status, $cost)
	{
		$cost = str_replace (',', '.', $cost);
		return pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_INSTANCE,
			array ($p_id, $status, $cost));
	}


	function trin_db_get_sellers ($conn)
	{
		return pg_query ($conn, TRIN_QUERY_GET_SELLERS);
	}

	function trin_db_get_next_seller ($sellers)
	{
		$result = array ();
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';

		$seller = pg_fetch_row ($sellers);
		if ($seller !== FALSE)
		{
			$result[TRIN_DB_SELLER_PARAM_ID] = $seller[0];
			$result[TRIN_DB_SELLER_PARAM_NAME] = $seller[1];

			return $result;
		}
		return FALSE;
	}

	function trin_db_add_seller ($db, $name)
	{
		return pg_query_params ($db, TRIN_QUERY_ADD_SELLER, array ($name));
	}

	function trin_db_get_seller_details ($db, $id)
	{
		$result = array ();
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';

		$seller_res = pg_query_params ($db,
			TRIN_QUERY_GET_SELLER_DET, array ($id));

		if ($seller_res !== FALSE)
		{
			$seller = pg_fetch_row ($seller_res);
			if ($seller !== FALSE)
			{
				$result[TRIN_DB_SELLER_PARAM_ID] = $seller[0];
				$result[TRIN_DB_SELLER_PARAM_NAME] = $seller[1];
				return $result;
			}
		}
		return FALSE;
	}

	function trin_db_update_seller ($db, $s_id, $name)
	{
		return pg_query_params ($db, TRIN_QUERY_UPDATE_SELLER,
			array ($s_id, $name));
	}

	function trin_db_get_buyers ($conn)
	{
		return pg_query ($conn, TRIN_QUERY_GET_BUYERS);
	}

	function trin_db_get_next_buyer ($buyers)
	{
		$result = array ();
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ADDRESS] = '';
		$result[TRIN_DB_BUYER_PARAM_LOGIN] = '';
		$result[TRIN_DB_BUYER_PARAM_EMAIL] = '';
		$result[TRIN_DB_BUYER_PARAM_COMMENT] = '';

		$buyer = pg_fetch_row ($buyers);
		if ($buyer !== FALSE)
		{
			$result[TRIN_DB_BUYER_PARAM_ID] = $buyer[0];
			$result[TRIN_DB_BUYER_PARAM_NAME] = $buyer[1];
			$result[TRIN_DB_BUYER_PARAM_ADDRESS] = $buyer[2];
			$result[TRIN_DB_BUYER_PARAM_LOGIN] = $buyer[3];
			$result[TRIN_DB_BUYER_PARAM_EMAIL] = $buyer[4];
			$result[TRIN_DB_BUYER_PARAM_COMMENT] = $buyer[5];

			return $result;
		}
		return FALSE;
	}

	function trin_db_add_buyer ($db, $name, $address, $login, $email, $comment)
	{
		return pg_query_params ($db, TRIN_QUERY_ADD_BUYER,
			array ($name, $address, $login, $email, $comment));
	}

	function trin_db_get_buyer_details ($db, $id)
	{
		$result = array ();
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ADDRESS] = '';
		$result[TRIN_DB_BUYER_PARAM_LOGIN] = '';
		$result[TRIN_DB_BUYER_PARAM_EMAIL] = '';
		$result[TRIN_DB_BUYER_PARAM_COMMENT] = '';

		$buyer_res = pg_query_params ($db,
			TRIN_QUERY_GET_BUYER_DET, array ($id));

		if ($buyer_res !== FALSE)
		{
			$buyer = pg_fetch_row ($buyer_res);
			if ($buyer !== FALSE)
			{
				$result[TRIN_DB_BUYER_PARAM_ID] = $buyer[0];
				$result[TRIN_DB_BUYER_PARAM_NAME] = $buyer[1];
				$result[TRIN_DB_BUYER_PARAM_ADDRESS] = $buyer[2];
				$result[TRIN_DB_BUYER_PARAM_LOGIN] = $buyer[3];
				$result[TRIN_DB_BUYER_PARAM_EMAIL] = $buyer[4];
				$result[TRIN_DB_BUYER_PARAM_COMMENT] = $buyer[5];
				return $result;
			}
		}
		return FALSE;
	}

	function trin_db_update_buyer ($db, $b_id, $name, $address, $login, $email, $comment)
	{
		return pg_query_params ($db, TRIN_QUERY_UPDATE_BUYER,
			array ($b_id, $name, $address, $login, $email, $comment));
	}

	function trin_db_get_transactions ($conn)
	{
		return pg_query ($conn, TRIN_QUERY_GET_TRANSACTIONS);
	}

	function trin_db_get_next_transaction ($trans)
	{
		$result = array ();
		$result[TRIN_DB_TRANS_PARAM_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_TRANS_PARAM_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_PAID] = '';
		$result[TRIN_DB_TRANS_PARAM_SENT] = '';
		$result[TRIN_DB_TRANS_PARAM_SELLDATE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_COST] = '';

		$tran = pg_fetch_row ($trans);
		if ($tran !== FALSE)
		{
			$result[TRIN_DB_TRANS_PARAM_ID] = $tran[0];
			$result[TRIN_DB_PROD_DEF_FIELD_ID] = $tran[1];
			$result[TRIN_DB_PROD_INST_FIELD_ID] = $tran[2];
			$result[TRIN_DB_SELLER_PARAM_ID] = $tran[3];
			$result[TRIN_DB_BUYER_PARAM_ID] = $tran[4];
			$result[TRIN_DB_TRANS_PARAM_PRICE] = $tran[5];
			$result[TRIN_DB_TRANS_PARAM_PAID] = $tran[6];
			$result[TRIN_DB_TRANS_PARAM_SENT] = $tran[7];
			$result[TRIN_DB_TRANS_PARAM_SELLDATE] = $tran[8];
			$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = $tran[9];
			$result[TRIN_DB_TRANS_PARAM_SEND_COST] = $tran[10];

			return $result;
		}
		return FALSE;
	}

	function trin_db_add_transaction ($db, $t_product_id, $t_seller, $t_buyer,
		$t_price, $t_paid, $t_sent, $t_sell_date, $t_send_price, $t_send_cost)
	{
		$res = pg_query ($db, 'begin');

		if ($res !== FALSE)
		{
			$t_price = str_replace (',', '.', $t_price);
			$t_send_price = str_replace (',', '.', $t_send_price);
			$t_send_cost = str_replace (',', '.', $t_send_cost);
			$res = pg_query_params ($db, TRIN_QUERY_ADD_TRANSACTION,
				array ($t_product_id, $t_seller, $t_buyer,
					$t_price, $t_paid, $t_sent, $t_sell_date,
					$t_send_price, $t_send_cost));
			if ($res !== FALSE)
			{
				// mark product instance as sold
				$res = pg_query_params ($db,
					TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS,
					array ($t_product_id, TRIN_PROD_STATUS_SOLD));
				if ($res !== FALSE)
				{
					pg_query ($db, 'commit');
				}
				else
				{
					//pg_query ($db, 'rollback');
					return FALSE;
				}
			}
			else
			{
				//pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		return $res;
	}

	function trin_db_get_transaction_details ($db, $id)
	{
		$result = array ();
		$result[TRIN_DB_TRANS_PARAM_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_TRANS_PARAM_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_PAID] = '';
		$result[TRIN_DB_TRANS_PARAM_SENT] = '';
		$result[TRIN_DB_TRANS_PARAM_SELLDATE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_COST] = '';

		$trans_res = pg_query_params ($db,
			TRIN_QUERY_GET_TRANSACTION_DET, array ($id));

		if ($trans_res !== FALSE)
		{
			$trans = pg_fetch_row ($trans_res);
			if ($trans !== FALSE)
			{
				$result[TRIN_DB_TRANS_PARAM_ID] = $trans[0];
				$result[TRIN_DB_PROD_INST_FIELD_ID] = $trans[1];
				$result[TRIN_DB_SELLER_PARAM_ID] = $trans[2];
				$result[TRIN_DB_BUYER_PARAM_ID] = $trans[3];
				$result[TRIN_DB_TRANS_PARAM_PRICE] = $trans[4];
				$result[TRIN_DB_TRANS_PARAM_PAID] = $trans[5];
				$result[TRIN_DB_TRANS_PARAM_SENT] = $trans[6];
				$result[TRIN_DB_TRANS_PARAM_SELLDATE] = $trans[7];
				$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = $trans[8];
				$result[TRIN_DB_TRANS_PARAM_SEND_COST] = $trans[9];
				return $result;
			}
		}
		return FALSE;
	}

	function trin_db_update_transaction ($db, $trans_id, $t_product_id,
		$t_seller, $t_buyer, $t_price, $t_paid, $t_sent, $t_sell_date,
		$t_send_price, $t_send_cost)
	{
		$res = pg_query ($db, 'begin');

		if ($res !== FALSE)
		{
			$t_price = str_replace (',', '.', $t_price);
			$t_send_price = str_replace (',', '.', $t_send_price);
			$t_send_cost = str_replace (',', '.', $t_send_cost);
			$res = pg_query_params ($db, TRIN_QUERY_UPDATE_TRANSACTION,
				array ($trans_id, $t_product_id, $t_seller,
					$t_buyer, $t_price, $t_paid, $t_sent,
					$t_sell_date, $t_send_price, $t_send_cost));
			if ($res !== FALSE)
			{
				// mark the old product instance as READY
				$trans = trin_db_get_transaction_details ($db, $trans_id);
				if ($trans !== FALSE)
				{
					$res = pg_query_params ($db,
						TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS,
						array ($trans[TRIN_DB_PROD_INST_FIELD_ID],
							TRIN_PROD_STATUS_READY));
					if ($res !== FALSE)
					{
						// mark new product instance as sold
						$res = pg_query_params ($db,
							TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS,
							array ($t_product_id, TRIN_PROD_STATUS_SOLD));
						if ($res !== FALSE)
						{
							pg_query ($db, 'commit');
						}
						else
						{
							//pg_query ($db, 'rollback');
							return FALSE;
						}
					}
					else
					{
						//pg_query ($db, 'rollback');
						return FALSE;
					}
				}
				else
				{
					//pg_query ($db, 'rollback');
					return FALSE;
				}
			}
			else
			{
				//pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		return $res;
	}

	function trin_db_get_product_sales ($conn, $pd_id)
	{
		return pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_SALES,
			array ($pd_id));
	}

	function trin_db_get_next_product_sale ($sales)
	{
		$result = array ();
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_TRANS_PARAM_COUNT] = '';

		$sale = pg_fetch_row ($sales);
		if ($sale !== FALSE)
		{
			$result[TRIN_DB_BUYER_PARAM_ID] = $sale[0];
			$result[TRIN_DB_TRANS_PARAM_COUNT] = $sale[1];

			return $result;
		}
		return FALSE;
	}

?>
