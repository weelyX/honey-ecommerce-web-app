# Honey E-Commerce Web App

A simple e-commerce web application for selling honey products. The project includes product browsing, shopping cart functionality, user account registration/login, checkout flow, and an admin orders page.

## Project Overview

This project was developed as part of a Web Application Development course. The main goal was to build a functional online store using front-end pages, client-side cart handling, PHP back-end scripts, and a MySQL database schema.

## Features

- Home page with product branding and promotional sections
- Product listing page with product details and add-to-cart functionality
- Shopping cart using browser `localStorage`
- User registration and login system
- Account page for viewing and updating user information
- Checkout form that submits customer orders
- Admin orders page for viewing submitted orders
- Contact and About Us pages
- Arabic/English mixed interface content

## Tech Stack

- HTML
- CSS
- JavaScript
- PHP
- MySQL

## Database

The project includes a database schema file:

```text
database.sql
```

The schema creates the following tables:

- `users`
- `customers`
- `orders`
- `order_items`

## How to Run Locally

1. Install a local PHP/MySQL environment such as XAMPP or WAMP.
2. Copy the project folder into the local server directory, such as `htdocs` in XAMPP.
3. Start Apache and MySQL.
4. Open phpMyAdmin and import `database.sql`.
5. Check the database connection settings in `database.php`.
6. Open the project in the browser:

```text
http://localhost/honey-ecommerce-web-app/HomePage.html
```

## Project Files

- `HomePage.html` — Main landing page for the honey e-commerce website.
- `ItemsPage.html` — Products page displaying available honey items.
- `Cart.html` — Shopping cart page for viewing and managing selected products.
- `Account.php` — User account page for displaying customer-related information.
- `Login.php` — Login page for registered users.
- `signup.php` — Registration page for creating new user accounts.
- `submit_order.php` — Handles order submission and stores order data.
- `admin_orders.php` — Admin page for viewing and managing customer orders.
- `auth_nav.js` — JavaScript file for handling authentication-based navigation behavior.
- `database.php` — Database connection file used to connect the application with MySQL.
- `database.sql` — SQL file containing the database structure required to run the project.
- `Css/` — Stylesheets used to design and layout the website pages.
- `image/` — Image assets used across the website.
- `Video/` — Video assets used in the project.

## Notes

This project is a course-based web application prototype. It can be adapted from a honey store into a general product store by changing the product content, images, and pricing logic.
