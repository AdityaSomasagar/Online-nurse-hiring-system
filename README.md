# 🏥 Online Nurse Hiring System (ONHS)

The **Online Nurse Hiring System (ONHS)** is a web-based platform that allows patients to book nurses for in-home medical services. This guide provides step-by-step instructions to set up the project locally using XAMPP, WAMP, or LAMP.

---

## 📦 Features

- User registration and login
- Nurse booking functionality
- Admin panel for managing users and bookings
- Database-driven system
- Role-based access (Admin, Nurse, User)

---

## 🚀 How to Run the Project

### 1. Download the Project

- Download the ZIP file containing the project files.

### 2. Extract the Files

- Extract the ZIP file and copy the folder named `ONHS`.

### 3. Move the Project to Your Server Root

Paste the `ONHS` folder inside your web server’s root directory:

- **XAMPP**: `C:/xampp/htdocs`
- **WAMP**: `C:/wamp/www`
- **LAMP**: `/var/www/html`

### 4. Create the Database

1. Open your browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Create a new database named:

onhsdb

markdown
Copy
Edit

### 5. Import the SQL File

1. In phpMyAdmin, select the `onhsdb` database.
2. Go to the **Import** tab.
3. Choose the file `onhsdb.sql` from the `SQL File` folder inside the extracted ZIP.
4. Click **Go** to import.

### 6. Run the Project

Open your browser and navigate to:

http://localhost/onhs

yaml
Copy
Edit

---

## 🔐 Admin Panel Credentials

- **Username**: `admin`
- **Password**: `Test@123`

---

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML/CSS
- JavaScript
- Bootstrap (optional)

---

## 📄 License

This project is for academic and learning purposes only.

---

## 🙋‍♀️ Need Help?

For any issues or questions, feel free to contact the project maintainer.
