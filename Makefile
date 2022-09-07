# Trinventum Makefile
#
# Copyright (C) 2022 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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
VER = 0.5

RMDIR = /bin/rm -fr
# when using '-p', no error is generated when the directory exists
MKDIR = /bin/mkdir -p
COPY = /bin/cp -pRf
CHMOD = /bin/chmod

PACK1 = /bin/tar --format gnutar -vcf
PACK1_EXT = .tar

PACK2 = /usr/bin/gzip -9
PACK2_EXT = .gz

SUBDIRS = webapp scripts

all:	dist

dist:	$(NAME)-$(VER)$(PACK1_EXT)$(PACK2_EXT)

$(NAME)-$(VER)$(PACK1_EXT)$(PACK2_EXT): AUTHORS ChangeLog COPYING NEWS \
		README INSTALL-Trinventum.txt Makefile \
		$(shell find $(SUBDIRS) -type f)
	$(RMDIR) $(NAME)-$(VER)
	$(MKDIR) $(NAME)-$(VER)
	$(COPY) AUTHORS ChangeLog COPYING INSTALL-Trinventum.txt NEWS \
		README Makefile $(SUBDIRS) \
		$(NAME)-$(VER)
	$(PACK1) $(NAME)-$(VER)$(PACK1_EXT) $(NAME)-$(VER)
	$(PACK2) $(NAME)-$(VER)$(PACK1_EXT)
	$(RMDIR) $(NAME)-$(VER)

install:
	$(MKDIR) $(PREFIX)/$(NAME)
	$(COPY) webapp/* webapp/.htaccess $(PREFIX)/$(NAME)/
	$(CHMOD) 604 $(PREFIX)/$(NAME)/*.php $(PREFIX)/$(NAME)/.htaccess \
		$(PREFIX)/$(NAME)/sql/* $(PREFIX)/$(NAME)/rsrc/*
	$(CHMOD) 705 $(PREFIX)/$(NAME) $(PREFIX)/$(NAME)/sql $(PREFIX)/$(NAME)/rsrc

.PHONY: all dist install
