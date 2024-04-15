/*
 * Trinventum - the database script for creating roles.
 *
 * Copyright (C) 2024 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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

create role trinventum_product_manager;
grant connect on database trinventum to trinventum_product_manager;
grant usage on schema trinventum to trinventum_product_manager;
grant select on trinventum.versions to trinventum_product_manager;
grant select on sequence trinventum.product_categories_pc_id_seq to trinventum_product_manager;
grant usage on sequence trinventum.product_categories_pc_id_seq to trinventum_product_manager;
grant select on sequence trinventum.seq_pd_id to trinventum_product_manager;
grant usage on sequence trinventum.seq_pd_id to trinventum_product_manager;
grant select on sequence trinventum.products_p_id_seq to trinventum_product_manager;
grant usage on sequence trinventum.products_p_id_seq to trinventum_product_manager;
grant select, insert, update, delete on trinventum.product_categories to trinventum_product_manager;
grant select, insert on trinventum.product_categories_hist to trinventum_product_manager;
grant select, insert, update, delete on trinventum.product_definitions to trinventum_product_manager;
grant select, insert on trinventum.product_definitions_hist to trinventum_product_manager;
grant select, insert, update, delete on trinventum.products to trinventum_product_manager;
grant select, insert on trinventum.products_hist to trinventum_product_manager;
grant execute on function trinventum.months_ago(m integer) to trinventum_product_manager;
grant execute on function trinventum.year_months_ago(m integer) to trinventum_product_manager;

create role trinventum_seller_manager;
grant connect on database trinventum to trinventum_seller_manager;
grant usage on schema trinventum to trinventum_seller_manager;
grant select on trinventum.versions to trinventum_seller_manager;
grant select on sequence trinventum.sellers_s_id_seq to trinventum_seller_manager;
grant usage on sequence trinventum.sellers_s_id_seq to trinventum_seller_manager;
grant select, insert, update, delete on trinventum.sellers to trinventum_seller_manager;
grant select, insert on trinventum.sellers_hist to trinventum_seller_manager;

create role trinventum_buyer_manager;
grant connect on database trinventum to trinventum_buyer_manager;
grant usage on schema trinventum to trinventum_buyer_manager;
grant select on trinventum.versions to trinventum_buyer_manager;
grant select on sequence trinventum.buyers_b_id_seq to trinventum_buyer_manager;
grant usage on sequence trinventum.buyers_b_id_seq to trinventum_buyer_manager;
grant select, insert, update, delete on trinventum.buyers to trinventum_buyer_manager;
grant select, insert on trinventum.buyers_hist to trinventum_buyer_manager;

create role trinventum_transaction_manager;
grant connect on database trinventum to trinventum_transaction_manager;
grant usage on schema trinventum to trinventum_transaction_manager;
grant select on sequence trinventum.transactions_t_id_seq to trinventum_transaction_manager;
grant usage on sequence trinventum.transactions_t_id_seq to trinventum_transaction_manager;
grant select on trinventum.versions to trinventum_transaction_manager;
grant select, insert, update, delete on trinventum.transactions to trinventum_transaction_manager;
grant select, insert on trinventum.transactions_hist to trinventum_transaction_manager;
grant select on trinventum.product_categories to trinventum_transaction_manager;
grant select on trinventum.product_definitions to trinventum_transaction_manager;
grant select on trinventum.product_definitions_hist to trinventum_transaction_manager;
grant select, update on trinventum.products to trinventum_transaction_manager;
grant select, insert on trinventum.products_hist to trinventum_transaction_manager;
grant select on trinventum.buyers to trinventum_transaction_manager;
grant select on trinventum.buyers_hist to trinventum_transaction_manager;
grant select on trinventum.sellers to trinventum_transaction_manager;
grant select on trinventum.sellers_hist to trinventum_transaction_manager;
