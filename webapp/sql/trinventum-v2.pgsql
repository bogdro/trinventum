/*
 * Trinventum - the database script for database version 2.
 *
 * Copyright (C) 2015-2022 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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

alter table trinventum.product_definitions add pd_version integer not null default 1 check (pd_version >= 1);
alter table trinventum.products add p_version integer not null default 1 check (p_version >= 1);
alter table trinventum.sellers add s_version integer not null default 1 check (s_version >= 1);
alter table trinventum.buyers add b_version integer not null default 1 check (b_version >= 1);
alter table trinventum.transactions add t_version integer not null default 1 check (t_version >= 1);

comment on column trinventum.product_definitions.pd_version is 'Record version';
comment on column trinventum.products.p_version is 'Record version';
comment on column trinventum.sellers.s_version is 'Record version';
comment on column trinventum.buyers.b_version is 'Record version';
comment on column trinventum.transactions.t_version is 'Record version';

update trinventum.product_definitions set pd_version = 1;
update trinventum.products set p_version = 1;
update trinventum.sellers set s_version = 1;
update trinventum.buyers set b_version = 1;
update trinventum.transactions set t_version = 1;

/* Depends on the database version, seems enabled by default on some:
create language plpgsql;
*/
set schema 'trinventum';

create table trinventum.product_definitions_hist
(
	his_pd_id bigint,
	his_pd_name text,
	his_pd_photo bytea,
	his_pd_size text,
	his_pd_length numeric (7,2),
	his_pd_width numeric (7,2),
	his_pd_colour text,
	his_pd_brand text,
	his_pd_gender trinventum.t_gender,
	his_pd_comment text,
	his_pd_version integer,
	his_pd_user text default current_user,
	his_pd_record_timestamp timestamp not null default now()
);

comment on table trinventum.product_definitions_hist is 'The table for product definitions/type history';
comment on column trinventum.product_definitions_hist.his_pd_id is 'Product definition ID';
comment on column trinventum.product_definitions_hist.his_pd_name is 'Product name';
comment on column trinventum.product_definitions_hist.his_pd_photo is 'Product binary photo data';
comment on column trinventum.product_definitions_hist.his_pd_size is 'Product size';
comment on column trinventum.product_definitions_hist.his_pd_length is 'Product length';
comment on column trinventum.product_definitions_hist.his_pd_width is 'Product width';
comment on column trinventum.product_definitions_hist.his_pd_colour is 'Product colour';
comment on column trinventum.product_definitions_hist.his_pd_brand is 'Product brand name';
comment on column trinventum.product_definitions_hist.his_pd_gender is 'Product target gender/sex';
comment on column trinventum.product_definitions_hist.his_pd_comment is 'Product comment/description';
comment on column trinventum.product_definitions_hist.his_pd_version is 'Record version';
comment on column trinventum.product_definitions_hist.his_pd_user is 'History record creation user';
comment on column trinventum.product_definitions_hist.his_pd_record_timestamp is 'History record creation time';

create index product_definitions_hist_idx_his_pd_id on trinventum.product_definitions_hist (his_pd_id);
comment on index product_definitions_hist_idx_his_pd_id is
 'Index for searching product definition history by product ID';

create function trinventum.trg_product_definitions_proc()
returns trigger as
$trg_product_definitions_proc$
begin
	insert into trinventum.product_definitions_hist
	(his_pd_id, his_pd_name, his_pd_photo, his_pd_size,
	 his_pd_length, his_pd_width, his_pd_colour, his_pd_brand,
	 his_pd_gender, his_pd_comment, his_pd_version)
	values
	(old.pd_id, old.pd_name, old.pd_photo, old.pd_size,
	 old.pd_length, old.pd_width, old.pd_colour, old.pd_brand,
	 old.pd_gender, old.pd_comment, old.pd_version);

	return null;
end;
$trg_product_definitions_proc$ language plpgsql;

comment on function trinventum.trg_product_definitions_proc() is
 'Function for the trg_product_definitions trigger, which saves product_definitions record history';

create trigger trg_product_definitions
after update or delete on trinventum.product_definitions
for each row execute procedure trinventum.trg_product_definitions_proc();

comment on trigger trg_product_definitions on trinventum.product_definitions is
 'Trigger that saves product_definitions record history';

create table trinventum.products_hist
(
	his_p_id bigint,
	his_p_pd_id bigint,
	his_p_status text,
	his_p_cost numeric (7,2),
	his_p_version integer,
	his_p_user text default current_user,
	his_p_record_timestamp timestamp not null default now()
);

