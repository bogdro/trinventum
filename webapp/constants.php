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

	define ('TRIN_VERSION', '0.4');

	define ('TRIN_HTTP_DATE_FORMAT', 'D, j M Y G:i:s T');

	define ('TRIN_SESS_DB_CONN', 'trin_sess_db_conn');
	define ('TRIN_SESS_DB_LOGIN', 'trin_sess_db_login');
	define ('TRIN_SESS_DB_PASS', 'trin_sess_db_pass');
	define ('TRIN_SESS_DB_HOST', 'trin_sess_db_host');
	define ('TRIN_SESS_DB_DBNAME', 'trin_sess_db_dbname');
	define ('TRIN_SESS_DB_LAST_ERROR', 'trin_sess_db_last_error');
	define ('TRIN_SESS_LAST_SUCCESS', 'trin_sess_last_success');

	define ('TRIN_EXPECTED_DB_VERSION', '4');

	define ('TRIN_DB_PROD_DEF_FIELD_ID', 'id');
	define ('TRIN_DB_PROD_DEF_FIELD_PHOTO', 'photo');
	define ('TRIN_DB_PROD_DEF_FIELD_NAME', 'name');
	define ('TRIN_DB_PROD_DEF_FIELD_SIZE', 'size');
	define ('TRIN_DB_PROD_DEF_FIELD_LENGTH', 'length');
	define ('TRIN_DB_PROD_DEF_FIELD_WIDTH', 'width');
	define ('TRIN_DB_PROD_DEF_FIELD_GENDER', 'gender');
	define ('TRIN_DB_PROD_DEF_FIELD_COLOUR', 'colour');
	define ('TRIN_DB_PROD_DEF_FIELD_COUNT', 'count');
	define ('TRIN_DB_PROD_DEF_FIELD_COUNT_TOTAL', 'count_total');
 	define ('TRIN_DB_PROD_DEF_FIELD_COMMENT', 'comment');
	define ('TRIN_DB_PROD_DEF_FIELD_BRAND', 'brand');
	define ('TRIN_DB_PROD_DEF_FIELD_CATEGORY', 'category');
	define ('TRIN_DB_PROD_DEF_FIELD_CATEGORY_ID', 'category_id');
 	define ('TRIN_DB_PROD_DEF_FIELD_VERSION', 'version');
	define ('TRIN_DB_PROD_DEF_FIELD_USER', 'user');
	define ('TRIN_DB_PROD_DEF_FIELD_TIMESTAMP', 'timestamp');

	define ('TRIN_PROD_PHOTO_PARAM', 'id');
	define ('TRIN_PROD_PHOTO_PARAM_HIS', 'h_id');
	define ('TRIN_PROD_PHOTO_PARAM_HIS_VERSION', 'h_v');
	define ('TRIN_PROD_DETAIL_PARAM', 'id');

	define ('TRIN_PROD_COUNT_COLUMN_TOTAL', 'Total');

	define ('TRIN_PROD_STATUS_READY', 'READY');
	define ('TRIN_PROD_STATUS_SALE_IN_PROGRESS', 'SELLING');
	define ('TRIN_PROD_STATUS_SOLD', 'SOLD');

	define ('TRIN_DB_PROD_PARAM_NAME', 'prod_param_name');
	define ('TRIN_DB_PROD_PARAM_PHOTO', 'prod_param_photo');
	define ('TRIN_DB_PROD_PARAM_SIZE', 'prod_param_size');
	define ('TRIN_DB_PROD_PARAM_LENGTH', 'prod_param_length');
	define ('TRIN_DB_PROD_PARAM_WIDTH', 'prod_param_width');
	define ('TRIN_DB_PROD_PARAM_COLOUR', 'prod_param_colour');
	define ('TRIN_DB_PROD_PARAM_COUNT', 'prod_param_count');
	define ('TRIN_DB_PROD_PARAM_BRAND', 'prod_param_brand');
	define ('TRIN_DB_PROD_PARAM_GENDER', 'prod_param_gender');
	define ('TRIN_DB_PROD_PARAM_COMMENT', 'prod_param_comment');
	define ('TRIN_DB_PROD_PARAM_COST', 'prod_param_cost');
	define ('TRIN_DB_PROD_PARAM_CATEGORY', 'prod_param_category');
 	define ('TRIN_DB_PROD_PARAM_VERSION', 'prod_param_vesion');

	define ('TRIN_DB_PROD_INST_FIELD_ID', 'pid');
	define ('TRIN_DB_PROD_INST_FIELD_STATUS', 'status');
	define ('TRIN_DB_PROD_INST_FIELD_COST', 'cost');
	define ('TRIN_DB_PROD_INST_FIELD_VERSION', 'version');
	define ('TRIN_DB_PROD_INST_FIELD_USER', 'user');
	define ('TRIN_DB_PROD_INST_FIELD_TIMESTAMP', 'timestamp');

	define ('TRIN_DB_PROD_INST_PARAM_LIST', 'pp_list');
	define ('TRIN_DB_PROD_INST_LIST_PARAM_START', 'pstart');
	define ('TRIN_DB_PROD_INST_LIST_PARAM_COUNT', 'pcount');

	define ('TRIN_DB_SELLER_PARAM_ID', 'sid');
	define ('TRIN_DB_SELLER_PARAM_NAME', 'seller_param_name');
	define ('TRIN_DB_SELLER_PARAM_VERSION', 'seller_param_version');
	define ('TRIN_DB_SELLER_PARAM_USER', 'seller_param_user');
	define ('TRIN_DB_SELLER_PARAM_TIMESTAMP', 'seller_param_timestamp');

	define ('TRIN_DB_BUYER_PARAM_ID', 'bid');
	define ('TRIN_DB_BUYER_PARAM_NAME', 'buyer_param_name');
	define ('TRIN_DB_BUYER_PARAM_ADDRESS', 'buyer_param_address');
	define ('TRIN_DB_BUYER_PARAM_LOGIN', 'buyer_param_login');
	define ('TRIN_DB_BUYER_PARAM_EMAIL', 'buyer_param_email');
	define ('TRIN_DB_BUYER_PARAM_COMMENT', 'buyer_param_comment');
	define ('TRIN_DB_BUYER_PARAM_VERSION', 'buyer_param_version');
	define ('TRIN_DB_BUYER_PARAM_USER', 'buyer_param_user');
	define ('TRIN_DB_BUYER_PARAM_TIMESTAMP', 'buyer_param_timestamp');

	define ('TRIN_DB_TRANS_PARAM_LIST', 'trans_list');
	define ('TRIN_DB_TRANS_PARAM_ID', 'tid');
	define ('TRIN_DB_TRANS_PARAM_PRICE', 'trans_price');
	define ('TRIN_DB_TRANS_PARAM_PAID', 'trans_paid');
	define ('TRIN_DB_TRANS_PARAM_SENT', 'trans_sent');
	define ('TRIN_DB_TRANS_PARAM_SELLDATE', 'trans_sell_date');
	define ('TRIN_DB_TRANS_PARAM_SEND_PRICE', 'trans_send_price');
	define ('TRIN_DB_TRANS_PARAM_SEND_COST', 'trans_send_cost');
	define ('TRIN_DB_TRANS_PARAM_VERSION', 'trans_version');
	define ('TRIN_DB_TRANS_PARAM_USER', 'trans_user');
	define ('TRIN_DB_TRANS_PARAM_TIMESTAMP', 'trans_timestamp');

	define ('TRIN_DB_TRANS_LIST_PARAM_START', 'tstart');
	define ('TRIN_DB_TRANS_LIST_PARAM_COUNT', 'tcount');

	define ('TRIN_DB_TRANS_PARAM_COUNT', 'trans_count');

	define ('TRIN_VALIDATION_FIELD_TYPE_NUMBER', 'number');
	define ('TRIN_VALIDATION_FIELD_TYPE_REQUIRED', 'required');

	define ('TRIN_FORM_FIELD_SUBMIT_PREFIX', 'submit_');
	define ('TRIN_FORM_SUBMIT_DB_DUMP', 'trin_db_dump');
	define ('TRIN_FORM_SUBMIT_DB_RESTORE', 'trin_db_restore');
	define ('TRIN_FORM_PARAM_DB_RESTORE_FILE', 'trin_db_restore_file');
	define ('TRIN_FORM_SUBMIT_DB_DESTROY', 'trin_db_destroy');
	define ('TRIN_FORM_SUBMIT_DB_DESTROY2', 'trin_db_destroy2');

	define ('TRIN_FORM_PARAM_DB_QUERY', 'trin_db_query');

	define ('TRIN_CAT_DETAIL_PARAM', 'id');
	define ('TRIN_DB_PROD_CAT_FIELD_ID', 'cat_param_id');
	define ('TRIN_DB_PROD_CAT_FIELD_NAME', 'cat_param_name');
	define ('TRIN_DB_PROD_CAT_FIELD_VERSION', 'cat_param_version');
	define ('TRIN_DB_PROD_CAT_FIELD_USER', 'cat_user');
	define ('TRIN_DB_PROD_CAT_FIELD_TIMESTAMP', 'cat_timestamp');
?>
