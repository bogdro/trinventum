# Trinventum RPM spec file
#
# Copyright (C) 2022-2024 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
#
# This file is part of Trinventum (Transaction and Inventory Unified Manager),
#  a software that helps manage an e-commerce business.
# Trinventum homepage: https://trinventum.sourceforge.io/
#
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
#
#  You should have received a copy of the GNU Affero General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#

# Special names here like {__make} come from /usr/lib/rpm/macros, /usr/lib/rpm/macros.rpmbuild

%define trin_version 1.0
%define trin_release 1
%define trin_name trinventum
%define trin_url https://trinventum.sourceforge.io
%define trin_lic AGPLv3
%define trin_summary Transaction and Inventory Unified Manager
%define trin_descr Trinventum (Transaction and Inventory Unified Manager) \
is a software that helps manage an e-commerce business. Trinventum allows \
you to register and modify products for sale, add and modify buyers, \
sellers and transactions.

%define trin_server_app_path /srv/www/html
%define trin_server_conf_path /etc/httpd/conf/webapps.d

# define _unpackaged_files_terminate_build 0
%define _enable_debug_packages 0

Summary:	%{trin_summary}
Name:		%{trin_name}
Version:	%{trin_version}
Release:	%{trin_release}
URL:		%{trin_url}
BugURL:		%{trin_url}
License:	%{trin_lic}
# group must be one of the listed in /usr/share/doc/rpm-.../GROUPS or /usr/share/rpmlint/config.d/*.conf
Group:		Office
Source:		%{trin_name}-%{trin_version}.tar.gz
#BuildRoot:	{_tmppath}/{trin_name}-build
Requires:	php-cgi
BuildArch:	noarch
BuildRequires:	make

%description
%{trin_descr}

%prep
%setup -q

%build

#post

#postun

%install

%make_build install \
	PREFIX=%{buildroot}/%{trin_server_app_path} \
	SERVERCONF=%{buildroot}/%{trin_server_conf_path} \
	DOCDIR=%{buildroot}/%{_datadir}/doc

%clean

%{__rm} -rf %{buildroot}

%files

%defattr(-,root,root)
%doc AUTHORS
%doc COPYING
%doc ChangeLog
%doc INSTALL-Trinventum.txt
%doc README
%config(noreplace) %attr(644,-,-) %{trin_server_conf_path}/%{trin_name}-app.conf
%attr(644,-,-) %{trin_server_app_path}/%{trin_name}/*.php
%attr(644,-,-) %{trin_server_app_path}/%{trin_name}/inc/*
%attr(644,-,-) %{trin_server_app_path}/%{trin_name}/rsrc/*.html
%attr(644,-,-) %{trin_server_app_path}/%{trin_name}/rsrc/*.css
%attr(644,-,-) %{trin_server_app_path}/%{trin_name}/rsrc/img/*
%attr(644,-,-) %{trin_server_app_path}/%{trin_name}/sql/*
%dir %attr(755,-,-) %{trin_server_app_path}/%{trin_name}
%dir %attr(755,-,-) %{trin_server_app_path}/%{trin_name}/inc
%dir %attr(755,-,-) %{trin_server_app_path}/%{trin_name}/rsrc
%dir %attr(755,-,-) %{trin_server_app_path}/%{trin_name}/rsrc/img
%dir %attr(755,-,-) %{trin_server_app_path}/%{trin_name}/sql

%changelog