comment on table trinventum.products_hist is 'The table for individual product instance/piece history';
comment on column trinventum.products_hist.his_p_id is 'Product piece ID';
comment on column trinventum.products_hist.his_p_pd_id is 'Product type of the given product piece';
comment on column trinventum.products_hist.his_p_status is 'Product piece''s status';
comment on column trinventum.products_hist.his_p_cost is 'Product piece''s individual cost';
comment on column trinventum.products_hist.his_p_version is 'Record version';
comment on column trinventum.products_hist.his_p_user is 'History record creation user';
comment on column trinventum.products_hist.his_p_record_timestamp is 'History record creation time';

create index products_hist_idx_his_p_id on trinventum.products_hist (his_p_id);
comment on index products_hist_idx_his_p_id is
 'Index for searching product piece history by product piece ID';

create function trinventum.trg_products_proc()
returns trigger as
$trg_products_proc$
begin
	insert into trinventum.products_hist
	(his_p_id, his_p_pd_id, his_p_status, his_p_cost, his_p_version)
	values
	(old.p_id, old.p_pd_id, old.p_status, old.p_cost, old.p_version);

	return null;
end;
$trg_products_proc$ language plpgsql;

comment on function trinventum.trg_products_proc() is
 'Function for the trg_products trigger, which saves products record history';

create trigger trg_products
after update or delete on trinventum.products
for each row execute procedure trinventum.trg_products_proc();

comment on trigger trg_products on trinventum.products is
 'Trigger that saves products record history';

create table trinventum.sellers_hist
(
	his_s_id bigint,
	his_s_name text,
	his_s_version integer,
	his_s_user text default current_user,
	his_s_record_timestamp timestamp not null default now()
);

comment on table trinventum.sellers_hist is 'The table for product seller history';
comment on column trinventum.sellers_hist.his_s_id is 'Seller ID';
comment on column trinventum.sellers_hist.his_s_name is 'Seller name';
comment on column trinventum.sellers_hist.his_s_version is 'Record version';
comment on column trinventum.sellers_hist.his_s_user is 'History record creation user';
comment on column trinventum.sellers_hist.his_s_record_timestamp is 'History record creation time';

create index sellers_hist_idx_his_s_id on trinventum.sellers_hist (his_s_id);
comment on index sellers_hist_idx_his_s_id is
 'Index for searching seller history by seller ID';

create function trinventum.trg_sellers_proc()
returns trigger as
$trg_sellers_proc$
begin
	insert into trinventum.sellers_hist
	(his_s_id, his_s_name, his_s_version)
	values
	(old.s_id, old.s_name, old.s_version);

	return null;
end;
$trg_sellers_proc$ language plpgsql;

comment on function trinventum.trg_sellers_proc() is
 'Function for the trg_sellers trigger, which saves seller record history';

create trigger trg_sellers
after update or delete on trinventum.sellers
for each row execute procedure trinventum.trg_sellers_proc();

comment on trigger trg_sellers on trinventum.sellers is
 'Trigger that saves seller record history';

create table trinventum.buyers_hist
(
	his_b_id bigint,
	his_b_name text,
	his_b_postal_address text,
	his_b_login text,
	his_b_email_address text,
	his_b_comment text,
	his_b_version integer,
	his_b_user text default current_user,
	his_b_record_timestamp timestamp not null default now()
);

comment on table trinventum.buyers_hist is 'The table for product buyer history';
comment on column trinventum.buyers_hist.his_b_id is 'Buyer ID';
comment on column trinventum.buyers_hist.his_b_name is 'Buyer''s name';
comment on column trinventum.buyers_hist.his_b_postal_address is 'Buyer''s postal address';
comment on column trinventum.buyers_hist.his_b_login is 'Buyer''s login (short name)';
comment on column trinventum.buyers_hist.his_b_email_address is 'Buyer''s e-mail address';
comment on column trinventum.buyers_hist.his_b_comment is 'Buyer''s description/comment';
comment on column trinventum.buyers_hist.his_b_version is 'Record version';
comment on column trinventum.buyers_hist.his_b_user is 'History record creation user';
comment on column trinventum.buyers_hist.his_b_record_timestamp is 'History record creation time';

create index buyers_hist_idx_his_b_id on trinventum.buyers_hist (his_b_id);
comment on index buyers_hist_idx_his_b_id is
 'Index for searching buyer history by buyer ID';

