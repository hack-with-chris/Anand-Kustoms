# Anand Kustoms

A responsive auto-customization showcase built with PHP, HTML, CSS, JavaScript, and MySQL. It features service listings, project examples, testimonials, and a consultation form that stores enquiries in MySQL.

## Requirements

- PHP 8.1 or newer with `pdo_mysql` and `mbstring` enabled
- MySQL or MariaDB (XAMPP is supported)

## Run with XAMPP

1. Start **MySQL** in the XAMPP Control Panel.
2. Open phpMyAdmin at <http://localhost/phpmyadmin>, create a database named `anand_kustoms`, then import [`database/schema.sql`](database/schema.sql).
3. Confirm the MySQL settings in `includes/db.php`. Defaults are `127.0.0.1:3306`, user `root`, and an empty password. Update them if your local XAMPP configuration differs.
4. Copy this project into XAMPP's `htdocs` directory, or run PHP's development server from the project folder:

   ```powershell
   php -S 127.0.0.1:8000 -t . router.php
   ```

5. Visit <http://127.0.0.1:8000>. With the PHP development server, the router script runs PHP pages and serves CSS/JavaScript assets.

If you use XAMPP Apache, open the project under its `htdocs` URL instead.

## Database

`database/schema.sql` creates the database and the `services`, `projects`, and `contact_messages` tables. It also seeds the example services and projects. Contact enquiries submitted through the form are saved in `contact_messages`.

The application reads optional database settings from environment variables: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.

## Project layout

```text
assets/
  css/style.css
  js/script.js
database/schema.sql
includes/db.php
index.php
router.php
```
