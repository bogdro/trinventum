<?php
	/*
	 * Trinventum - the legal information page.
	 *
	 * Copyright (C) 2024 Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net
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

	session_start();

	include_once 'inc/constants.php';
	include_once 'inc/functions.php';
	include_once 'inc/db_functions.php';

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<HTML lang="en">
<HEAD profile="http://www.w3.org/2005/10/profile">
<META HTTP-EQUIV="Content-Type"       CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="Content-Language"   CONTENT="en">
<?php
	trin_meta_lastmod ($t_lastmod);
	trin_include_css ();
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">

<TITLE> Trinventum - legal information </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/img/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
	if (trin_validate_session ())
	{
		include 'inc/header.php';
		include 'menu.php';
	}
?>

<h1 class="header">Legal information</h1>

<h2>Author</h2>
Bogdan 'bogdro' Drozdowski, bogdro (at) users . sourceforge . net

<h2>License</h2>
Trinventum is licensed under the <a href="rsrc/GNU-agpl-3.0-standalone.html" hreflang="en">GNU AGPL v3+</a>.

<h2>Trademarks</h2>

<p>
LINUX&reg; is a registered trademark of Linus Torvalds.
</p>

<p>
Postgres&reg; and PostgreSQL&reg; and the Elephant Logo (Slonik) are all registered trademarks of the
<a href="https://www.postgres.ca/" hreflang="en">PostgreSQL Community Association of Canada</a>.
</p>

<p>
Apache HTTP Server, Apache, and the Apache feather logo are trademarks of
<a href="https://www.apache.org/" hreflang="en">The Apache Software Foundation</a>.
</p>

<p>
All other trademarks, logos and names on this page and all subpages are properties
of their respective owners and are given here only as an example.
</p>


<div class="menu">
<a href="main.php">Return to the main page</a>
</div>

<?php
	include 'inc/footer.php';
?>

</BODY></HTML>
