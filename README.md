# Hotel Project

Web application for searching and booking hotel rooms.

WDA - CollegeLink Web Development eLearning Academy.
Versions covered: v3.0 (core), v3.1 (Ajax), v3.2 (security).

Source: `https://github.com/agkouzou/hotel-project`

## Table of Contents

1. [What It Is](#1-what-it-is)
2. [Requirements](#2-requirements)
3. [Folder Structure](#3-folder-structure)
4. [Installation (from zero)](#4-installation-from-zero)
5. [How to Use the Application](#5-how-to-use-the-application)
6. [Validation and Security](#6-validation-and-security)
7. [Database Notes](#7-database-notes)
8. [Troubleshooting](#8-troubleshooting)
9. [Delivery Checklist](#9-delivery-checklist)

## 1. What It Is

A PHP + MySQL web application where visitors can search hotel rooms by
dates, city and room type, view room details, and (after registering)
book rooms, leave reviews and keep favorites.

| Page | Purpose |
| --- | --- |
| `index.php` | Search form + results (filters, Ajax, URL stays in sync) |
| `room.php` | Room details, availability, booking, map, reviews, favorite |
| `register.php` | Create an account |
| `login.php` | Log in |
| `profile.php` | Booking history, favorite rooms, my reviews, logout |
| `list.php` | Legacy URL: forwards to `index.php` (v3.0 results page) |

## 2. Requirements

- PHP 8.0 or newer with the `pdo_mysql` extension (XAMPP 8.2 has it)
- MySQL or MariaDB
- Apache (bundled with XAMPP)
- Internet access in the browser (Bootstrap is loaded from a CDN and
  the room map is an embedded Google Map)

## 3. Folder Structure

```text
hotel.sql              Database dump (schema + sample rooms)
README.md              This file
config/config.json     Database connection settings
boot/boot.php          Bootstrap file included by every page/action
app/                    Application code (namespace App\)
  Support/             Autoloader, Config, Db (PDO), Validator, Csrf,
                       helpers.php (e(), redirect(), flash(), ...)
  Hotel/               Services: UserService, RoomService,
                       RoomTypeService, BookingService,
                       FavoriteService, ReviewService
public/                 Web root - only this folder is served
  *.php                One file per page
  _header.php          Shared header (nav, flash messages, CSRF meta)
  _footer.php          Shared footer (copyright, scripts)
  actions/             Form handlers and Ajax endpoints
  partials/            HTML fragments reused by pages and Ajax
  assets/css, js       Styles and JavaScript
  assets/images/rooms  The 10 room photos (room-1.jpg ... room-10.jpg)
```

## 4. Installation (from zero)

### 4.1 Install XAMPP

Download XAMPP 8.2 from <https://www.apachefriends.org> and install it
(default folder `C:\xampp`). In the XAMPP Control Panel start
"MySQL" and "Apache" (both are required - see [2. Requirements](#2-requirements)).

### 4.2 Import the database

**Option A - phpMyAdmin** (<http://localhost/phpmyadmin>):
Import tab -> choose `hotel.sql` -> Go.
The dump creates the database `hotel` by itself.

**Option B - command line:**

```sh
C:\xampp\mysql\bin\mysql.exe -u root < hotel.sql
```

### 4.3 Create the database user used by the application

In phpMyAdmin (SQL tab) or the mysql client, run:

```sql
CREATE USER 'hotel'@'localhost'  IDENTIFIED BY 'password';
CREATE USER 'hotel'@'127.0.0.1' IDENTIFIED BY 'password';
GRANT ALL ON hotel.* TO 'hotel'@'localhost';
GRANT ALL ON hotel.* TO 'hotel'@'127.0.0.1';
FLUSH PRIVILEGES;
```

### 4.4 Check the configuration

`config/config.json` must contain:

```json
{
  "db": {
    "host": "127.0.0.1",
    "database": "hotel",
    "user": "hotel",
    "password": "password"
  }
}
```

These are the values required by the delivery instructions. They are
intentionally simple, course-mandated test credentials for a
local-only development database - not a real secret, and not
something used anywhere outside this local setup.

### 4.5 Verify the database connection

```sh
C:\xampp\mysql\bin\mysql.exe -u hotel -ppassword -h 127.0.0.1 hotel ^
    -e "SELECT COUNT(*) FROM room;"
```

It must print `10`.

### 4.6 Run the application

**Once the one-time setup below is done**, starting the app again
later is just: open the **XAMPP Control Panel** -> click **Start**
next to MySQL, then **Start** next to Apache -> open
`http://hotel.collegelink.localhost`.

**One-time setup** - Apache virtual host, matching the domain stated
in the course spec (`http://hotel.collegelink.localhost`):

Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf` (the second
block keeps plain `http://localhost/` and phpMyAdmin working once a
named vhost exists on port 80):

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "C:/xampp/htdocs"
</VirtualHost>

<VirtualHost *:80>
    ServerName hotel.collegelink.localhost
    DocumentRoot "C:/path/to/hotel-project/public"
    <Directory "C:/path/to/hotel-project/public">
        Require all granted
    </Directory>
</VirtualHost>
```

Add `127.0.0.1 hotel.collegelink.localhost` to
`C:\Windows\System32\drivers\etc\hosts`. This requires administrator
rights:

- Right-click Notepad -> **Run as administrator** -> File -> Open ->
  type the path above with the file-type filter set to **All Files**
  (the `hosts` file has no extension; if you save without selecting
  "All Files", Windows silently saves it as `hosts.txt` next to the
  real file and the entry will not take effect).
- Add the line, save, then restart Apache from the XAMPP Control
  Panel and open `http://hotel.collegelink.localhost`.

> **Important:** the document root must be the `public` folder, because
> the pages use absolute URLs such as `/assets/css/style.css` and
> `/actions/login.php`.

### 4.7 Stopping the application

Open the **XAMPP Control Panel** and click **Stop** next to Apache,
then **Stop** next to MySQL. This frees port 80 (Apache) and MySQL's
port (3306) for other uses.

## 5. How to Use the Application

### 5.1 Searching rooms (index.php)

- The page opens with results for today through today + 2 days, all cities.
- Fill in Check-in, Check-out and City (hold Ctrl to pick several
  cities); optionally room type(s), price range and number of guests.
- Press Search. Results load below the form without a page reload
  and the address bar is updated, so reloading or sharing the URL
  shows the same results.
- Only rooms with no booking overlapping the chosen dates are listed.
- Click a room to open its page (the chosen dates are carried over).

### 5.2 Room page (room.php)

- Shows photo, name, city/area, type, guests, price per night,
  average rating, WiFi / Parking / Pet friendly, descriptions and a map.
- Change Check-in / Check-out and press "Check availability": the
  availability box refreshes asynchronously and shows the total price.
- Reviews load asynchronously, newest first, with stars, author,
  comment and date.

### 5.3 Account

- Register: name, email, repeat email, password. You are logged in
  straight after registering.
- Login: email + password. Logged-in users are redirected away from
  the login/register pages.
- Profile (link with your name in the header): booking history with
  totals, favorite rooms (newest first), your reviews, and Logout.
- The sample user `hotel_admin` has an empty password hash, so it
  cannot log in; register your own account.

### 5.4 Actions for logged-in users (room page)

- Book now: books the displayed dates. Availability is re-checked
  on the server inside a transaction, so the same room cannot be
  double-booked. Total = nights x price per night.
- Add review: rating 1-5 (required) and optional comment up to 250
  characters. The list and the average rating refresh via Ajax.
- Heart icon: add/remove the room from your favorites (Ajax).

## 6. Validation and Security

- Validation runs in both front end (`assets/js/app.js`) and back end
  (`App\Support\Validator` + the action files) - back end is the authority.
- v3.2 protections are implemented: CSRF tokens on every POST form
  (checked centrally in `boot.php`), XSS escaping on all
  user-originated output, SQL injection protection via PDO bound
  parameters everywhere, and hashed passwords (`password_hash` /
  `password_verify`).

## 7. Database Notes

Tables: `user`, `room`, `room_type`, `booking`, `favorite`, `review`
(see `hotel.sql`). `room.avg_reviews` and `room.count_reviews` are
updated whenever a review is added. A room is "booked" for a period
when an existing booking satisfies
`check_in_date < new_check_out AND check_out_date > new_check_in`
(the check-out day itself is free).

### 7.1 Accessing the database directly

**Option A - phpMyAdmin (GUI):**

1. Go to <http://localhost/phpmyadmin>.
2. Click the `hotel` database in the left sidebar to expand its tables.
3. Click a table name, then the **Browse** tab, to view its rows (or
   **SQL** to run a custom query).

**Option B - MySQL command line:**

```sh
C:\xampp\mysql\bin\mysql.exe -u hotel -ppassword -h 127.0.0.1 hotel
```

Logs straight into the `hotel` database (credentials match
`config/config.json`). Example queries:

```sql
SELECT user_id, name, email, created_time FROM user;
SELECT * FROM booking WHERE user_id = 1;
SELECT * FROM favorite WHERE user_id = 1;
SELECT * FROM review WHERE user_id = 1;
SELECT room_id, name, city FROM room;
```

Type `exit` to leave the CLI session.

### 7.2 Resetting test data

To wipe everything created through using the app (test bookings,
favorites, reviews, and any accounts you registered) while keeping
the seeded rooms, room types and the `hotel_admin` account, run this
in phpMyAdmin's SQL tab or the mysql CLI:

```sql
DELETE FROM review;
DELETE FROM favorite;
DELETE FROM booking;
DELETE FROM user WHERE user_id <> 1;
UPDATE room SET avg_reviews = NULL, count_reviews = 0;
ALTER TABLE review AUTO_INCREMENT = 1;
ALTER TABLE booking AUTO_INCREMENT = 1;
ALTER TABLE user AUTO_INCREMENT = 2;
```

The `UPDATE room ...` line matters: deleting reviews directly (unlike
the app's own review flow) does not recalculate the cached
`avg_reviews`/`count_reviews` columns on `room`, so it is reset here
to match the values `hotel.sql` originally seeded.

To start completely from scratch instead (including the seed data),
drop and re-import the whole database - see step 4.2.

## 8. Troubleshooting

| Symptom | Fix |
| --- | --- |
| "could not find driver" | Enable `extension=pdo_mysql` in `php.ini`. |
| "Access denied for user hotel" | Repeat step 4.3. |
| "Unknown database hotel" | Import `hotel.sql` (step 4.2). |
| Pages show no styling / 404s | The web root is not the `public` folder. |
| "Invalid or missing security token" (419) | The session expired or cookies are blocked; reload the page and retry. |
| Room photos missing | Check `public/assets/images/rooms`. |
| `hotel.collegelink.localhost` does not resolve / times out | The `hosts` file entry is missing, or was saved as `hosts.txt` instead of `hosts` (step 4.6). |

## 9. Delivery Checklist

- [ ] Database name is `hotel`; `hotel.sql` is in the project root
- [ ] `config/config.json` holds the credentials from step 4.4
- [ ] Connection verified (step 4.5)
- [ ] Zip the project as `<academy-login-email>.zip`
- [ ] Upload the zip to the Dropbox request link in the instructions
