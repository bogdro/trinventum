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

/*
createdb trinventum
*/

create schema trinventum;

create type trinventum.t_gender as enum ('M', 'F', 'C', '-');
create type trinventum.t_status as enum ('READY', 'SELLING', 'SOLD');

create sequence trinventum.seq_pd_id;

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

create table trinventum.products
(
	p_id serial primary key,
	p_pd_id bigint not null references trinventum.product_definitions (pd_id),
	p_status trinventum.t_status not null default 'READY',
	p_cost numeric (7,2) not null default 0.0 check (p_cost >= 0.0)
);

create table trinventum.sellers
(
	s_id serial primary key,
	s_name text not null
);

create table trinventum.buyers
(
	b_id serial primary key,
	b_name text not null,
	b_postal_address text not null,
	b_login text not null,
	b_email_address text not null,
	b_comment text
);

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

create table trinventum.versions
(
	db_version text not null
);

insert into trinventum.versions (db_version) values ('1');

