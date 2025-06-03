# 🏥 Online Nurse Hiring System (ONHS) - Installation Guide

This guide will help you set up and run the **Online Nurse Hiring System (ONHS)** project on your local server using XAMPP, WAMP, or LAMP.

---

## 📥 1. Download and Extract

- Download the project ZIP file.
- Extract the contents of the ZIP file.
- Copy the `ONHS` folder from the extracted files.

---

## 📂 2. Move Project to Server Directory

Paste the `ONHS` folder into your server’s root directory based on the platform you are using:

- **XAMPP**: `C:/xampp/htdocs`
- **WAMP**: `C:/wamp/www`
- **LAMP**: `/var/www/html`

---

## 🗃️ 3. Setup the Database

1. Open **phpMyAdmin** in your browser:  
   [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

2. Create a new database with the name:  
onhsdb

yaml
Copy
Edit

3. Import the SQL file:
- Go to the **Import** tab.
- Choose the file `onhsdb.sql` located inside the `SQL File` folder in the extracted ZIP.
- Click **Go** to import.

---

## 🚀 4. Run the Project

Open your browser and navigate to:

http://localhost/onhs

yaml
Copy
Edit

---

## 🔐 Admin Panel Login

Use the following credentials to log in to the **Admin Panel**:

- **Username**: `admin`  
- **Password**: `Test@123`

---

> ✅ You are now ready to use the Online Nurse Hiring System on your local machine.

