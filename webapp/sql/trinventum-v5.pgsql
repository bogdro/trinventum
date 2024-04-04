/*
 * Trinventum - the database script for database version 5.
 *
 * Copyright (C) 2022-2024 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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
Required for indices, triggers and their comments.
*/
set schema 'trinventum';

create or replace function trinventum.trg_protect_history()
returns trigger as
$trg_protect_history$
begin
	raise 'Modifying history not allowed'
		using hint = 'Do not perform an UPDATE or DELETE on the table',
		errcode = 'prohibited_sql_statement_attempted';
	return null;
end;
$trg_protect_history$ language plpgsql;

comment on function trinventum.trg_protect_history() is
 'Function for the triggers that prevent from modifying history data.';

create trigger trg_protect_product_categories_hist
before update or delete or truncate on trinventum.product_categories_hist
execute procedure trinventum.trg_protect_history();

comment on trigger trg_protect_product_categories_hist on trinventum.product_categories_hist is
 'Trigger that prevents modification of product category history';

create trigger trg_protect_product_definitions_hist
before update or delete or truncate on trinventum.product_definitions_hist
execute procedure trinventum.trg_protect_history();

comment on trigger trg_protect_product_definitions_hist on trinventum.product_definitions_hist is
 'Trigger that prevents modification of product definition history';

create trigger trg_protect_products_hist
before update or delete or truncate on trinventum.products_hist
execute procedure trinventum.trg_protect_history();

comment on trigger trg_protect_products_hist on trinventum.products_hist is
 'Trigger that prevents modification of product history';

create trigger trg_protect_sellers_hist
before update or delete or truncate on trinventum.sellers_hist
execute procedure trinventum.trg_protect_history();

comment on trigger trg_protect_sellers_hist on trinventum.sellers_hist is
 'Trigger that prevents modification of seller history';

create trigger trg_protect_buyers_hist
before update or delete or truncate on trinventum.buyers_hist
execute procedure trinventum.trg_protect_history();

comment on trigger trg_protect_buyers_hist on trinventum.buyers_hist is
 'Trigger that prevents modification of buyer history';

create trigger trg_protect_transactions_hist
before update or delete or truncate on trinventum.transactions_hist
execute procedure trinventum.trg_protect_history();

comment on trigger trg_protect_transactions_hist on trinventum.transactions_hist is
 'Trigger that prevents modification of transaction history';

update trinventum.versions set db_version = '5';