create function trinventum.trg_buyers_proc()
returns trigger as
$trg_buyers_proc$
begin
	insert into trinventum.buyers_hist
	(his_b_id, his_b_name, his_b_postal_address, his_b_login,
	 his_b_email_address, his_b_comment, his_b_version)
	values
	(old.b_id, old.b_name, old.b_postal_address, old.b_login,
	 old.b_email_address, old.b_comment, old.b_version);

	return null;
end;
$trg_buyers_proc$ language plpgsql;

comment on function trinventum.trg_buyers_proc() is
 'Function for the trg_buyers trigger, which saves buyer record history';

create trigger trg_buyers
after update or delete on trinventum.buyers
for each row execute procedure trinventum.trg_buyers_proc();

comment on trigger trg_buyers on trinventum.buyers is
 'Trigger that saves buyer record history';

create table trinventum.transactions_hist
(
	his_t_id bigint,
	his_t_product_id integer,
	his_t_seller integer,
	his_t_buyer integer,
	his_t_price numeric (7,2),
	his_t_paid boolean,
	his_t_sent boolean,
	his_t_sell_date timestamp,
	his_t_send_price numeric (7,2),
	his_t_send_cost numeric (7,2),
	his_t_version integer,
	his_t_user text default current_user,
	his_t_record_timestamp timestamp not null default now()
);

comment on table trinventum.transactions_hist is 'The table for product transaction history';
comment on column trinventum.transactions_hist.his_t_id is 'Transaction ID';
comment on column trinventum.transactions_hist.his_t_product_id is 'ID of the product piece in the transaction';
comment on column trinventum.transactions_hist.his_t_seller is 'ID of the seller in the transaction';
comment on column trinventum.transactions_hist.his_t_buyer is 'ID of the buyer in the transaction';
comment on column trinventum.transactions_hist.his_t_price is 'Transaction price';
comment on column trinventum.transactions_hist.his_t_paid is 'Whether the product was paid for';
comment on column trinventum.transactions_hist.his_t_sent is 'Whether the product was sent to buyer';
comment on column trinventum.transactions_hist.his_t_sell_date is 'The transaction date';
comment on column trinventum.transactions_hist.his_t_send_price is 'The price of sending the product';
comment on column trinventum.transactions_hist.his_t_send_cost is 'The actual cost of sending the product';
comment on column trinventum.transactions_hist.his_t_version is 'Record version';
comment on column trinventum.transactions_hist.his_t_user is 'History record creation user';
comment on column trinventum.transactions_hist.his_t_record_timestamp is 'History record creation time';

create index transactions_hist_idx_his_t_id on trinventum.transactions_hist (his_t_id);
comment on index transactions_hist_idx_his_t_id is
 'Index for searching transaction history by transaction ID';

create function trinventum.trg_transactions_proc()
returns trigger as
$trg_transactions_proc$
begin
	insert into trinventum.transactions_hist
	(his_t_id, his_t_product_id, his_t_seller, his_t_buyer,
	 his_t_price, his_t_paid, his_t_sent, his_t_sell_date,
	 his_t_send_price, his_t_send_cost, his_t_version)
	values
	(old.t_id, old.t_product_id, old.t_seller, old.t_buyer,
	 old.t_price, old.t_paid, old.t_sent, old.t_sell_date,
	 old.t_send_price, old.t_send_cost, old.t_version);

	return null;
end;
$trg_transactions_proc$ language plpgsql;

comment on function trinventum.trg_transactions_proc() is
 'Function for the trg_transactions trigger, which saves transaction record history';

create trigger trg_transactions
after update or delete on trinventum.transactions
for each row execute procedure trinventum.trg_transactions_proc();

comment on trigger trg_transactions on trinventum.transactions is
 'Trigger that saves transaction record history';

create or replace function trinventum.months_ago(m integer)
returns integer as
$months_ago$
declare
	c integer;
begin
	execute 'select extract (month from (current_date - interval ''' || m || ' months''))' into c;
	return c;
end;
$months_ago$ language plpgsql;

comment on function trinventum.months_ago(m integer) is 'Returns the month number (1-12) that was ''m'' months ago';

create index products_idx_p_pd_id on trinventum.products (p_pd_id);
comment on index products_idx_p_pd_id is
 'Index for searching product pieces by product type';

create index transactions_idx_t_product_id on trinventum.transactions (t_product_id);
comment on index transactions_idx_t_product_id is
 'Index for searching transactions by product piece ID';

update trinventum.versions set db_version = '2';
