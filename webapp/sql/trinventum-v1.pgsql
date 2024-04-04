/*
 * Trinventum - the database script for database version 1.
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

/*
createdb trinventum
*/

create schema trinventum;
comment on schema trinventum is 'The main schema for the Trinventum application';

create type trinventum.t_gender as enum ('M', 'F', 'C', '-');
comment on type trinventum.t_gender is 'The gender/sex enumeration';

create type trinventum.t_status as enum ('READY', 'SELLING', 'SOLD');
comment on type trinventum.t_status is 'The possible product piece statuses';

create sequence trinventum.seq_pd_id;
comment on sequence trinventum.seq_pd_id is 'The product definition ID sequence';

create table trinventum.product_definitions
(
	pd_id bigint primary key,
	pd_name text not null,
	pd_photo bytea,
	pd_size text not null default 'N/A',
	pd_length numeric (7,2) not null default 0.0,
	pd_width numeric (7,2) not null default 0.0,
	pd_colour text,
	pd_brand text not null default 'NoName',
	pd_gender trinventum.t_gender not null default '-',
	pd_comment text
);

comment on table trinventum.product_definitions is 'The table for product definitions/types';
comment on column trinventum.product_definitions.pd_id is 'Product definition ID, taken from trinventum.seq_pd_id';
comment on column trinventum.product_definitions.pd_name is 'Product name';
comment on column trinventum.product_definitions.pd_photo is 'Product binary photo data';
comment on column trinventum.product_definitions.pd_size is 'Product size';
comment on column trinventum.product_definitions.pd_length is 'Product length';
comment on column trinventum.product_definitions.pd_width is 'Product width';
comment on column trinventum.product_definitions.pd_colour is 'Product colour';
comment on column trinventum.product_definitions.pd_brand is 'Product brand name';
comment on column trinventum.product_definitions.pd_gender is 'Product target gender/sex';
comment on column trinventum.product_definitions.pd_comment is 'Product comment/description';

create table trinventum.products
(
	p_id serial primary key,
	p_pd_id bigint not null references trinventum.product_definitions (pd_id),
	p_status trinventum.t_status not null default 'READY',
	p_cost numeric (7,2) not null default 0.0 check (p_cost >= 0.0)
);

comment on table trinventum.products is 'The table for individual product instances/pieces';
comment on column trinventum.products.p_id is 'Product piece ID (assigned automatically)';
comment on column trinventum.products.p_pd_id is 'Product type of the given product piece';
comment on column trinventum.products.p_status is 'Product piece''s status';
comment on column trinventum.products.p_cost is 'Product piece''s individual cost';

create table trinventum.sellers
(
	s_id serial primary key,
	s_name text not null
);

comment on table trinventum.sellers is 'The table for product sellers';
comment on column trinventum.sellers.s_id is 'Seller ID (assigned automatically)';
comment on column trinventum.sellers.s_name is 'Seller name';

create table trinventum.buyers
(
	b_id serial primary key,
	b_name text not null,
	b_postal_address text not null,
	b_login text not null,
	b_email_address text not null,
	b_comment text
);

comment on table trinventum.buyers is 'The table for product buyers';
comment on column trinventum.buyers.b_id is 'Buyer ID (assigned automatically)';
comment on column trinventum.buyers.b_name is 'Buyer''s name';
comment on column trinventum.buyers.b_postal_address is 'Buyer''s postal address';
comment on column trinventum.buyers.b_login is 'Buyer''s login (short name)';
comment on column trinventum.buyers.b_email_address is 'Buyer''s e-mail address';
comment on column trinventum.buyers.b_comment is 'Buyer''s description/comment';

create table trinventum.transactions
(
	t_id serial primary key,
	t_product_id integer not null references trinventum.products (p_id),
	t_seller integer not null references trinventum.sellers (s_id),
	t_buyer integer not null references trinventum.buyers (b_id),
	t_price numeric (7,2) not null check (t_price >= 0.0),
	t_paid boolean not null,
	t_sent boolean not null,
	t_sell_date timestamp not null default now(),
	t_send_price numeric (7,2) not null default 0.0 check (t_send_price >= 0.0),
	t_send_cost numeric (7,2) not null default 0.0 check (t_send_cost >= 0.0)
);

comment on table trinventum.transactions is 'The table for product buy/sell transactions';
comment on column trinventum.transactions.t_id is 'Transaction ID (assigned automatically)';
comment on column trinventum.transactions.t_product_id is 'ID of the product piece in the transaction';
comment on column trinventum.transactions.t_seller is 'ID of the seller in the transaction';
comment on column trinventum.transactions.t_buyer is 'ID of the buyer in the transaction';
comment on column trinventum.transactions.t_price is 'Transaction price';
comment on column trinventum.transactions.t_paid is 'Whether the product was paid for';
comment on column trinventum.transactions.t_sent is 'Whether the product was sent to buyer';
comment on column trinventum.transactions.t_sell_date is 'The transaction date';
comment on column trinventum.transactions.t_send_price is 'The price of sending the product';
comment on column trinventum.transactions.t_send_cost is 'The actual cost of sending the product';

create table trinventum.versions
(
	db_version text not null
);

comment on table trinventum.versions is 'The table containing the database version';
comment on column trinventum.versions.db_version is 'The current database version';

insert into trinventum.versions (db_version) values ('1');

