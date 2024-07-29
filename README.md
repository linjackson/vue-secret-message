# Vue Secret Message (vue-secret-message)

Vue Secret Message

## Install DDev

Check the guide for [Get started with DDev](https://ddev.com/get-started/) for more information.

## Start DDev

```bash
ddev start
```

## Install the dependencies

```bash
yarn
```

## Connect to the database

Get the connection information:

```bash
ddev describe
```

## Import the database structure:

```bash
ddev import-db < db.sql
```

Insert a username and a hashed password into the database.
Hash the password using the PHP function:

```php
password_hash($password, PASSWORD_DEFAULT);
```

where `$password` is the password to hash, or use an online tool such as the [OnlinePHP.io Password Hash Tool](https://onlinephp.io/password-hash).

## Add the database credentials in the app

Copy the `api/configurations.example.php` file to `api/configurations.php` and update the database credentials as needed.

## Build the app for production

```bash
yarn build
```

## Visit the site

Visit [the site](https://vue-secret-message.ddev.site/#/) in the browser.

### If setting up on a webserver

DDev could be used to host with low traffic, otherwise a larger Docker container may need to be set up, or Apache, the database, and PHP can be set up at a server level.

If multiple sites are being served from the same machine, Apache virtual hosts can be used to separate them out.

Place the sites in their own directories in the `www` folder and update Apache's `httpd.conf` file with the virtual host information following this example:

```apacheconf
<VirtualHost *:80>
    # This first-listed virtual host is also the default for *:80
    ServerName www.example.com
    ServerAlias example.com
    DocumentRoot "/www/domain"
</VirtualHost>

<VirtualHost *:80>
    ServerName other.example.com
    DocumentRoot "/www/otherdomain"
</VirtualHost>
```

or follow the [guide on the Apache website](https://httpd.apache.org/docs/2.4/vhosts/name-based.html).
