# Trinventum #

## Description ##

Trinventum (Transaction and Inventory Unified Manager) is a software that helps manage an e-commerce business.

Trinventum allows to:

-   register and modify products for sale (description, price, parameters, photo),
-   add and modify buyers,
-   add and modify sellers,
-   add, modify and delete transactions.

Requirements:

-   a web server with PHP and .htaccess support (the Apache HTTP Server is preferred: <https://httpd.apache.org>),
-   a PostgreSQL database server (<https://www.postgresql.org/>) with the PL/pgSQL language installed,
-   a PHP (<https://www.php.net>) interpreter in the web server with the PostgreSQL driver.

Trinventum homepage: <https://trinventum.sourceforge.io/>

----------------------------------------------------------------

## Compatibility ##

Various versions of Trinventum were successfully used with the following components in the following versions:

1.  PostgreSQL:
-   8.x (checked: 8.4)
-   9.x (checked: 9.0, 9.6.13)
-   11.x (checked: 11.18)
-   12.x (checked: 12.13)
-   13.x (checked: 13.9)
-   14.x (checked: 14.1)
-   15.x (checked: 15.1)
-   16.x (checked: 16.0)

2.  Apache HTTP Server:
-   2.2.x (checked: 2.2.22)
-   2.4.x (checked: 2.4.46, 2.4.52 and 2.4.54)

3.  PHP:
-   5.x (checked: 5.3.20)
-   8.0.x (checked: 8.0.2)
-   8.1.x (checked: 8.1.3)
-   8.2.x (checked: 8.2.1)

Other versions may also work.

----------------------------------------------------------------

## ChangeLog ##

For a list of changes, refer to the `ChangeLog` file in the package.

----------------------------------------------------------------

## WARNING ##

The `dev` branch may contain code which is a work in progress and committed just for tests. The code here may not work properly or even compile.

The `master` branch may contain code which is committed just for quality tests.

The tags, matching the official packages on SourceForge, should be the most reliable points.
