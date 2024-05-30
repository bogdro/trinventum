# Trinventum Makefile
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

NAME = trinventum
VER = 1.0

RMDIR = /bin/rm -fr
# when using '-p', no error is generated when the directory exists
MKDIR = /bin/mkdir -p
COPY = /bin/cp -pRf
CHMOD = /bin/chmod

# Use the GNU tar format
# ifneq ($(shell tar --version | grep -i bsd),)
# PACK1_GNUOPTS = --format gnutar
# endif
PACK1 = /bin/tar $(PACK1_GNUOPTS) -vcf
PACK1_EXT = .tar

PACK2 = /usr/bin/gzip -9
PACK2_EXT = .gz

PHP_UNIT_TESTER = phpunit
PHP_UNIT_TEST_DIR = tests
PHP_UNIT_TESTER_ARGS = --testdox --colors=auto

ifeq ($(PREFIX),)
PREFIX = /srv/www/html
endif

ifeq ($(SERVERCONF),)
SERVERCONF = /etc/httpd/conf/webapps.d
endif

DOCS = AUTHORS ChangeLog COPYING INSTALL-*.txt README
EXTRA_DIST = $(DOCS) Makefile NEWS $(NAME).spec $(NAME)-app.conf

SUBDIRS = webapp scripts $(PHP_UNIT_TEST_DIR)

all:	dist

dist:	$(NAME)-$(VER)$(PACK1_EXT)$(PACK2_EXT)

$(NAME)-$(VER)$(PACK1_EXT)$(PACK2_EXT): $(EXTRA_DIST) \
		$(shell find $(SUBDIRS) -type f)
	$(RMDIR) $(NAME)-$(VER)
	$(MKDIR) $(NAME)-$(VER)
	$(COPY) $(EXTRA_DIST) $(SUBDIRS) $(NAME)-$(VER)
	$(RMDIR) $(NAME)-$(VER)$(PACK1_EXT)$(PACK2_EXT)
	$(PACK1) $(NAME)-$(VER)$(PACK1_EXT) $(NAME)-$(VER)
	$(PACK2) $(NAME)-$(VER)$(PACK1_EXT)
	$(RMDIR) $(NAME)-$(VER)

install:
	$(MKDIR) $(PREFIX)/$(NAME)
	$(MKDIR) $(SERVERCONF)
	$(COPY) webapp/* $(PREFIX)/$(NAME)/
	$(RMDIR) $(PREFIX)/$(NAME)/.htaccess $(PREFIX)/$(NAME)/inc/.htaccess
	$(COPY) $(NAME)-app.conf $(SERVERCONF)/
	$(CHMOD) 604 $(PREFIX)/$(NAME)/*.php $(SERVERCONF)/$(NAME)-app.conf \
		$(PREFIX)/$(NAME)/inc/* $(PREFIX)/$(NAME)/rsrc/img/* \
		$(PREFIX)/$(NAME)/rsrc/* $(PREFIX)/$(NAME)/sql/*
	$(CHMOD) 755 $(PREFIX)/$(NAME) $(PREFIX)/$(NAME)/inc \
		$(PREFIX)/$(NAME)/rsrc $(PREFIX)/$(NAME)/rsrc/img \
		$(PREFIX)/$(NAME)/sql
ifneq ($(DOCDIR),)
	$(MKDIR) $(DOCDIR)/$(NAME)
	$(CHMOD) 755 $(DOCDIR)/$(NAME)
	$(COPY) $(DOCS) $(DOCDIR)/$(NAME)/
endif

uninstall:
	$(RMDIR) $(PREFIX)/$(NAME)/
ifneq ($(DOCDIR),)
	$(RMDIR) $(DOCDIR)/$(NAME)/
endif

check:
	$(PHP_UNIT_TESTER) $(PHP_UNIT_TESTER_ARGS) $(PHP_UNIT_TEST_DIR)

.PHONY: all check dist install uninstall
