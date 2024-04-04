/*
 * Trinventum - the database script for database version 4.
 *
 * Copyright (C) 2016-2024 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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
Required for indices and their comments.
*/
set schema 'trinventum';

create table trinventum.product_categories
(
	pc_id serial primary key,
	pc_name text not null,
	pc_version integer not null default 1 check (pc_version >= 1)
);

comment on table trinventum.product_categories is 'The table for product categories';
comment on column trinventum.product_categories.pc_id is 'Product category ID (assigned automatically)';
comment on column trinventum.product_categories.pc_name is 'Product category name';
comment on column trinventum.product_categories.pc_version is 'Record version';

create table trinventum.product_categories_hist
(
	his_pc_id integer,
	his_pc_name text not null,
	his_pc_version integer,
	his_pc_user text default current_user,
	his_pc_record_timestamp timestamp not null default now()
);

comment on table trinventum.product_categories_hist is 'The table for product category history';
comment on column trinventum.product_categories_hist.his_pc_id is 'Product category ID';
comment on column trinventum.product_categories_hist.his_pc_name is 'Product category name';
comment on column trinventum.product_categories_hist.his_pc_version is 'Record version';
comment on column trinventum.product_categories_hist.his_pc_user is 'History record creation user';
comment on column trinventum.product_categories_hist.his_pc_record_timestamp is 'History record creation time';

create index product_categories_hist_idx_his_pc_id on trinventum.product_categories_hist (his_pc_id);
comment on index product_categories_hist_idx_his_pc_id is
 'Index for searching product category history by category piece ID';

create function trinventum.trg_product_categories_proc()
returns trigger as
$trg_product_categories_proc$
begin
	insert into trinventum.product_categories_hist
	(his_pc_id, his_pc_name, his_pc_version)
	values
	(old.pc_id, old.pc_name, old.pc_version);

	return null;
end;
$trg_product_categories_proc$ language plpgsql;

comment on function trinventum.trg_product_categories_proc() is
 'Function for the trg_product_categories trigger, which saves product category record history';

create trigger trg_product_categories
after update or delete on trinventum.product_categories
for each row execute procedure trinventum.trg_product_categories_proc();

comment on trigger trg_product_categories on trinventum.product_categories is
 'Trigger that saves products category history';

insert into trinventum.product_categories (pc_id, pc_name) values (0, 'Uncategorised products');

alter table trinventum.product_definitions add pd_pc_id integer not null default 0
 references trinventum.product_categories (pc_id);

comment on column trinventum.product_definitions.pd_pc_id is 'Product category';

alter table trinventum.product_definitions_hist add his_pd_pc_id integer;
comment on column trinventum.product_definitions_hist.his_pd_comment is 'Product category';

create or replace function trinventum.trg_product_definitions_proc()
returns trigger as
$trg_product_definitions_proc$
begin
	insert into trinventum.product_definitions_hist
	(his_pd_id, his_pd_name, his_pd_photo, his_pd_size,
	 his_pd_length, his_pd_width, his_pd_colour, his_pd_brand,
	 his_pd_gender, his_pd_comment, his_pd_pc_id, his_pd_version)
	values
	(old.pd_id, old.pd_name, old.pd_photo, old.pd_size,
	 old.pd_length, old.pd_width, old.pd_colour, old.pd_brand,
	 old.pd_gender, old.pd_comment, old.pd_pc_id, old.pd_version);

	return null;
end;
$trg_product_definitions_proc$ language plpgsql;

update trinventum.versions set db_version = '4';
