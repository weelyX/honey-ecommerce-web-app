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

```text
honey-ecommerce-web-app/
├── HomePage.html
├── ItemsPage.html
├── Cart.html
├── Account.php
├── Login.php
├── signup.php
├── submit_order.php
├── admin_orders.php
├── auth_nav.js
├── database.php
├── database.sql
├── Css/
├── image/
└── Video/
```

## Notes

This project is a course-based web application prototype. It can be adapted from a honey store into a general product store by changing the product content, images, and pricing logic.
