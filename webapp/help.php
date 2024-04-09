<?php
	/*
	 * Trinventum - the help page.
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

	session_start();

	include_once 'constants.php';
	include_once 'functions.php';
	include_once 'db_functions.php';

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

<TITLE> Trinventum - help </TITLE>
<link rel="icon" type="image/svg+xml" href="rsrc/trinventum-icon.svg">

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

<?php
	if (trin_validate_session ())
	{
		include 'header.php';
		include 'menu.php';
	}
?>

<h1 class="header">Installation and usage instructions</h1>

<h2>Installation - initial steps</h2>

To use Trinventum, the following steps must be performed:
<ol>
 <li>A supported database (currently: <a href="https://www.postgresql.org/" hreflang="en">PostgreSQL</a>) must be running.<br>
	On a Linux system, you would do one of:
	<ul>
	 <li>login to the system as the database user <code>postgres</code> (you can
		do <code>su - postgres</code> as <code>root</code> to log in to that account on Linux) and run:
	  <pre>	pg_ctl start</pre>
	  </li>
	 <li>login as <code>root</code> and run:
	  <pre>	systemctl start postgresqlXX (XX being the version)</pre>
	  </li>
	 <li>login as <code>root</code> and run:
	  <pre>	service postgresql start</pre>
	  </li></ul>
	</li>

 <li>A web (HTTP) server with <a href="https://www.php.net" hreflang="en">PHP</a> support
 	(like the <a href="https://httpd.apache.org" hreflang="en">Apache HTTP Server</a> with mod_php installed) must be running.<br>
	On a Linux system, you would do one of (as <code>root</code>):
	<pre>
	systemctl start httpd-prefork</pre>
	<pre>
	service httpd start</pre>
	</li>

 <li>A database user must be created within the database server.<br>
	On PostgreSQL, login to the system as the database user <code>postgres</code> and run:
	<pre>
	createuser -P trinventum</pre>
	It's advised for the user to be the owner of the database that will be created.<br>
	It's easiest to create a user with the same name as the system user name and
	create the database as the system user.<br>
	You can create other database users later, and give them full or less privileges
	so that those users can execute all the activities or just the ones they have
	access to.
	<br><br></li>

 <li>A logical database must be created within the database server.<br>
	On PostgreSQL, login to the system as the database user <code>postgres</code> and run:
	<pre>
	createdb -O trinventum trinventum</pre>
	(for the specified user to be the database owner)
	<br><br></li>

 <li>A procedural language suitable for the database must be installed in the logical database.<br>
	On PostgreSQL versions earlier than 9.0, login to the system as the database user <code>postgres</code> and run:
	<pre>
	createlang plpgsql trinventum</pre>
	(don't worry if it says that the language already exists).<br>
	Later PostgreSQL have the language already built-in by default.
	<br><br></li>

 <li>Database access rules must be created within the database server unless you wish to leave the default rules.<br>
	On PostgreSQL, you would do (as the database user <code>postgres</code> or <code>root</code>):
	<pre>
	cp /var/lib/pgsql/data/pg_hba.conf /var/lib/pgsql/data/pg_hba.conf-backup
	echo local trinventum trinventum scram-sha-256 &gt;&gt; /var/lib/pgsql/data/pg_hba.conf
	echo host all all 127.0.0.1/32 scram-sha-256 &gt;&gt; /var/lib/pgsql/data/pg_hba.conf</pre>
	(Note the double "<code>&gt;&gt;</code>" - it's <em class="important">CRUCIAL</em> to use double "<code>&gt;&gt;</code>",
	a single "<code>&gt;</code>" would <em class="important">OVERWRITE</em> the target file).<br>
	On older PostgreSQL versions replace <code>scram-sha-256</code> with <code>md5</code>.<br>
	If you'll need to access the database from another computer:
	<ul>
	 <li>a line similar to the <code>host all all 127.0.0.1/32 scram-sha-256</code>
	  should be added to pg_hba.conf, containing the correct IP address</li>
	 <li>firewall rules may need to be adjusted</li>
	 <li>the <code>/etc/hosts.allow</code> file (tcpwrappers) may need to be adjusted</li>
	</ul><br></li>

 <li>After changing the access rules for the database server, restart it.<br>
	On Linux with PostgreSQL, you would do one of:
	<ul>
	 <li>login to the system as the database user <code>postgres</code> and run:
	  <pre>	pg_ctl reload</pre>
	 </li>
	 <li>login as <code>root</code> and run:
	  <pre>	systemctl restart postgresqlXX (XX being the version)</pre>
	 </li>
	 <li>login as <code>root</code> and run:
	  <pre>	service postgresql restart</pre>
	 </li></ul></li>

</ol>

<hr>

<h2>Installation - web application</h2>

To install the web application part:

<ol>
 <li><p>Copy the contents of the <code>webapp</code> directory (<em class="important">including hidden files</em>) to a
  chosen location reachable by the web server and adjust the access modes accordingly so that the web server
  can actually run the files.</p>
  <p>
  Alternatively, if you have the <code>make</code> program and some common Linux
  utilities, you can run <code>make install</code>, passing the prefix of the target
  directory. Trinventum will be installed in <code>$(PREFIX)/trinventum</code>. Examples:</p>
	<pre>
	make install PREFIX=/srv/www/html
	make install PREFIX=/var/www/html
	make install PREFIX=$HOME/public_html</pre>
	</li>

  <p>To install the documentation, you can add a chosen directory as the <code>DOCDIR</code>
  parameter to <code>make install</code>. Documentation will be installed in
  <code>$(DOCDIR)/trinventum</code>. Examples:</p>
	<pre>
	make install PREFIX=/srv/www/html DOCDIR=/usr/share/doc
	make install PREFIX=/var/www/html DOCDIR=/usr/share/doc
	make install PREFIX=$HOME/public_html DOCDIR=$HOME/tools/doc</pre>
	</li>

 <li>The necessary database structures will be created by the application
  itself upon the first successful login of a user <em class="important">with full access to the database schema</em>
  (like the schema owner <code>trinventum</code> created earlier).<br>
  There is <em class="important">NO NEED</em> to run any SQL scripts manually.</li>
</ol>

If you're reading this in a browser through a web server, this step most probably succeeded.

<hr>

<h2>Usage</h2>

<?php
	$login_link = trin_html_escape($_SERVER['REQUEST_SCHEME'])
		. '://' . trin_html_escape($_SERVER['HTTP_HOST'])
		. dirname(trin_html_escape($_SERVER['PHP_SELF']))
		. '/login.php';
?>
After having done the installation, point your browser to the Trinventum login page, like
<a href="<?php echo $login_link; ?>"><?php echo $login_link; ?></a>.
You enter 4 parameters there:
<ol>
 <li>the username of the DATABASE (not system) user you've created</li>
 <li>the password of the DATABASE user you've created</li>
 <li>the database address: IP address or hostname, can also be the local socket's
 	directory (<code>/run/postgresql</code> in some versions, <code>/tmp</code> in other)
	if the database is running locally using the default settings</li>
 <li>the database name, which would be <code>trinventum</code></li>
</ol>

<p>
After the first successful logon as a user
<em class="important">with full access to the database schema</em>
(like the schema owner <code>trinventum</code> created earlier),
the database structures are created. If the current database
needs to be upgraded, the required scripts are run.
</p>

<p>
Each upgrade also requires that a user <em class="important">with full access
 to the database schema</em> logs in to the application before others.
</p>

<p>
After logging in to the application, you're presented with the list of currently-existing
categories of product types (there will be one during the first run - Uncategorised products).
</p>

<p>
Use the "Categories" link to add new product type categories (this is optional).
</p>

<p>
To add a new product, use the "Add a new product" link. Supply all the details and press "Add product".
Remember that the photos you add will take at least the same amount of space in the database as on disk,
so keep the photographs smaller than usual. You can use the <code>convert</code> utility from the
<a href="https://imagemagick.org" hreflang="en">ImageMagick</a> package to shrink image sizes:
</p>
<pre>
	convert original.jpg -resize 50% smaller_copy.jpg</pre>
<p>
The "50%" means to shrink both the width and height to 50% of the original size (thus effectively
making the image have 4 times fewer pixels). To shrink all images in the current directory, run
</p>
<pre>
	for i in *.jpg; do convert "$i" -resize 50% "$i-small.jpg"; done</pre>
<p>
This utility can also change the image format (you can convert from JPEG to other picture formats,
like PNG).
</p>

<p>
If everything goes well, you'll be redirected back to the main page with the new product already
visible.
</p>

<p>
Click the product ID or the photo (intentionally shrunk) to modify the product type.
</p>

<p>
You'll see the current product details (click the photo to see it in its full size)
and a form to modify the details.
Depending on the settings, you can perform one of these operations:
</p>
<ol>
 <li>Update all the product details at the same time.
 	<p>
	Due to HTML/browser limitations, the photo field will be empty
	(so you need to upload the same photo each time when
	you want to update the details).
	</p>
	<p>
	The rest of the details will be pre-filled. Change the required
	fields and press "Update product". All the data (including the photo) will be modified by this
	operation.
	</p>
	</li>
 <li>Update the product details one by one.<br>
	You can use the "Update" button in every field to change the value of
	just that field in the product.
	</li>
</ol>
<p>
In both cases:
</p>
<ul>
 <li>if you modify the cost, the cost of all product pieces will be modified,</li>
 <li>you cannot decrease the number of product pieces, but you can increase it.</li>
</ul>

<p>
Below the product type details, you'll see a list of current product pieces of the given type.
To modify a piece, click on its ID. You can only change the status between READY and SELLING
(which means that the product is put for sale). When the piece is SOLD, you can't change it back
to READY or SELLING, you can update only the cost then, unless:
</p>
<ul>
 <li>the piece's transaction is modified to actually select another piece, or</li>
 <li>the transaction is deleted, in which case the product piece's status is
 changed automatically to READY.</li>
</ul>

<p>
Below the list of product pieces you'll see a list of product sales, showing which buyers
bought the pieces and which sellers sold them. You can also see the history of the changes
made to the product definition.
</p>

<p>
Use the "Manage sellers" and "Manage buyers" to add and modify buyers and sellers, required
to register transactions.
</p>

<p>
Use the "Manage transactions" to see a list of currently-registered transactions
(the "List all transactions" link) and register new ones (the "Register a new transaction" link).
</p>

<p>
After clicking "Register a new transaction", you'll be presented with a transaction form.
</p>

<p>
If modifying the product which took part in the transaction is allowed,
first you choose the product type. Next, you select a product piece. You can only select
products which are in the SELLING state (you have to set that state manually).
</p>

<p>
Then you input the rest of the transaction parameters:
</p>
<ul>
 <li>the buyer (selected from the list of registered buyers)</li>
 <li>the seller (selected from the list of registered sellers)</li>
 <li>selling price</li>
 <li>paying status (pain/unpaid)</li>
 <li>sending status (sent/unsent)</li>
 <li>selling date</li>
 <li>sending price</li>
 <li>sending cost</li>
</ul>

<p>
If you get "Record version doesn't match" errors (anywhere, not just in transactions),
then someone must have updated the object you're working on. Refresh the page,
double-check the values, re-enter your changes and retry the operation.
</p>

<p>
After finishing work, click the "Logout" link to clean up the session on the server.
Depending on the server settings, you may get logged-out automatically after some period
of inactivity. See the <code>session.cookie_lifetime</code> entry in your php.ini
file (<code>/etc/php.ini</code> on Linux) to see how long does the session cookie live for (in seconds).
</p>

<hr>

<h2>Management</h2>

<p>
There is a <a href="management.php">dedicated management page</a> with some
of the activities you may wish to perform, including database queries.
</p>
<p>
To backup the database, for PostgreSQL, you would do:
</p>
	<pre>
	pg_dump -Fc trinventum > trinventum.dmp</pre>
<p>
and provide the DATABASE user password.
</p>

<p>
To restore the database, for PostgreSQL, you would do:
</p>
	<pre>
	pg_restore -d trinventum trinventum.dmp</pre>
<p>
and provide the DATABASE user password.
</p>

<p>
To delete and re-create the database schema, run (for PostgreSQL):
</p>
	<pre>
	bash$ psql trinventum
	trinventum=&gt; drop schema trinventum cascade; quit;</pre>
<p>
After this, you need to re-login to Trinventum as a user
<em class="important">with full access to the database schema</em>
(like the schema owner <code>trinventum</code> created earlier)
to re-create the structures.
</p>

<p>
To delete and re-crete the whole database, run (for PostgreSQL,
login to the system as the database user <code>postgres</code> and run):
</p>
	<pre>
	dropdb trinventum
	createdb trinventum</pre>
<p>
After this, you need to re-login to Trinventum as a user
<em class="important">with full access to the database schema</em>
(like the schema owner <code>trinventum</code> created earlier)
to re-create the structures.
</p>

<div class="menu">
<a href="main.php">Return to the main page</a>
</div>

<?php
		include 'footer.php';
?>

</BODY></HTML>
