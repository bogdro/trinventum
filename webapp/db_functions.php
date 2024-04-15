<?php
	/*
	 * Trinventum - database functions.
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

	define ('TRIN_QUERY_DB_CHECK', 'select now()');
	define ('TRIN_QUERY_DB_VERSION_CHECK',
		'select db_version from trinventum.versions');

	define ('TRIN_QUERY_GET_PRODUCT_CATEGORIES',
		'select pc_id, pc_name
		from trinventum.product_categories order by pc_id desc');
	define ('TRIN_QUERY_GET_PRODUCT_CATEGORY_HIST_BY_ID',
		'select his_pc_id, his_pc_name, his_pc_version, his_pc_user, his_pc_record_timestamp
		from trinventum.product_categories_hist where his_pc_id = $1
		order by his_pc_version desc');
	define ('TRIN_QUERY_GET_PRODUCT_CATEGORY_DET',
		'select pc_id, pc_name
		from trinventum.product_categories where pc_id = $1');
	define ('TRIN_QUERY_ADD_PRODUCT_CATEGORY',
		'insert into trinventum.product_categories (pc_name) values ($1)');
	define ('TRIN_QUERY_UPDATE_PRODUCT_CATEGORY',
		"update trinventum.product_categories set pc_name = $2,
		pc_version = pc_version+1 where pc_id = $1");

	define ('TRIN_QUERY_GET_PRODUCT_DEFS',
		'select pd_id, pd_photo, pd_name, pd_size, pd_length, pd_width, pd_gender,
		pd_colour, pd_comment, pd_brand, pc_name
		from trinventum.product_definitions
		left join trinventum.product_categories on pc_id = pd_pc_id
		order by pd_id desc');
	define ('TRIN_QUERY_GET_PRODUCT_PHOTO',
		'select pd_photo from trinventum.product_definitions where pd_id = $1');
	define ('TRIN_QUERY_GET_PRODUCT_DET',
		'select pd_id, pd_photo, pd_name, pd_size, pd_length, pd_width, pd_gender,
		pd_colour, pd_comment, pd_brand, pd_version,
		coalesce (pc_name, \'?\') as pc_name,
		coalesce (pc_id, -1) as pc_id
		from trinventum.product_definitions
		left join trinventum.product_categories on pc_id = pd_pc_id
		where pd_id = $1');
	define ('TRIN_QUERY_GET_PRODUCT_COUNTS',
		'select p_status::text as p_status, count(*) as p_count from trinventum.products
		where p_pd_id = $1 group by p_status
		union all select \'' . TRIN_PROD_COUNT_COLUMN_TOTAL . '\' as p_status,
		count(*) as p_count from trinventum.products where p_pd_id = $1 order by p_status');
	define ('TRIN_QUERY_GET_ALL_PRODUCT_COUNTS',
		'select p_status as p_status, count(*) as p_count from trinventum.products
		group by p_status');
	// had to use a dedicated function - PHP driver didn't accept correct SQL statements
	define ('TRIN_QUERY_GET_MONTH_HIST_PRODUCT_COUNTS',
		'select his_p_status as p_status, count(*) as p_count from trinventum.products_hist
		where extract (month from his_p_record_timestamp)
		= trinventum.months_ago($1)
		and extract (year from his_p_record_timestamp)
		= trinventum.year_months_ago($1)
		group by p_status');
//		= extract (month from (current_date - interval \'$1 months\'))
	define ('TRIN_QUERY_GET_PRODUCT_DEFS_OF_CATEGORY',
		'select pd_id, pd_photo, pd_name, pd_size, pd_length, pd_width, pd_gender,
		pd_colour, pd_comment, pd_brand, pc_name
		from trinventum.product_definitions
		left join trinventum.product_categories on pc_id = pd_pc_id
		where pd_pc_id = $1 order by pd_id desc');

	define ('TRIN_QUERY_GET_PRODUCT_BUYS',
		'select b_id, b_name, count(*) as b_count from trinventum.transactions
		join trinventum.buyers on b_id = t_buyer where t_product_id in
		(select p_id from trinventum.products where p_pd_id = $1)
		group by b_id, b_name order by count(*) desc');
	define ('TRIN_QUERY_GET_PRODUCT_SELLINGS',
		'select s_id, s_name, count(*) as s_count from trinventum.transactions
		join trinventum.sellers on s_id = t_seller where t_product_id in
		(select p_id from trinventum.products where p_pd_id = $1)
		group by s_id, s_name order by count(*) desc');
	define ('TRIN_QUERY_GET_PRODUCT_HIST_BY_ID',
		'select his_pd_id, his_pd_photo, his_pd_name, his_pd_size,
		his_pd_length, his_pd_width, his_pd_gender, his_pd_colour,
		his_pd_comment, his_pd_brand, his_pd_version, his_pd_user,
		his_pd_record_timestamp,
		coalesce (pc_name, \'?\') as pc_name
		from trinventum.product_definitions_hist
		left join trinventum.product_categories on pc_id = his_pd_pc_id
		where his_pd_id = $1
		order by his_pd_version desc');
	define ('TRIN_QUERY_GET_PRODUCT_HIST_PHOTO',
		'select his_pd_photo from trinventum.product_definitions_hist
		where his_pd_id = $1 and his_pd_version = $2');

	define ('TRIN_QUERY_GET_PRODUCT_NEXT_ID',
		"select nextval('trinventum.seq_pd_id')");
	define ('TRIN_QUERY_ADD_PRODUCT_DEF',
		"insert into trinventum.product_definitions (pd_id, pd_photo, pd_name, pd_size,
		pd_length, pd_width, pd_gender, pd_colour, pd_comment, pd_brand, pd_pc_id)
		values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)");
	define ('TRIN_QUERY_ADD_PRODUCT_INSTANCE',
		"insert into trinventum.products (p_pd_id, p_status, p_cost)
		values ($1, $2, $3)");

	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF',
		"update trinventum.product_definitions set pd_photo = $2, pd_name = $3,
		pd_size = $4, pd_length = $5, pd_width = $6, pd_gender = $7,
		pd_colour = $8, pd_comment = $9, pd_brand = $10, pd_version = pd_version+1
		where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_NAME',
		"update trinventum.product_definitions set pd_name = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_PHOTO',
		"update trinventum.product_definitions set pd_photo = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_SIZE',
		"update trinventum.product_definitions set pd_size = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_LENGTH',
		"update trinventum.product_definitions set pd_length = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_WIDTH',
		"update trinventum.product_definitions set pd_width = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_COLOUR',
		"update trinventum.product_definitions set pd_colour = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_BRAND',
		"update trinventum.product_definitions set pd_brand = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_GENDER',
		"update trinventum.product_definitions set pd_gender = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_COMMENT',
		"update trinventum.product_definitions set pd_comment = $2,
		pd_version = pd_version+1 where pd_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_DEF_CATEGORY',
		"update trinventum.product_definitions set pd_pc_id = $2,
		pd_version = pd_version+1 where pd_id = $1");

	define ('TRIN_QUERY_UPDATE_PRODUCT_INSTANCE',
		"update trinventum.products set p_status = $2, p_cost = $3,
		p_version = p_version+1 where p_id = $1");
	define ('TRIN_QUERY_UPDATE_PRODUCT_INSTANCE_COST',
		"update trinventum.products set p_cost = $2,
		p_version = p_version+1 where p_pd_id = $1");
	define ('TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS',
		"update trinventum.products set p_status = $2,
		p_version = p_version+1 where p_id = $1");
	define ('TRIN_QUERY_GET_PRODUCT_INSTANCES',
		'select p_id, p_status, p_cost
		from trinventum.products where p_pd_id = $1 order by p_id asc');
	define ('TRIN_QUERY_GET_ALL_PRODUCT_INSTANCES',
		'select p_id, p_pd_id, pd_name, p_status, p_cost
		from trinventum.products
		join trinventum.product_definitions on pd_id = p_pd_id
		order by p_id desc offset $1 limit $2');
	define ('TRIN_QUERY_GET_PRODUCT_INSTANCES_WITH_STATUS',
		'select p_id, p_status, p_cost
		from trinventum.products where p_pd_id = $1 and p_status = $2 order by p_id asc');
	define ('TRIN_QUERY_GET_PRODUCT_INSTANCE_DET',
		'select p_id, p_status, p_cost, p_version
		from trinventum.products where p_id = $1');
	define ('TRIN_QUERY_GET_PRODUCT_INSTANCE_HIST_BY_ID',
		'select his_p_status, his_p_cost, his_p_user, his_p_record_timestamp
		from trinventum.products_hist where his_p_id = $1 order by his_p_version desc');

	define ('TRIN_QUERY_GET_SELLERS',
		'select s_id, s_name from trinventum.sellers order by s_id asc');
	define ('TRIN_QUERY_ADD_SELLER',
		'insert into trinventum.sellers (s_name) values ($1)');
	define ('TRIN_QUERY_GET_SELLER_DET',
		'select s_id, s_name, s_version from trinventum.sellers where s_id = $1');
	define ('TRIN_QUERY_UPDATE_SELLER',
		"update trinventum.sellers set s_name = $2,
		s_version = s_version+1 where s_id = $1");
	define ('TRIN_QUERY_GET_SELLER_TRANSACTIONS',
		'select pd_id, pd_name, s_id, s_name, count(*) as st_count from trinventum.transactions
		join trinventum.product_definitions on pd_id =
		(select p_pd_id from trinventum.products where p_id = t_product_id)
		join trinventum.sellers on s_id = t_seller
		group by pd_id, pd_name, s_id, s_name order by count(*) desc');
	define ('TRIN_QUERY_GET_SELLER_HIST_BY_ID',
		'select his_s_name, his_s_user, his_s_record_timestamp from trinventum.sellers_hist
		where his_s_id = $1 order by his_s_version desc');

	define ('TRIN_QUERY_GET_BUYERS',
		'select b_id, b_name, b_postal_address, b_login, b_email_address, b_comment
		from trinventum.buyers order by b_id asc');
	define ('TRIN_QUERY_ADD_BUYER',
		'insert into trinventum.buyers (b_name, b_postal_address, b_login,
		b_email_address, b_comment) values ($1, $2, $3, $4, $5)');
	define ('TRIN_QUERY_GET_BUYER_DET',
		'select b_id, b_name, b_postal_address, b_login,
		b_email_address, b_comment, b_version
		from trinventum.buyers where b_id = $1');
	define ('TRIN_QUERY_UPDATE_BUYER',
		"update trinventum.buyers set b_name = $2, b_postal_address = $3,
		b_login = $4, b_email_address = $5, b_comment = $6,
		b_version = b_version+1  where b_id = $1");
	define ('TRIN_QUERY_GET_BUYER_TRANSACTIONS',
		'select pd_id, pd_name, b_id, b_name, count(*) as bt_count from trinventum.transactions
		join trinventum.product_definitions on pd_id =
		(select p_pd_id from trinventum.products where p_id = t_product_id)
		join trinventum.buyers on b_id = t_buyer
		group by pd_id, pd_name, b_id, b_name order by count(*) desc');
	define ('TRIN_QUERY_GET_BUYER_HIST_BY_ID',
		'select his_b_name, his_b_postal_address, his_b_login,
		his_b_email_address, his_b_comment, his_b_user, his_b_record_timestamp
		from trinventum.buyers_hist where his_b_id = $1 order by his_b_version desc');

	define ('TRIN_QUERY_GET_TRANSACTIONS',
		'select t.t_id, p.p_pd_id, t.t_product_id, pd.pd_name, t.t_seller, s.s_name,
		t.t_buyer, b.b_name, t.t_price, t.t_paid,
		t.t_sent, t.t_sell_date, t.t_send_price, t.t_send_cost
		from trinventum.transactions t
		join trinventum.products p on p.p_id = t.t_product_id
		join trinventum.product_definitions pd on pd.pd_id = p.p_pd_id
		join trinventum.buyers b on b.b_id  = t.t_buyer
		join trinventum.sellers s on s.s_id  = t.t_seller
		order by t.t_id desc offset $1 limit $2');
	define ('TRIN_QUERY_GET_DELETED_TRANSACTIONS',
		'select t.his_t_id as t_id,
		coalesce (p.p_pd_id, ph.his_p_pd_id, -1) as p_pd_id,
		t.his_t_product_id as t_product_id,
		coalesce (pd.pd_name, pdh.his_pd_name, \'?\') as pd_name,
		t.his_t_seller as t_seller,
		coalesce (s.s_name, sh.his_s_name, \'?\') as s_name,
		t.his_t_buyer as t_buyer,
		coalesce (b.b_name, bh.his_b_name, \'?\') as b_name,
		t.his_t_price as t_price,
		t.his_t_paid as t_paid,
		t.his_t_sent as t_sent,
		t.his_t_sell_date as t_sell_date,
		t.his_t_send_price as t_send_price,
		t.his_t_send_cost as t_send_cost,
		t.his_t_version as t_version,
		t.his_t_user as t_user,
		t.his_t_record_timestamp as t_record_timestamp
		from trinventum.transactions_hist t
		left join trinventum.products p on p.p_id = t.his_t_product_id
		left join trinventum.products_hist ph on ph.his_p_id = t.his_t_product_id
		 and ph.his_p_version = (select max(his_p_version) from trinventum.products_hist where his_p_id = t.his_t_product_id)
		left join trinventum.product_definitions pd on pd.pd_id = p.p_pd_id
		left join trinventum.product_definitions_hist pdh on pdh.his_pd_id = ph.his_p_pd_id
		 and pdh.his_pd_version = (select max(his_pd_version) from trinventum.product_definitions_hist
		 where his_pd_id = ph.his_p_pd_id)
		left join trinventum.buyers b on b.b_id  = t.his_t_buyer
		left join trinventum.buyers_hist bh on bh.his_b_id  = t.his_t_buyer
		 and bh.his_b_version = (select max(his_b_version) from trinventum.buyers_hist where his_b_id = t.his_t_buyer)
		left join trinventum.sellers s on s.s_id  = t.his_t_seller
		left join trinventum.sellers_hist sh on sh.his_s_id  = t.his_t_seller
		 and sh.his_s_version = (select max(his_s_version) from trinventum.sellers_hist where his_s_id = t.his_t_seller)
		where t.his_t_id not in (select t_id from trinventum.transactions)
		order by t.his_t_id desc, t.his_t_version desc offset $1 limit $2');
	define ('TRIN_QUERY_ADD_TRANSACTION',
		'insert into trinventum.transactions (t_product_id, t_seller, t_buyer,
		t_price, t_paid, t_sent, t_sell_date, t_send_price, t_send_cost)
		values ($1, $2, $3, $4, $5, $6, $7, $8, $9)');
	define ('TRIN_QUERY_UPDATE_TRANSACTION',
		'update trinventum.transactions set t_product_id = $2, t_seller = $3,
		t_buyer = $4, t_price = $5, t_paid = $6, t_sent = $7, t_sell_date = $8,
		t_send_price = $9, t_send_cost = $10,
		t_version = t_version+1  where t_id = $1');
	define ('TRIN_QUERY_GET_TRANSACTION_DET',
		'select t.t_id, p.p_pd_id, t.t_product_id, pd.pd_name, t.t_seller, s.s_name,
		t.t_buyer, b.b_name, t.t_price, t.t_paid,
		t.t_sent, t.t_sell_date, t.t_send_price, t.t_send_cost, t.t_version
		from trinventum.transactions t
		join trinventum.products p on p.p_id = t.t_product_id
		join trinventum.product_definitions pd on pd.pd_id = p.p_pd_id
		join trinventum.buyers b on b.b_id  = t.t_buyer
		join trinventum.sellers s on s.s_id  = t.t_seller
		where t_id = $1');
	define ('TRIN_QUERY_DELETE_TRANSACTION',
		'delete from trinventum.transactions where t_id = $1');
	/*
	define ('TRIN_QUERY_GET_TRANS_HIST_BY_ID',
		'select t.his_t_id, p.p_pd_id, t.his_t_product_id,
		pd.pd_name, t.his_t_seller, s.s_name,
		t.his_t_buyer, b.b_name, t.his_t_price, t.his_t_paid,
		t.his_t_sent, t.his_t_sell_date, t.his_t_send_price,
		t.his_t_send_cost, t.his_t_version, t.his_t_user, t.his_t_record_timestamp
		from trinventum.transactions_hist t
		join trinventum.products p on p.p_id = t.his_t_product_id
		join trinventum.product_definitions pd on pd.pd_id = p.p_pd_id
		join trinventum.buyers b on b.b_id  = t.his_t_buyer
		join trinventum.sellers s on s.s_id  = t.his_t_seller
		where his_t_id = $1');
	*/
	define ('TRIN_QUERY_GET_TRANS_HIST_BY_ID',
		'select t.his_t_id,
		coalesce (p.p_pd_id, ph.his_p_pd_id, -1) as p_pd_id,
		t.his_t_product_id,
		coalesce (pd.pd_name, pdh.his_pd_name, \'?\') as pd_name,
		t.his_t_seller,
		coalesce (s.s_name, sh.his_s_name, \'?\') as s_name,
		t.his_t_buyer,
		coalesce (b.b_name, bh.his_b_name, \'?\') as b_name,
		t.his_t_price,
		t.his_t_paid,
		t.his_t_sent,
		t.his_t_sell_date,
		t.his_t_send_price,
		t.his_t_send_cost,
		t.his_t_version,
		t.his_t_user,
		t.his_t_record_timestamp
		from trinventum.transactions_hist t
		left join trinventum.products p on p.p_id = t.his_t_product_id
		left join trinventum.products_hist ph on ph.his_p_id = t.his_t_product_id
		 and ph.his_p_version = (select max(his_p_version) from trinventum.products_hist where his_p_id = t.his_t_product_id)
		left join trinventum.product_definitions pd on pd.pd_id = p.p_pd_id
		left join trinventum.product_definitions_hist pdh on pdh.his_pd_id = ph.his_p_pd_id
		 and pdh.his_pd_version = (select max(his_pd_version) from trinventum.product_definitions_hist
		 where his_pd_id = ph.his_p_pd_id)
		left join trinventum.buyers b on b.b_id  = t.his_t_buyer
		left join trinventum.buyers_hist bh on bh.his_b_id  = t.his_t_buyer
		 and bh.his_b_version = (select max(his_b_version) from trinventum.buyers_hist where his_b_id = t.his_t_buyer)
		left join trinventum.sellers s on s.s_id  = t.his_t_seller
		left join trinventum.sellers_hist sh on sh.his_s_id  = t.his_t_seller
		 and sh.his_s_version = (select max(his_s_version) from trinventum.sellers_hist where his_s_id = t.his_t_seller)
		where his_t_id = $1');

	define ('TRIN_QUERY_DESTROY_DATABASE',
		//'drop schema trinventum cascade; drop language plpgsql');
		'drop schema trinventum cascade');

	// ===============================================================

	function trin_db_open ($login, $pass, $dbname, $host, $port='', $timeout=60)
	{
		$conn_string = "host=$host dbname=$dbname user=$login password=$pass connect_timeout=$timeout";
		if ($port != '')
		{
			$conn_string .= " port=$port";
		}
		$conn = pg_connect($conn_string);
		return $conn;
	}

	function trin_db_close ($conn)
	{
		pg_close ($conn);
	}

	function trin_db_query ($conn, $query)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn, $query);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_query_get_numrows ($res)
	{
		return pg_num_rows ($res);
	}

	function trin_db_query_get_numfields ($res)
	{
		return pg_num_fields ($res);
	}

	function trin_db_query_get_column_name ($res, $index)
	{
		return pg_field_name ($res, $index);
	}

	function trin_db_query_get_column_type ($res, $index)
	{
		return pg_field_type ($res, $index);
	}

	function trin_db_query_get_next_row ($res)
	{
		return pg_fetch_row ($res);
	}

	function trin_db_check ($conn)
	{
		trin_db_clear_last_error();
		$result = pg_query ($conn, TRIN_QUERY_DB_CHECK);
		trin_db_set_last_error($conn);
		return $result !== FALSE;
	}

	function trin_db_get_version ($conn)
	{
		trin_db_clear_last_error();
		$result = pg_query ($conn, TRIN_QUERY_DB_VERSION_CHECK);
		trin_db_set_last_error($conn);
		if ($result !== FALSE)
		{
			$row = pg_fetch_assoc ($result);
			if ($row !== FALSE)
			{
				return (int)$row['db_version'];
			}
		}
		return 0;
	}

	// =================== Product definitions/types =====================

	function trin_db_get_product_categories ($conn)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn, TRIN_QUERY_GET_PRODUCT_CATEGORIES);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_product_category ($conn, $categories)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_CAT_FIELD_ID] = '';
		$result[TRIN_DB_PROD_CAT_FIELD_NAME] = '';

		$product = pg_fetch_assoc ($categories);
		trin_db_set_last_error($conn);
		if ($product !== FALSE)
		{
			$result[TRIN_DB_PROD_CAT_FIELD_ID] = $product['pc_id'];
			$result[TRIN_DB_PROD_CAT_FIELD_NAME] = $product['pc_name'];
			return $result;
		}
		return FALSE;
	}

	function trin_db_get_product_categories_as_options ($db)
	{
		$res = array();
		$categories = trin_db_get_product_categories ($db);
		if ($categories !== FALSE)
		{
			while (TRUE)
			{
				$next_cat = trin_db_get_next_product_category ($db, $categories);
				if ($next_cat === FALSE)
				{
					break;
				}
				$res[$next_cat[TRIN_DB_PROD_CAT_FIELD_ID]]
					= $next_cat[TRIN_DB_PROD_CAT_FIELD_NAME];
			}
		}
		return $res;
	}

	function trin_db_get_product_category_history ($db, $id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($db, TRIN_QUERY_GET_PRODUCT_CATEGORY_HIST_BY_ID,
			array ($id));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_get_next_product_category_history_entry ($db, $product_his)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_CAT_FIELD_ID] = '';
		$result[TRIN_DB_PROD_CAT_FIELD_NAME] = '';
		$result[TRIN_DB_PROD_CAT_FIELD_USER] = '';
		$result[TRIN_DB_PROD_CAT_FIELD_TIMESTAMP] = '';

		$his_entry = pg_fetch_assoc ($product_his);
		trin_db_set_last_error($db);
		if ($his_entry !== FALSE)
		{
			$result[TRIN_DB_PROD_CAT_FIELD_ID] = $his_entry['his_pc_id'];
			$result[TRIN_DB_PROD_CAT_FIELD_NAME] = $his_entry['his_pc_name'];
			$result[TRIN_DB_PROD_CAT_FIELD_USER] = $his_entry['his_pc_user'];
			$result[TRIN_DB_PROD_CAT_FIELD_TIMESTAMP] = $his_entry['his_pc_record_timestamp'];
			return $result;
		}
		return FALSE;
	}

	function trin_db_get_product_category_details ($db, $id)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_CAT_FIELD_ID] = '';
		$result[TRIN_DB_PROD_CAT_FIELD_NAME] = '';
		$result[TRIN_DB_PROD_CAT_FIELD_VERSION] = '';

		$cat_res = pg_query_params ($db,
			TRIN_QUERY_GET_PRODUCT_CATEGORY_DET, array ($id));
		trin_db_set_last_error($db);

		if ($cat_res !== FALSE)
		{
			$cat = pg_fetch_assoc ($cat_res);
			if ($cat !== FALSE)
			{
				$result[TRIN_DB_PROD_CAT_FIELD_ID] = $cat['pc_id'];
				$result[TRIN_DB_PROD_CAT_FIELD_NAME] = $cat['pc_name'];
				$result[TRIN_DB_PROD_CAT_FIELD_VERSION] = $cat['pc_name'];
				return $result;
			}
			else
			{
				trin_db_set_last_error ('No data');
			}
		}
		return FALSE;
	}

	function trin_db_get_product_defs_of_category ($db, $id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($db, TRIN_QUERY_GET_PRODUCT_DEFS_OF_CATEGORY, array ($id));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_add_product_category ($db, $name)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($db, TRIN_QUERY_ADD_PRODUCT_CATEGORY, array ($name));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_update_category ($db, $s_id, $name, $version)
	{
		trin_db_clear_last_error();
		$det = trin_db_get_product_category_details ($db, $s_id);
		if ($det === FALSE)
		{
			trin_db_set_last_error('Cannot read record before update');
			return FALSE;
		}
		else if ((int)$det[TRIN_DB_PROD_CAT_FIELD_VERSION] != (int)$version)
		{
			trin_db_set_last_error("Record version doesn't match: expected: "
				. $det[TRIN_DB_PROD_CAT_FIELD_VERSION]
				. ', got: ' . $version);
			return FALSE;
		}

		$res = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_CATEGORY,
			array ($s_id, $name));
		trin_db_set_last_error($db);
		return $res;
	}

	// =================== Product definitions/types =====================

	function trin_db_get_product_defs ($conn)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn, TRIN_QUERY_GET_PRODUCT_DEFS);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_create_photo_img_tag($prod_id)
	{
		return '<img src="get_photo.php?' .
			TRIN_PROD_PHOTO_PARAM . '=' . $prod_id
			. '" alt="-" class="prod_image">';
	}

	function trin_db_get_next_product ($conn, $products)
	{
		trin_db_clear_last_error();
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
		$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY] = '';

		$product = pg_fetch_assoc ($products);
		trin_db_set_last_error($conn);
		if ($product !== FALSE)
		{
			$pd_id = $product['pd_id'];
			$result[TRIN_DB_PROD_DEF_FIELD_ID] = $pd_id;
			if (//pg_field_is_null ($products, 'pd_photo') == 0
				$product['pd_photo'] !== NULL)
			{
				$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] =
					trin_create_photo_img_tag ($pd_id);
			}
			else
			{
				$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '-';
			}
			$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $product['pd_name'];
			$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = $product['pd_size'];
			if ((float)$product['pd_length'] > 0.0) // pd_length
			{
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE]
					.= '<br>Length: ' . $product['pd_length'];
			}
			if ((float)$product['pd_width'] > 0.0) // pd_width
			{
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE]
					.= '<br>Width: ' . $product['pd_width'];
			}
			$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = $product['pd_gender'];
			$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = $product['pd_colour'];
			$product_count_res = trin_db_count_products ($conn, $pd_id);
			$count_html = '';
			foreach ($product_count_res as $status => $count)
			{
				$count_html .= "$status: $count<br>";
			}
			$result[TRIN_DB_PROD_DEF_FIELD_COUNT] = $count_html;
			$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = $product['pd_comment'];
			$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = $product['pd_brand'];
			$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY] = $product['pc_name'];
			return $result;
		}
		return FALSE;
	}

	function trin_create_historical_photo_img_tag($prod_id, $prod_version)
	{
		return '<img src="get_photo.php?'
			. TRIN_PROD_PHOTO_PARAM_HIS . '=' . $prod_id
			. '&amp;'
			. TRIN_PROD_PHOTO_PARAM_HIS_VERSION . '=' . $prod_version
			. '" alt="-" class="prod_image">';
	}

	function trin_db_get_product_history ($db, $id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($db, TRIN_QUERY_GET_PRODUCT_HIST_BY_ID,
			array ($id));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_get_next_product_history_entry ($conn, $product_his)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_USER] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_TIMESTAMP] = '';

		$his_entry = pg_fetch_assoc ($product_his);
		trin_db_set_last_error($conn);
		if ($his_entry !== FALSE)
		{
			$pd_id = $his_entry['his_pd_id'];
			if (//pg_field_is_null ($products, 'pd_photo') == 0
				$his_entry['his_pd_photo'] !== NULL)
			{
				$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] =
					trin_create_historical_photo_img_tag (
						$pd_id, $his_entry['his_pd_version']);
			}
			else
			{
				$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '-';
			}
			$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $his_entry['his_pd_name'];
			$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = $his_entry['his_pd_size'];
			if ((float)$his_entry['his_pd_length'] > 0.0) // pd_length
			{
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE]
					.= '<br>Length: ' . $his_entry['his_pd_length'];
			}
			if ((float)$his_entry['his_pd_width'] > 0.0) // pd_width
			{
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE]
					.= '<br>Width: ' . $his_entry['his_pd_width'];
			}
			$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = $his_entry['his_pd_gender'];
			$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = $his_entry['his_pd_colour'];
			$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = $his_entry['his_pd_comment'];
			$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = $his_entry['his_pd_brand'];
			$result[TRIN_DB_PROD_DEF_FIELD_USER] = $his_entry['his_pd_user'];
			$result[TRIN_DB_PROD_DEF_FIELD_TIMESTAMP] = $his_entry['his_pd_record_timestamp'];
			$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY] = $his_entry['pc_name'];
			return $result;
		}
		return FALSE;
	}

	function fix_bytea_from_db ($data)
	{
		// contrary to what the manual says, it seems
		// that the database value not only has to be
		// un-escaped TWICE, but it also still has the
		// single quotes doubled (i.e., escaped), so they
		// must be taken care of, too. This may be the
		// effect of using parameterized statements
		//return pg_unescape_bytea
		//		(pg_unescape_bytea
		//			(str_replace ("''", "'", $data)));
		return base64_decode(pg_unescape_bytea($data));
	}

	function trin_db_get_photo ($db, $id)
	{
		trin_db_clear_last_error();
		$photo_result = pg_query_params ($db, TRIN_QUERY_GET_PRODUCT_PHOTO, array($id));
		trin_db_set_last_error($db);
		if ($photo_result !== FALSE)
		{
			if (! pg_field_is_null ($photo_result, 0, 'pd_photo'))
			{
				$photo_data = pg_fetch_assoc ($photo_result);
				if ($photo_data !== FALSE)
				{
					echo fix_bytea_from_db ($photo_data['pd_photo']);
				}
			}
		}
		return '';
	}

	function trin_db_get_history_photo ($db, $prod_id, $prod_ver)
	{
		trin_db_clear_last_error();
		$photo_result = pg_query_params ($db,
			TRIN_QUERY_GET_PRODUCT_HIST_PHOTO,
			array($prod_id, $prod_ver));
		trin_db_set_last_error($db);
		if ($photo_result !== FALSE)
		{
			if (! pg_field_is_null ($photo_result, 0, 'his_pd_photo'))
			{
				$photo_data = pg_fetch_assoc ($photo_result);
				if ($photo_data !== FALSE)
				{
					echo fix_bytea_from_db ($photo_data['his_pd_photo']);
				}
			}
		}
		return '';
	}

	function trin_db_add_product ($db, $param_pd_name, $param_pd_photo,
		$param_pd_size, $param_pd_length, $param_pd_width,
		$param_pd_colour, $param_pd_count, $param_pd_brand,
		$param_pd_gender, $param_pd_comment, $param_pd_category,
		$param_pd_cost)
	{
		trin_db_clear_last_error();
		if (is_uploaded_file ($_FILES[$param_pd_photo]['tmp_name']))
		{
			//$photo_data = pg_escape_bytea (
			//	file_get_contents ($_FILES[$param_pd_photo]['tmp_name']));
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
			trin_db_set_last_error($db);
			return FALSE;
		}
		$nextseq_val = pg_fetch_row ($nextseq_res);
		if ($nextseq_val === FALSE)
		{
			trin_db_set_last_error($db);
			return FALSE;
		}
		$pd_id = $nextseq_val[0];

		$res = pg_query ($db, 'begin');
		if ($res === FALSE)
		{
			trin_db_set_last_error($db);
			return FALSE;
		}

		// add the product definition:
		$result = pg_query_params ($db, TRIN_QUERY_ADD_PRODUCT_DEF,
			array ($pd_id, $photo_data, $param_pd_name, $param_pd_size,
			$param_pd_length, $param_pd_width, $param_pd_gender,
			$param_pd_colour, $param_pd_comment, $param_pd_brand,
			$param_pd_category));

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
					trin_db_set_last_error($db);
					break;
				}
			}
			if ($success)
			{
				$res = pg_query ($db, 'commit');
				trin_db_set_last_error($db);
				return $res;
			}
			else
			{
				trin_db_set_last_error($db);
				pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		else
		{
			trin_db_set_last_error($db);
			pg_query ($db, 'rollback');
			return FALSE;
		}
	}

	function trin_db_get_product_details ($db, $id)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COUNT] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_COUNT_TOTAL] = 0;
		$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_VERSION] = 0;

		$product_res = pg_query_params ($db,
			TRIN_QUERY_GET_PRODUCT_DET, array ($id));
		trin_db_set_last_error($db);

		if ($product_res !== FALSE)
		{
			$product = pg_fetch_assoc ($product_res);
			if ($product !== FALSE)
			{
				$pd_id = $product['pd_id'];
				$result[TRIN_DB_PROD_DEF_FIELD_ID] = $pd_id;
				if (//pg_field_is_null ($product_res, 'pd_photo') == 0
					$product['pd_photo'] !== NULL)
				{
					$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] =
						trin_create_photo_img_tag ($pd_id);
				}
				else
				{
					$result[TRIN_DB_PROD_DEF_FIELD_PHOTO] = '-';
				}
				$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $product['pd_name'];
				$result[TRIN_DB_PROD_DEF_FIELD_SIZE] = $product['pd_size'];
				$result[TRIN_DB_PROD_DEF_FIELD_LENGTH] = $product['pd_length'];
				$result[TRIN_DB_PROD_DEF_FIELD_WIDTH] = $product['pd_width'];
				$result[TRIN_DB_PROD_DEF_FIELD_GENDER] = $product['pd_gender'];
				$result[TRIN_DB_PROD_DEF_FIELD_COLOUR] = $product['pd_colour'];
				$product_count_res = trin_db_count_products ($db, $pd_id);
				$count_html = '';
				foreach ($product_count_res as $status => $count)
				{
					$count_html .= "$status: $count<br>";
				}

				$result[TRIN_DB_PROD_DEF_FIELD_COUNT] = $count_html;
				$result[TRIN_DB_PROD_DEF_FIELD_COUNT_TOTAL] =
					$product_count_res[TRIN_PROD_COUNT_COLUMN_TOTAL];
				$result[TRIN_DB_PROD_DEF_FIELD_COMMENT] = $product['pd_comment'];
				$result[TRIN_DB_PROD_DEF_FIELD_BRAND] = $product['pd_brand'];
				$result[TRIN_DB_PROD_DEF_FIELD_VERSION] = $product['pd_version'];
				$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY] = $product['pc_name'];
				$result[TRIN_DB_PROD_DEF_FIELD_CATEGORY_ID] = $product['pc_id'];
				return $result;
			}
			else
			{
				trin_db_set_last_error ($db, 'No data');
			}
		}
		return FALSE;
	}

	function trin_db_validate_product_version ($db, $pd_id, $param_pd_version)
	{
		$det = trin_db_get_product_details ($db, $pd_id);
		if ($det === FALSE)
		{
			trin_db_set_last_error($db, 'Cannot read record before update');
			return FALSE;
		}
		else if ((int)$det[TRIN_DB_PROD_DEF_FIELD_VERSION] != (int)$param_pd_version)
		{
			trin_db_set_last_error($db, "Record version doesn't match: expected: "
				. $det[TRIN_DB_PROD_DEF_FIELD_VERSION]
				. ', got: ' . $param_pd_version);
			return FALSE;
		}
		return TRUE;
	}

	function trin_db_update_product_name ($db, $pd_id,
		$param_pd_name, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_NAME,
			array($pd_id, $param_pd_name));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_photo ($db, $pd_id,
		$param_pd_photo, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		if (is_uploaded_file ($_FILES[$param_pd_photo]['tmp_name']))
		{
			//$photo_data = pg_escape_bytea (
			//	file_get_contents ($_FILES[$param_pd_photo]['tmp_name']));
			$photo_data = base64_encode (
				file_get_contents ($_FILES[$param_pd_photo]['tmp_name']));
		}
		else
		{
			$photo_data = NULL;
		}
		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_PHOTO,
			array($pd_id, $photo_data));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_size ($db, $pd_id,
		$param_pd_size, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_SIZE,
			array($pd_id, $param_pd_size));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_length ($db, $pd_id,
		$param_pd_length, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$param_pd_length = str_replace (',', '.', $param_pd_length);
		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_LENGTH,
			array($pd_id, $param_pd_length));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_width ($db, $pd_id,
		$param_pd_width, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$param_pd_width = str_replace (',', '.', $param_pd_width);
		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_WIDTH,
			array($pd_id, $param_pd_width));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_colour ($db, $pd_id,
		$param_pd_colour, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_COLOUR,
			array($pd_id, $param_pd_colour));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_count ($db, $pd_id, $param_pd_count,
		$param_pd_version, $param_pd_cost = '0.0')
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
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
				break;
			}
		}
		// if the new count is smaller, don't change it
		if ($old_pd_count > (int)$param_pd_count)
		{
			$param_pd_count = $old_pd_count;
		}

		$success = TRUE;
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
					trin_db_set_last_error($db);
					$success = FALSE;
					break;
				}
			}
		}
		return $success;
	}

	function trin_db_update_product_brand ($db, $pd_id,
		$param_pd_brand, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_BRAND,
			array($pd_id, $param_pd_brand));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_gender ($db, $pd_id,
		$param_pd_gender, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_GENDER,
			array($pd_id, $param_pd_gender));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_comment ($db, $pd_id,
		$param_pd_comment, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_COMMENT,
			array($pd_id, $param_pd_comment));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product_cost ($db, $pd_id,
		$param_pd_cost, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$param_pd_cost = str_replace (',', '.', $param_pd_cost);

		// update the cost of all instances
		$result = pg_query_params ($db,
			TRIN_QUERY_UPDATE_PRODUCT_INSTANCE_COST,
			array ($pd_id, $param_pd_cost));
		if ($result === FALSE)
		{
			trin_db_set_last_error($db);
			return FALSE;
		}
		return $result;
	}

	function trin_db_update_product_category ($db, $pd_id,
		$param_pd_category, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF_CATEGORY,
			array($pd_id, $param_pd_category));
		trin_db_set_last_error($db);
		return $result;
	}

	function trin_db_update_product ($db, $pd_id, $param_pd_name,
		$param_pd_photo, $param_pd_size,
		$param_pd_length, $param_pd_width,
		$param_pd_colour, $param_pd_count,
		$param_pd_brand, $param_pd_gender,
		$param_pd_comment, $param_pd_cost,
		$param_pd_category, $param_pd_version)
	{
		trin_db_clear_last_error();
		if (! trin_db_validate_product_version ($db, $pd_id, $param_pd_version))
		{
			return FALSE;
		}

		$res = pg_query ($db, 'begin');
		if ($res === FALSE)
		{
			trin_db_set_last_error($db);
			return FALSE;
		}

		// update the product definition:
		/*
		$result = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_DEF,
			array ($pd_id, $photo_data, $param_pd_name, $param_pd_size,
			$param_pd_length, $param_pd_width, $param_pd_gender,
			$param_pd_colour, $param_pd_comment, $param_pd_brand));
		*/
		$result = TRUE;
		$result = $result && trin_db_update_product_name ($db, $pd_id,
			$param_pd_name, $param_pd_version++);
		$result = $result && trin_db_update_product_photo ($db, $pd_id,
			$param_pd_photo, $param_pd_version++);
		$result = $result && trin_db_update_product_size ($db, $pd_id,
			$param_pd_size, $param_pd_version++);
		$result = $result && trin_db_update_product_length ($db, $pd_id,
			$param_pd_length, $param_pd_version++);
		$result = $result && trin_db_update_product_width ($db, $pd_id,
			$param_pd_width, $param_pd_version++);
		$result = $result && trin_db_update_product_colour ($db, $pd_id,
			$param_pd_colour, $param_pd_version++);
		$result = $result && trin_db_update_product_count ($db, $pd_id,
			$param_pd_count, $param_pd_version++, $param_pd_cost);
		$result = $result && trin_db_update_product_brand ($db, $pd_id,
			$param_pd_brand, $param_pd_version++);
		$result = $result && trin_db_update_product_gender ($db, $pd_id,
			$param_pd_gender, $param_pd_version++);
		$result = $result && trin_db_update_product_comment ($db, $pd_id,
			$param_pd_comment, $param_pd_version++);
		$result = $result && trin_db_update_product_cost ($db, $pd_id,
			$param_pd_cost, $param_pd_version++);
		$result = $result && trin_db_update_product_category ($db, $pd_id,
			$param_pd_category, $param_pd_version++);

		if ($result !== FALSE)
		{
			$res = pg_query ($db, 'commit');
			trin_db_set_last_error($db);
			return $res;
		}
		else
		{
			trin_db_set_last_error($db);
			pg_query ($db, 'rollback');
			return FALSE;
		}
	}

	function trin_db_count_products ($conn, $id)
	{
		trin_db_clear_last_error();
		$result = array ();
		$count_result = pg_query_params ($conn,
			TRIN_QUERY_GET_PRODUCT_COUNTS, array ($id));
		trin_db_set_last_error($conn);

		if ($count_result !== FALSE)
		{
			while (TRUE)
			{
				$row = pg_fetch_assoc ($count_result);
				if ($row === FALSE)
				{
					break;
				}
				$result[$row['p_status']] = $row['p_count'];
			}
		}
		return $result;
	}

	function trin_db_count_all_products ($conn)
	{
		trin_db_clear_last_error();
		$result = array ();
		$count_result = pg_query ($conn,
			TRIN_QUERY_GET_ALL_PRODUCT_COUNTS);
		trin_db_set_last_error($conn);

		if ($count_result !== FALSE)
		{
			while (TRUE)
			{
				$row = pg_fetch_assoc ($count_result);
				if ($row === FALSE)
				{
					break;
				}
				$result[$row['p_status']] = $row['p_count'];
			}
		}
		return $result;
	}

	function trin_db_get_product_status_changes ($conn, $months_ago)
	{
		trin_db_clear_last_error();
		$result = array ();
		$count_result = pg_query_params ($conn,
			TRIN_QUERY_GET_MONTH_HIST_PRODUCT_COUNTS,
			array ($months_ago));
		trin_db_set_last_error($conn);

		if ($count_result !== FALSE)
		{
			while (TRUE)
			{
				$row = pg_fetch_assoc ($count_result);
				if ($row === FALSE)
				{
					break;
				}
				$result[$row['p_status']] = $row['p_count'];
			}
		}
		return $result;
	}


	// =================== Product pieces =====================

	function trin_db_get_product_instances ($conn, $pd_id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_INSTANCES,
			array ($pd_id));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_product_instances_with_status ($conn, $pd_id, $pd_status)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_INSTANCES_WITH_STATUS,
			array ($pd_id, $pd_status));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_all_product_instances ($conn, $offset = 0, $limit = 1000000000)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_ALL_PRODUCT_INSTANCES,
			array ($offset, $limit));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_product_instance ($conn, $product_insts)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_PROD_INST_FIELD_STATUS] = '';
		$result[TRIN_DB_PROD_INST_FIELD_COST] = '';

		$product = pg_fetch_assoc ($product_insts);
		trin_db_set_last_error($conn);
		if ($product !== FALSE)
		{
			$result[TRIN_DB_PROD_INST_FIELD_ID] = $product['p_id'];
			$result[TRIN_DB_PROD_INST_FIELD_STATUS] = $product['p_status'];
			$result[TRIN_DB_PROD_INST_FIELD_COST] = $product['p_cost'];
			if (isset ($product['p_pd_id']))
			{
				$result[TRIN_DB_PROD_DEF_FIELD_ID] = $product['p_pd_id'];
			}
			if (isset ($product['pd_name']))
			{
				$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $product['pd_name'];
			}

			return $result;
		}
		return FALSE;
	}

	function trin_db_get_product_instance_details ($db, $p_id)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_STATUS] = '';
		$result[TRIN_DB_PROD_INST_FIELD_COST] = '';
		$result[TRIN_DB_PROD_INST_FIELD_VERSION] = 0;

		$pinst_res = pg_query_params ($db,
			TRIN_QUERY_GET_PRODUCT_INSTANCE_DET, array ($p_id));
		trin_db_set_last_error($db);

		if ($pinst_res !== FALSE)
		{
			$p_inst = pg_fetch_assoc ($pinst_res);
			if ($p_inst !== FALSE)
			{
				$result[TRIN_DB_PROD_INST_FIELD_ID] = $p_inst['p_id'];
				$result[TRIN_DB_PROD_INST_FIELD_STATUS] = $p_inst['p_status'];
				$result[TRIN_DB_PROD_INST_FIELD_COST] = $p_inst['p_cost'];
				$result[TRIN_DB_PROD_INST_FIELD_VERSION] = $p_inst['p_version'];
				return $result;
			}
			else
			{
				trin_db_set_last_error ($db, 'No data');
			}
		}
		return FALSE;
	}

	function trin_db_update_product_instance ($db, $p_id,
		$status, $cost, $version)
	{
		trin_db_clear_last_error();
		$det = trin_db_get_product_instance_details ($db, $p_id);
		if ($det === FALSE)
		{
			trin_db_set_last_error($db, 'Cannot read record before update');
			return FALSE;
		}
		else if ((int)$det[TRIN_DB_PROD_INST_FIELD_VERSION] != (int)$version)
		{
			trin_db_set_last_error($db, "Record version doesn't match: expected: "
				. $det[TRIN_DB_PROD_INST_FIELD_VERSION]
				. ', got: ' . $version);
			return FALSE;
		}

		$cost = str_replace (',', '.', $cost);
		$res = pg_query_params ($db, TRIN_QUERY_UPDATE_PRODUCT_INSTANCE,
			array ($p_id, $status, $cost));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_get_product_instance_history ($conn, $pd_id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_INSTANCE_HIST_BY_ID,
			array ($pd_id));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_product_instance_hist_entry ($conn, $product_inst_his)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_INST_FIELD_STATUS] = '';
		$result[TRIN_DB_PROD_INST_FIELD_COST] = '';
		$result[TRIN_DB_PROD_INST_FIELD_USER] = '';
		$result[TRIN_DB_PROD_INST_FIELD_TIMESTAMP] = '';

		$product_his = pg_fetch_assoc ($product_inst_his);
		trin_db_set_last_error($conn);
		if ($product_his !== FALSE)
		{
			$result[TRIN_DB_PROD_INST_FIELD_STATUS] = $product_his['his_p_status'];
			$result[TRIN_DB_PROD_INST_FIELD_COST] = $product_his['his_p_cost'];
			$result[TRIN_DB_PROD_INST_FIELD_USER] = $product_his['his_p_user'];
			$result[TRIN_DB_PROD_INST_FIELD_TIMESTAMP] = $product_his['his_p_record_timestamp'];

			return $result;
		}
		return FALSE;
	}

	// =================== Sellers =====================

	function trin_db_get_sellers ($conn)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn, TRIN_QUERY_GET_SELLERS);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_seller ($conn, $sellers)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';

		$seller = pg_fetch_assoc ($sellers);
		trin_db_set_last_error($conn);
		if ($seller !== FALSE)
		{
			$result[TRIN_DB_SELLER_PARAM_ID] = $seller['s_id'];
			$result[TRIN_DB_SELLER_PARAM_NAME] = $seller['s_name'];

			return $result;
		}
		return FALSE;
	}

	function trin_db_add_seller ($db, $name)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($db, TRIN_QUERY_ADD_SELLER, array ($name));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_get_seller_details ($db, $id)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';
		$result[TRIN_DB_SELLER_PARAM_VERSION] = 0;

		$seller_res = pg_query_params ($db,
			TRIN_QUERY_GET_SELLER_DET, array ($id));
		trin_db_set_last_error($db);

		if ($seller_res !== FALSE)
		{
			$seller = pg_fetch_assoc ($seller_res);
			if ($seller !== FALSE)
			{
				$result[TRIN_DB_SELLER_PARAM_ID] = $seller['s_id'];
				$result[TRIN_DB_SELLER_PARAM_NAME] = $seller['s_name'];
				$result[TRIN_DB_SELLER_PARAM_VERSION] = $seller['s_version'];
				return $result;
			}
			else
			{
				trin_db_set_last_error ($db, 'No data');
			}
		}
		return FALSE;
	}

	function trin_db_update_seller ($db, $s_id, $name, $version)
	{
		trin_db_clear_last_error();
		$det = trin_db_get_seller_details ($db, $s_id);
		if ($det === FALSE)
		{
			trin_db_set_last_error($db, 'Cannot read record before update');
			return FALSE;
		}
		else if ((int)$det[TRIN_DB_SELLER_PARAM_VERSION] != (int)$version)
		{
			trin_db_set_last_error($db, "Record version doesn't match: expected: "
				. $det[TRIN_DB_SELLER_PARAM_VERSION]
				. ', got: ' . $version);
			return FALSE;
		}

		$res = pg_query_params ($db, TRIN_QUERY_UPDATE_SELLER,
			array ($s_id, $name));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_get_seller_history ($conn, $s_id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_SELLER_HIST_BY_ID,
			array ($s_id));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_seller_hist_entry ($conn, $seller_his)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';
		$result[TRIN_DB_SELLER_PARAM_USER] = '';
		$result[TRIN_DB_SELLER_PARAM_TIMESTAMP] = '';

		$s_his = pg_fetch_assoc ($seller_his);
		trin_db_set_last_error($conn);
		if ($s_his !== FALSE)
		{
			$result[TRIN_DB_SELLER_PARAM_NAME] = $s_his['his_s_name'];
			$result[TRIN_DB_SELLER_PARAM_USER] = $s_his['his_s_user'];
			$result[TRIN_DB_SELLER_PARAM_TIMESTAMP] = $s_his['his_s_record_timestamp'];

			return $result;
		}
		return FALSE;
	}

	// =================== Buyers =====================

	function trin_db_get_buyers ($conn)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn, TRIN_QUERY_GET_BUYERS);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_buyer ($conn, $buyers)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ADDRESS] = '';
		$result[TRIN_DB_BUYER_PARAM_LOGIN] = '';
		$result[TRIN_DB_BUYER_PARAM_EMAIL] = '';
		$result[TRIN_DB_BUYER_PARAM_COMMENT] = '';

		$buyer = pg_fetch_assoc ($buyers);
		trin_db_set_last_error($conn);
		if ($buyer !== FALSE)
		{
			$result[TRIN_DB_BUYER_PARAM_ID] = $buyer['b_id'];
			$result[TRIN_DB_BUYER_PARAM_NAME] = $buyer['b_name'];
			$result[TRIN_DB_BUYER_PARAM_ADDRESS] = $buyer['b_postal_address'];
			$result[TRIN_DB_BUYER_PARAM_LOGIN] = $buyer['b_login'];
			$result[TRIN_DB_BUYER_PARAM_EMAIL] = $buyer['b_email_address'];
			$result[TRIN_DB_BUYER_PARAM_COMMENT] = $buyer['b_comment'];

			return $result;
		}
		return FALSE;
	}

	function trin_db_add_buyer ($db, $name, $address, $login, $email, $comment)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($db, TRIN_QUERY_ADD_BUYER,
			array ($name, $address, $login, $email, $comment));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_get_buyer_details ($db, $id)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ADDRESS] = '';
		$result[TRIN_DB_BUYER_PARAM_LOGIN] = '';
		$result[TRIN_DB_BUYER_PARAM_EMAIL] = '';
		$result[TRIN_DB_BUYER_PARAM_COMMENT] = '';
		$result[TRIN_DB_BUYER_PARAM_VERSION] = 0;

		$buyer_res = pg_query_params ($db,
			TRIN_QUERY_GET_BUYER_DET, array ($id));
		trin_db_set_last_error($db);

		if ($buyer_res !== FALSE)
		{
			$buyer = pg_fetch_assoc ($buyer_res);
			if ($buyer !== FALSE)
			{
				$result[TRIN_DB_BUYER_PARAM_ID] = $buyer['b_id'];
				$result[TRIN_DB_BUYER_PARAM_NAME] = $buyer['b_name'];
				$result[TRIN_DB_BUYER_PARAM_ADDRESS] = $buyer['b_postal_address'];
				$result[TRIN_DB_BUYER_PARAM_LOGIN] = $buyer['b_login'];
				$result[TRIN_DB_BUYER_PARAM_EMAIL] = $buyer['b_email_address'];
				$result[TRIN_DB_BUYER_PARAM_COMMENT] = $buyer['b_comment'];
				$result[TRIN_DB_BUYER_PARAM_VERSION] = $buyer['b_version'];
				return $result;
			}
			else
			{
				trin_db_set_last_error ($db, 'No data');
			}
		}
		return FALSE;
	}

	function trin_db_update_buyer ($db, $b_id, $name, $address,
		$login, $email, $comment, $version)
	{
		trin_db_clear_last_error();
		$det = trin_db_get_buyer_details ($db, $b_id);
		if ($det === FALSE)
		{
			trin_db_set_last_error($db, 'Cannot read record before update');
			return FALSE;
		}
		else if ((int)$det[TRIN_DB_BUYER_PARAM_VERSION] != (int)$version)
		{
			trin_db_set_last_error($db, "Record version doesn't match: expected: "
				. $det[TRIN_DB_BUYER_PARAM_VERSION]
				. ', got: ' . $version);
			return FALSE;
		}

		$res = pg_query_params ($db, TRIN_QUERY_UPDATE_BUYER,
			array ($b_id, $name, $address, $login, $email, $comment));
		trin_db_set_last_error($db);
		return $res;
	}

	function trin_db_get_buyer_history ($conn, $b_id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_BUYER_HIST_BY_ID,
			array ($b_id));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_buyer_hist_entry ($conn, $buyer_his)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ADDRESS] = '';
		$result[TRIN_DB_BUYER_PARAM_LOGIN] = '';
		$result[TRIN_DB_BUYER_PARAM_EMAIL] = '';
		$result[TRIN_DB_BUYER_PARAM_COMMENT] = '';
		$result[TRIN_DB_BUYER_PARAM_USER] = '';
		$result[TRIN_DB_BUYER_PARAM_TIMESTAMP] = '';

		$b_his = pg_fetch_assoc ($buyer_his);
		trin_db_set_last_error($conn);
		if ($b_his !== FALSE)
		{
			$result[TRIN_DB_BUYER_PARAM_NAME] = $b_his['his_b_name'];
			$result[TRIN_DB_BUYER_PARAM_ADDRESS] = $b_his['his_b_postal_address'];
			$result[TRIN_DB_BUYER_PARAM_LOGIN] = $b_his['his_b_login'];
			$result[TRIN_DB_BUYER_PARAM_EMAIL] = $b_his['his_b_email_address'];
			$result[TRIN_DB_BUYER_PARAM_COMMENT] = $b_his['his_b_comment'];
			$result[TRIN_DB_BUYER_PARAM_USER] = $b_his['his_b_user'];
			$result[TRIN_DB_BUYER_PARAM_TIMESTAMP] = $b_his['his_b_record_timestamp'];

			return $result;
		}
		return FALSE;
	}

	// =================== Transactions =====================

	function trin_db_get_transactions ($conn, $offset = 0, $limit = 1000000000)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_TRANSACTIONS,
			array ($offset, $limit));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_deleted_transactions ($conn, $offset = 0, $limit = 1000000000)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_DELETED_TRANSACTIONS,
			array ($offset, $limit));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_transaction ($conn, $trans)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_TRANS_PARAM_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_TRANS_PARAM_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_PAID] = '';
		$result[TRIN_DB_TRANS_PARAM_SENT] = '';
		$result[TRIN_DB_TRANS_PARAM_SELLDATE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_COST] = '';

		$tran = pg_fetch_assoc ($trans);
		trin_db_set_last_error($conn);
		if ($tran !== FALSE)
		{
			$result[TRIN_DB_TRANS_PARAM_ID] = $tran['t_id'];
			$result[TRIN_DB_PROD_DEF_FIELD_ID] = $tran['p_pd_id'];
			$result[TRIN_DB_PROD_INST_FIELD_ID] = $tran['t_product_id'];
			$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $tran['pd_name'];
			$result[TRIN_DB_SELLER_PARAM_ID] = $tran['t_seller'];
			$result[TRIN_DB_SELLER_PARAM_NAME] = $tran['s_name'];
			$result[TRIN_DB_BUYER_PARAM_ID] = $tran['t_buyer'];
			$result[TRIN_DB_BUYER_PARAM_NAME] = $tran['b_name'];
			$result[TRIN_DB_TRANS_PARAM_PRICE] = $tran['t_price'];
			$result[TRIN_DB_TRANS_PARAM_PAID] = $tran['t_paid'];
			$result[TRIN_DB_TRANS_PARAM_SENT] = $tran['t_sent'];
			$result[TRIN_DB_TRANS_PARAM_SELLDATE] = $tran['t_sell_date'];
			$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = $tran['t_send_price'];
			$result[TRIN_DB_TRANS_PARAM_SEND_COST] = $tran['t_send_cost'];

			return $result;
		}
		return FALSE;
	}

	function trin_db_add_transaction ($db, $t_product_id, $t_seller, $t_buyer,
		$t_price, $t_paid, $t_sent, $t_sell_date, $t_send_price, $t_send_cost)
	{
		trin_db_clear_last_error();
		$res = pg_query ($db, 'begin');
		trin_db_set_last_error($db);

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
					$res = pg_query ($db, 'commit');
					trin_db_set_last_error($db);
					return $res;
				}
				else
				{
					trin_db_set_last_error($db);
					pg_query ($db, 'rollback');
					return FALSE;
				}
			}
			else
			{
				trin_db_set_last_error($db);
				pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		return $res;
	}

	function trin_db_get_transaction_details ($db, $id)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_TRANS_PARAM_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_TRANS_PARAM_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_PAID] = '';
		$result[TRIN_DB_TRANS_PARAM_SENT] = '';
		$result[TRIN_DB_TRANS_PARAM_SELLDATE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_COST] = '';
		$result[TRIN_DB_TRANS_PARAM_VERSION] = 0;

		$trans_res = pg_query_params ($db,
			TRIN_QUERY_GET_TRANSACTION_DET, array ($id));
		trin_db_set_last_error($db);

		if ($trans_res !== FALSE)
		{
			$trans = pg_fetch_assoc ($trans_res);
			if ($trans !== FALSE)
			{
				$result[TRIN_DB_TRANS_PARAM_ID] = $trans['t_id'];
				$result[TRIN_DB_PROD_DEF_FIELD_ID] = $trans['p_pd_id'];
				$result[TRIN_DB_PROD_INST_FIELD_ID] = $trans['t_product_id'];
				$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $trans['pd_name'];
				$result[TRIN_DB_SELLER_PARAM_ID] = $trans['t_seller'];
				$result[TRIN_DB_SELLER_PARAM_NAME] = $trans['s_name'];
				$result[TRIN_DB_BUYER_PARAM_ID] = $trans['t_buyer'];
				$result[TRIN_DB_BUYER_PARAM_NAME] = $trans['b_name'];
				$result[TRIN_DB_TRANS_PARAM_PRICE] = $trans['t_price'];
				$result[TRIN_DB_TRANS_PARAM_PAID] = $trans['t_paid'];
				$result[TRIN_DB_TRANS_PARAM_SENT] = $trans['t_sent'];
				$result[TRIN_DB_TRANS_PARAM_SELLDATE] = $trans['t_sell_date'];
				$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = $trans['t_send_price'];
				$result[TRIN_DB_TRANS_PARAM_SEND_COST] = $trans['t_send_cost'];
				$result[TRIN_DB_TRANS_PARAM_VERSION] = $trans['t_version'];
				return $result;
			}
			else
			{
				trin_db_set_last_error ($db, 'No data');
			}
		}
		return FALSE;
	}

	function trin_db_update_transaction ($db, $trans_id, $t_product_id,
		$t_seller, $t_buyer, $t_price, $t_paid, $t_sent, $t_sell_date,
		$t_send_price, $t_send_cost, $t_version)
	{
		trin_db_clear_last_error();
		$res = pg_query ($db, 'begin');
		trin_db_set_last_error($db);

		if ($res !== FALSE)
		{
			$trans = trin_db_get_transaction_details ($db, $trans_id);
			if ($trans === FALSE)
			{
				trin_db_set_last_error($db, 'Cannot read record before update');
				pg_query ($db, 'rollback');
				return FALSE;
			}
			else if ((int)$trans[TRIN_DB_TRANS_PARAM_VERSION] != (int)$t_version)
			{
				trin_db_set_last_error($db, "Record version doesn't match: expected: "
					. $trans[TRIN_DB_TRANS_PARAM_VERSION]
					. ', got: ' . $t_version);
				pg_query ($db, 'rollback');
				return FALSE;
			}

			// mark the old product instance as READY
			$res = pg_query_params ($db,
				TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS,
				array ($trans[TRIN_DB_PROD_INST_FIELD_ID],
					TRIN_PROD_STATUS_READY));
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
					// mark new product instance as sold
					$res = pg_query_params ($db,
						TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS,
						array ($t_product_id, TRIN_PROD_STATUS_SOLD));
					if ($res !== FALSE)
					{
						$res = pg_query ($db, 'commit');
						trin_db_set_last_error($db);
						return $res;
					}
					else
					{
						trin_db_set_last_error($db);
						pg_query ($db, 'rollback');
						return FALSE;
					}
				}
				else
				{
					trin_db_set_last_error($db);
					pg_query ($db, 'rollback');
					return FALSE;
				}
			}
			else
			{
				trin_db_set_last_error($db);
				pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		return $res;
	}

	function trin_db_delete_transaction ($db, $t_id)
	{
		trin_db_clear_last_error();
		$res = pg_query ($db, 'begin');
		trin_db_set_last_error($db);

		if ($res !== FALSE)
		{
			$trans_det = trin_db_get_transaction_details ($db, $t_id);
			$res = pg_query_params ($db, TRIN_QUERY_DELETE_TRANSACTION,
				array ($t_id));
			if ($res !== FALSE)
			{
				// mark product instance as ready
				$res = pg_query_params ($db,
					TRIN_QUERY_CHANGE_PRODUCT_INSTANCE_STATUS,
					array ($trans_det[TRIN_DB_PROD_INST_FIELD_ID],
						TRIN_PROD_STATUS_READY));
				if ($res !== FALSE)
				{
					$res = pg_query ($db, 'commit');
					trin_db_set_last_error($db);
					return $res;
				}
				else
				{
					trin_db_set_last_error($db);
					pg_query ($db, 'rollback');
					return FALSE;
				}
			}
			else
			{
				trin_db_set_last_error($db);
				pg_query ($db, 'rollback');
				return FALSE;
			}
		}
		return $res;
	}

	function trin_db_get_transaction_history ($conn, $t_id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_TRANS_HIST_BY_ID,
			array ($t_id));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_transaction_hist_entry ($conn, $trans_his)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_INST_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_TRANS_PARAM_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_PAID] = '';
		$result[TRIN_DB_TRANS_PARAM_SENT] = '';
		$result[TRIN_DB_TRANS_PARAM_SELLDATE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = '';
		$result[TRIN_DB_TRANS_PARAM_SEND_COST] = '';
		$result[TRIN_DB_TRANS_PARAM_USER] = '';
		$result[TRIN_DB_TRANS_PARAM_TIMESTAMP] = '';

		$t_his = pg_fetch_assoc ($trans_his);
		trin_db_set_last_error($conn);
		if ($t_his !== FALSE)
		{
			$result[TRIN_DB_PROD_DEF_FIELD_ID] = $t_his['p_pd_id'];
			$result[TRIN_DB_PROD_INST_FIELD_ID] = $t_his['his_t_product_id'];
			$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $t_his['pd_name'];
			$result[TRIN_DB_SELLER_PARAM_ID] = $t_his['his_t_seller'];
			$result[TRIN_DB_SELLER_PARAM_NAME] = $t_his['s_name'];
			$result[TRIN_DB_BUYER_PARAM_ID] = $t_his['his_t_buyer'];
			$result[TRIN_DB_BUYER_PARAM_NAME] = $t_his['b_name'];
			$result[TRIN_DB_TRANS_PARAM_PRICE] = $t_his['his_t_price'];
			$result[TRIN_DB_TRANS_PARAM_PAID] = $t_his['his_t_paid'];
			$result[TRIN_DB_TRANS_PARAM_SENT] = $t_his['his_t_sent'];
			$result[TRIN_DB_TRANS_PARAM_SELLDATE] = $t_his['his_t_sell_date'];
			$result[TRIN_DB_TRANS_PARAM_SEND_PRICE] = $t_his['his_t_send_price'];
			$result[TRIN_DB_TRANS_PARAM_SEND_COST] = $t_his['his_t_send_cost'];
			$result[TRIN_DB_TRANS_PARAM_USER] = $t_his['his_t_user'];
			$result[TRIN_DB_TRANS_PARAM_TIMESTAMP] = $t_his['his_t_record_timestamp'];

			return $result;
		}
		return FALSE;
	}

	// =================== Analytics =====================

	function trin_db_get_product_buys ($conn, $pd_id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_BUYS,
			array ($pd_id));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_product_buy ($conn, $buys)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_TRANS_PARAM_COUNT] = '';

		$buy = pg_fetch_assoc ($buys);
		trin_db_set_last_error($conn);
		if ($buy !== FALSE)
		{
			$result[TRIN_DB_BUYER_PARAM_ID] = $buy['b_id'];
			$result[TRIN_DB_BUYER_PARAM_NAME] = $buy['b_name'];
			$result[TRIN_DB_TRANS_PARAM_COUNT] = $buy['b_count'];

			return $result;
		}
		return FALSE;
	}

	function trin_db_get_product_sales ($conn, $pd_id)
	{
		trin_db_clear_last_error();
		$res = pg_query_params ($conn, TRIN_QUERY_GET_PRODUCT_SELLINGS,
			array ($pd_id));
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_product_sale ($conn, $sales)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';
		$result[TRIN_DB_TRANS_PARAM_COUNT] = '';

		$sale = pg_fetch_assoc ($sales);
		trin_db_set_last_error($conn);
		if ($sale !== FALSE)
		{
			$result[TRIN_DB_SELLER_PARAM_ID] = $sale['s_id'];
			$result[TRIN_DB_SELLER_PARAM_NAME] = $sale['s_name'];
			$result[TRIN_DB_TRANS_PARAM_COUNT] = $sale['s_count'];

			return $result;
		}
		return FALSE;
	}

	function trin_db_get_seller_transactions ($conn)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn,
			TRIN_QUERY_GET_SELLER_TRANSACTIONS);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_seller_transaction ($conn, $seller_trans)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_SELLER_PARAM_ID] = '';
		$result[TRIN_DB_SELLER_PARAM_NAME] = '';
		$result[TRIN_DB_TRANS_PARAM_COUNT] = '';

		$trans = pg_fetch_assoc ($seller_trans);
		trin_db_set_last_error($conn);
		if ($trans !== FALSE)
		{
			$result[TRIN_DB_PROD_DEF_FIELD_ID] = $trans['pd_id'];
			$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $trans['pd_name'];
			$result[TRIN_DB_SELLER_PARAM_ID] = $trans['s_id'];
			$result[TRIN_DB_SELLER_PARAM_NAME] = $trans['s_name'];
			$result[TRIN_DB_TRANS_PARAM_COUNT] = $trans['st_count'];

			return $result;
		}
		return FALSE;
	}

	function trin_db_get_buyer_transactions ($conn)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn,
			TRIN_QUERY_GET_BUYER_TRANSACTIONS);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_get_next_buyer_transaction ($conn, $buyer_trans)
	{
		trin_db_clear_last_error();
		$result = array ();
		$result[TRIN_DB_PROD_DEF_FIELD_ID] = '';
		$result[TRIN_DB_PROD_DEF_FIELD_NAME] = '';
		$result[TRIN_DB_BUYER_PARAM_ID] = '';
		$result[TRIN_DB_BUYER_PARAM_NAME] = '';
		$result[TRIN_DB_TRANS_PARAM_COUNT] = '';

		$trans = pg_fetch_assoc ($buyer_trans);
		trin_db_set_last_error($conn);
		if ($trans !== FALSE)
		{
			$result[TRIN_DB_PROD_DEF_FIELD_ID] = $trans['pd_id'];
			$result[TRIN_DB_PROD_DEF_FIELD_NAME] = $trans['pd_name'];
			$result[TRIN_DB_BUYER_PARAM_ID] = $trans['b_id'];
			$result[TRIN_DB_BUYER_PARAM_NAME] = $trans['b_name'];
			$result[TRIN_DB_TRANS_PARAM_COUNT] = $trans['bt_count'];

			return $result;
		}
		return FALSE;
	}

	// =================== Management =====================

	function trin_db_destroy_schema ($conn)
	{
		trin_db_clear_last_error();
		$res = pg_query ($conn,
			TRIN_QUERY_DESTROY_DATABASE);
		trin_db_set_last_error($conn);
		return $res;
	}

	function trin_db_clear_last_error()
	{
		unset ($_SESSION[TRIN_SESS_DB_LAST_ERROR]);
	}

	function trin_db_set_last_error($conn, $error = '')
	{
		if (isset ($_SESSION[TRIN_SESS_DB_LAST_ERROR])
			&& $_SESSION[TRIN_SESS_DB_LAST_ERROR] != '')
		{
			// error already set, don't overwrite it
			return;
		}
		$last_error = pg_last_error ($conn);
		if ($last_error === '' || $last_error === FALSE)
		{
			$last_error = $error;
		}
		$_SESSION[TRIN_SESS_DB_LAST_ERROR] = $last_error;
	}

	function trin_db_get_last_error($conn)
	{
		$db_error = pg_last_error ($conn);
		$db_sess_error = '';
		if (!$db_error && isset ($_SESSION[TRIN_SESS_DB_LAST_ERROR]))
		{
			$db_sess_error = $_SESSION[TRIN_SESS_DB_LAST_ERROR];
			unset ($_SESSION[TRIN_SESS_DB_LAST_ERROR]);
		}
		if ($db_error)
		{
			return $db_error;
		}
		if ($db_sess_error)
		{
			return $db_sess_error;
		}
		return '-Unknown error-';
	}
?>
