# Special names here like {__make} come from /usr/lib/rpm/macros, /usr/lib/rpm/macros.rpmbuild

%define trin_version 0.7
%define trin_release 1
%define trin_name trinventum
%define trin_url https://trinventum.sourceforge.io
%define trin_lic AGPLv3
%define trin_summary Transaction and Inventory Unified Manager
%define trin_descr Trinventum (Transaction and Inventory Unified Manager) \
is a software that helps manage an e-commerce business. Trinventum allows \
you to register and modify products for sale, add and modify buyers, \
sellers and transactions.

%define trin_server_path /srv/www/html

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
	PREFIX=%{buildroot}/%{trin_server_path} \
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
%dir %attr(755,-,-) %{trin_server_path}/%{trin_name}
%dir %attr(755,-,-) %{trin_server_path}/%{trin_name}/sql
%dir %attr(755,-,-) %{trin_server_path}/%{trin_name}/rsrc
%attr(644,-,-) %{trin_server_path}/%{trin_name}/*.php
%attr(644,-,-) %{trin_server_path}/%{trin_name}/.htaccess
%attr(644,-,-) %{trin_server_path}/%{trin_name}/sql/*
%attr(644,-,-) %{trin_server_path}/%{trin_name}/rsrc/*

%changelog
