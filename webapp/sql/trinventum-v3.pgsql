/*
 * Trinventum - the database script for database version 3.
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

alter table trinventum.buyers add constraint buyer_login_unique unique (b_login);
alter table trinventum.sellers add constraint seller_name_unique unique (s_name);
alter table trinventum.sellers add constraint seller_name_nonempty check (length(s_name) > 0);

create or replace function trinventum.year_months_ago(m integer)
returns integer as
$year_months_ago$
declare
	c integer;
begin
	execute 'select extract (year from (current_date - interval ''' || m || ' months''))' into c;
	return c;
end;
$year_months_ago$ language plpgsql;

comment on function trinventum.year_months_ago(m integer) is 'Returns the year that was ''m'' months ago';

update trinventum.versions set db_version = '3';

