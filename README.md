# Raneem Clinic — Cosmetic Clinic Website & Management System

![PHP](https://img.shields.io/badge/PHP-8-777BB4?logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)

> Arabic website for a cosmetic and dermatology clinic, with a back-office for customers, treatments, employees, expenses and reports.

## Overview
A production project built and deployed by **Mahmoud Hasan**. This repository holds the source code only. The client's secrets,
databases, uploaded files and personal data are not included; where the code needs them, they are replaced with placeholders.

**Tech stack:** PHP 8 · MySQL · Vanilla JS · CSS

## Features
- Public website (right-to-left Arabic): services, gallery, testimonials, contact
- Back-office: customers, treatments, employees, expenses, financial reports
- Content editing for services, gallery, testimonials and settings
- PDO database layer, session-based admin login, protected uploads folder
- `install.sql` creates the whole schema in one step

## Project structure
```
index.php          public website
admin/             back-office modules
includes/          auth + database
config.php         settings (placeholders)
install.sql        schema
```

## Getting started
Create a MySQL database, import `install.sql`, enter the credentials in `config.php` and upload to a PHP 8 host. `README.txt` (Arabic) has the full guide.

## Security & privacy
- No credentials in the repository. API keys, passwords and database settings are placeholders (`CHANGE_ME`, `YOUR_…`, `your_db_password`).
- Runtime data (customers, messages, appointments, logs, uploads) is left out.
- Personal names, phone numbers and addresses in the default content are replaced with sample data (e.g. Max Mustermann, +49 151 00000000, Musterstraße 1).

## Author
**Mahmoud Hasan** · [GitHub](https://github.com/MahmoudHasan2)
