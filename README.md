# 🏷️ EPayFC — Full-Stack Online Auction & Bidding Platform

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?logo=mysql)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/UI-Bootstrap%205.3%20%7C%20jQuery%204-7952B3?logo=bootstrap)](https://getbootstrap.com/)
[![Architecture](https://img.shields.io/badge/Architecture-Custom%20Lightweight%20MVC-informational)](#-software-architecture)
[![Security](https://img.shields.io/badge/Security-PDO%20%7C%20RBAC%20%7C%20.htaccess-success)](#-security--data-integrity)

> **Full-stack web platform for dynamic online auctions and direct sales, built with a custom-engineered PHP MVC framework, robust relational persistence (MySQL/MariaDB), role-based authorization, asynchronous client-side interactions, and an integrated time-shift simulation engine.**

---


## 🎯 Project Overview & Core Features

**EPayFC** is a modular web platform designed to manage the complete lifecycle of competitive item auctions and direct sales ("Buy It Now"). 

Rather than relying on an external monolithic framework, the platform is driven by a **custom lightweight Model-View-Controller (MVC) core**, providing complete architectural control over request dispatching, declarative business rules, input sanitization, and state transitions.

### Key Technical Highlights
* **Autonomous MVC Architecture:** Built-in front-controller router (`Router.php`), abstract controllers, reusable base model layer, and modular view template rendering.
* **Declarative Business Rules Engine:** Centralized constraint management configured directly via system parameters (`[Rules]`), enforcing:
  * Dynamic auction durations ranging from 1 to 365 days.
  * Title and description length boundaries.
  * Strict limit of 3 categories per listing.
  * Mutual exclusion rules between auction bidding and direct purchases.
* **Time-Warp Debugging Engine (`AppTime`):** Integrated temporal simulation utility allowing developers and testers to shift application time forward dynamically. This enables instant evaluation of scheduled auction expirations, winner allocations, and status mutations without altering server system clocks.
* **Automated Media Processing Pipeline (`ImageUtils`):** Server-side image manipulation using the PHP GD extension to enforce uniform aspect ratios, compress uploads to 1080p resolution, and generate 360p thumbnails for responsive grid rendering.
* **Role-Based Access Control (RBAC):** Tiered authorization dividing permissions among unauthenticated visitors, registered members (buyers/sellers), and administrators (category taxonomy control, user moderation).
* **Asynchronous Client Interface (AJAX):** Dynamic search autocomplete, live category reordering, media galleries, and interactive validation powered by modern JavaScript (ES6+), jQuery 4.0, and jQuery UI.

---

## 🏗️ Software Architecture

The codebase enforces a clean separation of concerns, decoupling domain entities, routing, security layers, and presentation:

```text
EPayFC/
├── config/             # System parameters and business rule definitions
├── controller/         # Request handling & orchestration (Item, User, Category, Time, Setup)
├── framework/          # Autonomous MVC Core (Router, Controller, Model, View, Tools)
├── model/              # Domain entities & database persistence (Item, Bid, User, Category, Picture)
├── utils/              # Specialized utilities (AppTime, ImageUtils, Sanitization)
├── view/               # Modular PHP presentation templates and partials
├── scripts/            # Asynchronous JavaScript handlers & jQuery AJAX modules
├── css/                # Custom stylesheets and localized Bootstrap 5.3 components
├── database/           # Relational schema migrations and database fixture dumps
└── uploads/            # Dynamic media storage segregated by entity ID
```

---

## 🔒 Security & Data Integrity

* **SQL Injection Immunity:** 100% of database interactions are abstracted via PDO using parameterized prepared statements.
* **Directory Cloaking & Defense-in-Depth:** Hierarchical `.htaccess` configurations enforce `Deny from all` on core directories (`/config`, `/controller`, `/framework`, `/model`, `/utils`, `/view`), preventing unauthorized direct HTTP script execution.
* **Input Sanitization & XSS Neutralization:** Centralized data-cleaning pipelines validate and sanitize input streams prior to reaching controller and model logic.
* **Secure File Upload Pipeline:** Strict server-side MIME type verification, dimension checks, cryptographic file renaming, and execution isolation for all uploaded media.
* **Password Hashing:** User authentication leverages modern cryptographic algorithms (`password_hash` with Bcrypt).

---

## 🛠️ Tech Stack

* **Backend:** PHP 8.2+ (OOP, PDO, GD Extension)
* **Database:** MySQL 8.0 / MariaDB 10.5+
* **Frontend:** HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6+), jQuery 4.0, jQuery UI
* **Web Server:** Apache (with `mod_rewrite` enabled)