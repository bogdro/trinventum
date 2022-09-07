<?php
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

	session_start();

	include_once ('constants.php');
	include_once ('functions.php');
	include_once ('db_functions.php');

	$t_lastmod = getlastmod ();
	trin_header_lastmod ($t_lastmod);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<HTML lang="en">
<HEAD>
<META HTTP-EQUIV="Content-Type"       CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="Content-Language"   CONTENT="en">
<?php
	trin_meta_lastmod ($t_lastmod);
?>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<META HTTP-EQUIV="X-Frame-Options"    CONTENT="DENY">
<LINK rel="stylesheet" type="text/css" href="trinventum.css">

<TITLE> Trinventum - help </TITLE>

<META NAME="Author" CONTENT="Bogdan D.">
<META NAME="Description" CONTENT="Trinventum e-commerce manager">
<META NAME="Language" CONTENT="en">
<META NAME="Generator" CONTENT="KWrite/Kate; www.kate-editor.org">

</HEAD><BODY>

To use Trinventum, the following steps must be performed:
<ol>
 <li>a supported database (currently: PostgreSQL) must be running.<br>
	On a Linux system, you would do:
	<pre>
	service postgresql start</pre>
	</li>

 <li>a web (HTTP) server with PHP support (like Apache httpd with mod_php installed) must be running.<br>
	On a Linux system, you would do:
	<pre>
	service httpd start</pre>
	</li>

 <li>a database user must be created within the database server.<br>
	On PostgreSQL, you would login to the system as the database user (usually do
	<code>su - postgres</code> as root) and do:
	<pre>
	createuser -S -l -P -d -R -I some_username</pre>
	It's advised for the user to be the owner of the database that will be created.<br>
	It's easiest to create a user with the same name as the system user name and
	create the database as the system user.
	<br><br></li>

 <li>a logical database must be created within the database server.<br>
	On PostgreSQL, you would do:
	<pre>
	createdb trinventum</pre>
	(for the current user to be the database owner) or
	<pre>
	createdb -O some_username trinventum</pre>
	(for the specified user to be the database owner)
	</li>

 <li>database access rules must be created within the database server.<br>
	On PostgreSQL, you would do (change XXXX to the database username created earlier):
	<pre>
	cp /var/lib/pgsql/data/pg_hba.conf ~
	echo local trinventum XXXX md5 &gt;&gt; /var/lib/pgsql/data/pg_hba.conf
	echo host all all 127.0.0.1/32 md5 &gt;&gt; /var/lib/pgsql/data/pg_hba.conf</pre>
	(Note the double "&gt;&gt;" - it's CRUCIAL to use double "&gt;",
	a single "&gt;" would OVERWRITE the target file).<br>
	If you'll need to access the database from another computer:
	<ul>
	 <li>a line similar to the <code>host all all 127.0.0.1/32 md5</code>
	  should be added to pg_hba.conf, containing the correct IP address</li>
	 <li>firewall rules may need to be adjusted</li>
	 <li>the <code>/etc/hosts.allow</code> file (tcpwrappers) may need to be adjusted</li>
	</ul></li>

</ol>
After having done all of this, point your browser to the Trinventum login page, like
<?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/login.php'; ?>. You enter 4 parameters there:
<ol>
 <li>the username of the DATABASE (not system) user you've created</li>
 <li>the password of the DATABASE user you've created</li>
 <li>the database address (IP address or hostname, can also be "/tmp" if the database
 	is running locally using the default settings)</li>
 <li>the database name, which would be <code>trinventum</code></li>
</ol>

<p>
After the first successful login, the database structures are created. If the current database
needs to be upgraded, the required scripts are run.
</p>

<p>
After logging in to the application, you're presented with the list of currently-existing product
types (there will be none during the first run).
</p>

<p>
To add a new product, use the "Add a new product" link. Supply all the details and press "Add product".
Remember that the photos you add will take at least the same amount of space in the database as on disk,
so keep the photographs smaller than usual. You can use the <code>convert</code> utility from the
ImageMagick package to shink image sizes:
</p>
<pre>
	convert original.jpg -resize 50% smaller_copy.jpg</pre>
<p>
The "50%" means to shrink both the width and height to 50% of the original size (thus effectively
making the image have 4 times less pixels). To shrink all images in the current directory, run
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
Click the product ID or the photo (intentionally shrinked) to modify the product type.
</p>

<p>
You'll see the current product details (click the photo to see it in its full size)
and a form to modify the details. Due to HTML/browser
limitations, the photo field will be empty (so you need to upload the same photo each time when
you want to update the details). The rest of the details will be pre-filled. Change the required
fields and press "Update product". All the data (including the photo) will be modified by this
operation. If you modify the cost, the cost of all product pieces will be modifed.
You cannot decrease the number of product pieces, but you can increase it.
</p>

<p>
Below the product type details, you'll see a list of current product pieces of the given type.
To modify a piece, click on its ID. You can only change the status between READY and SELLING
(which means that the product is put for sale). When the piece is SOLD, you can't change it back
to READY or SELLING, you can update only the cost then.
</p>

<p>
Below the list of product pieces you'll see a list of product sales, showing which buyers
bought the pieces.
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
First, you choose the product type. Next, you select a product piece. You can only select
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
To delete and re-crete the database, run (for PostgreSQL):
</p>
	<pre>
	dropdb trinventum
	createdb trinventum</pre>



<?php
		include ('footer.php');
?>

</BODY></HTML>
