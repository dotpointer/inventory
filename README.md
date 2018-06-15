# Inventory

Keep track of your belongings by putting them in a browseable and searchable
database. Categorize them by type and current location. Find out what is in
use and what you can sell. Create packlists for travel.

Made by dotpointer in jQuery, JavaScript, PHP, MySQL, HTML and CSS. 

## Getting Started

These instructions will get you a copy of the project up and running on your
local machine for development and testing purposes. See deployment for notes on
how to deploy the project on a live system.

### Prerequisites

What things you need to install the software and how to install them

```
- Debian Linux 9 or similar system
- nginx
- MariaDB (or MySQL)
- PHP
- PHP-FPM
- PHP-MySQLi
```

Setup the nginx web server with PHP-FPM support and MariaDB/MySQL.

In short: apt-get install nginx mariadb-server php-fpm php-mysqli
and then configure nginx, PHP and setup a user in MariaDB.

### Installing

Head to the nginx document root and clone the repository:

```
cd /var/www/html
git clone https://gitlab.com/dotpointer/inventory.git
cd inventory/
```

Import database structure, located in sql/database.sql

Standing in the project root directory login to the database:

```
mariadb/mysql -u <username> -p

```

If you do not have a user for the web server, then login as root and do
this to create the user named www with password www:

```
CREATE USER 'www'@'localhost' IDENTIFIED BY 'www';
```

Then import the database structure and assign a user to it, replace
www with the web server user in the database system:
```
SOURCE include/database.sql
GRANT ALL PRIVILEGES ON inventory.* TO 'www'@'localhost';
FLUSH PRIVILEGES;
```

Fill in the configuration in include/setup.php.

There are also shell scripts to run as cron jobs to regularly index new files
and create thumbnails in the cronjobs/ directory.

## Usage

Go through all your belongings item by item. Create a new record for each
item and add a photo of each one of them.

Try to make as complete records and photos as possible. Remember to include
items that belong to each item, like the original packages, 
instruction manuals, bags or cables.

There are tons of information online on how to organize belongings,
so these are just a few suggestions.

Do not spread things that belong together. An exception may be original
packagings, manuals and things you are sure you do not need - then you
make two storage locations - one with only the parts you will use and
one with the parts that just should be stored. You can note multiple
locations in the database by using comma (,).

Save everything when you get new items. Original packaging, manuals,
additional cables, even the plastic original bags. This way you get
the best chance to sell the item for the highest price if you
come to a point where you want to. Note the original price in the 
database.

Sell items you do not need instead of throwing them away. There
are bars that show what you use in each category and location.

Less items - better life. Do not let items you do not like
nor need take up space space. Challenge yourself to reduce the
quantity of your items.

The more you sell the more you can sell. In the beginning you may
find it hard to sell anything, because you think you want and need
everything. Start by selling things you absolutely do not want nor
need. Then sell something more and repeat. You will find that you
can dispense much more than you ever have thought before.

## Authors

* **Robert Klebe** - *Development* - [dotpointer](https://gitlab.com/dotpointer)

See also the list of
[contributors](https://gitlab.com/dotpointer/inventory/contributors)
who participated in this project.

## License

This project is licensed under the MIT License - see the
[LICENSE.md](LICENSE.md) file for details.

Contains dependency files that may be licensed under their own respective
licenses.
