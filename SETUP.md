# 🚀 Chat App - Complete Setup Guide

This guide will help you set up and run the Laravel chat application from scratch.

---

## 📋 Prerequisites

Before starting, ensure you have installed:
1. **XAMPP** - Download from [apachefriends.org](https://www.apachefriends.org/)
2. **PHP 8.2+** - Included with XAMPP
3. **Composer** - Download from [getcomposer.org](https://getcomposer.org/)
4. **Node.js & npm** - Download from [nodejs.org](https://nodejs.org/)
5. **Git** (optional) - Download from [git-scm.com](https://git-scm.com/)

---

## 🔧 Step-by-Step Installation

### **Step 1: Navigate to Project Directory**
Open PowerShell and go to the project folder:
```powershell
cd c:\xampp\htdocs\chat-app
```

---

### **Step 2: Verify XAMPP Services are Running**

1. Open **XAMPP Control Panel**
2. Click **Start** for:
   - **Apache** (web server)
   - **MySQL** (database)

Status should show: ✅ Running

> **Note:** Make sure MySQL is running on port 3306 (default)

---

### **Step 3: Create Database in phpMyAdmin**

**Option A: Using phpMyAdmin GUI (Easiest)**
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click on **"New"** button in left sidebar
3. Enter database name: `laravel2`
4. Click **Create**

**Option B: Using PowerShell Command**
```powershell
mysql -u root -e "CREATE DATABASE laravel2;"
```

---

### **Step 4: Install PHP Dependencies**

This will restore all the files that were removed from the vendor folder:

```powershell
composer install
```

**What this does:**
- Downloads all PHP packages listed in `composer.json`
- Installs Laravel framework, Laravel Reverb, and other dependencies
- Creates the `vendor/` folder
- Takes 2-5 minutes depending on internet speed

**Wait for it to complete.** You should see:
```
Package operations: XX installed, 0 updated, 0 removed
```

---

### **Step 5: Set Up Environment File**

The `.env` file is already configured, but verify it has:

```powershell
# View the .env file
type .env
```

Check that these lines exist:
```
APP_KEY=base64:HS1jFNmbkpeGYzaDx5GlZ8VlTwfgF02tdn9w/rudcFc=
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel2
DB_USERNAME=root
DB_PASSWORD=
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database
```

If `.env` is missing, copy from `.env.example`:
```powershell
Copy-Item .env.example -Destination .env
php artisan key:generate
```

---

### **Step 6: Run Database Migrations**

This creates all database tables:

```powershell
php artisan migrate --seed
```

**What this creates:**
- Users table
- Messages table
- User statuses table
- Cache & jobs tables
- Sample test data

**You should see:**
```
Migration table created successfully.
Migrating: 2025_01_01_000000_create_users_table
Migrating: 2025_01_01_000001_create_cache_table
...
Database seeded successfully.
```

✅ Check phpMyAdmin to verify tables are created

---

### **Step 7: Install Node Dependencies**

This installs frontend packages (Tailwind, Vite, Alpine.js, etc.):

```powershell
npm install
```

**Takes 1-3 minutes.** You should see:
```
added XXX packages in Xm
```

---

### **Step 8: Build Frontend Assets**

Compiles CSS and JavaScript:

```powershell
npm run build
```

**You should see:**
```
✓ 123 modules transformed
```

---

### **Step 9: Start the Application**

**Option A: Using Composer Script (Recommended - All Services in One Command)**

This command starts everything automatically:
```powershell
composer run dev
```

**This will run 4 services simultaneously:**
1. ✅ Laravel Development Server → http://localhost:8000
2. ✅ Queue Worker (for background jobs)
3. ✅ Laravel Pail (logs viewer)
4. ✅ Vite Dev Server (frontend hot reload)
5. ✅ Laravel Reverb (WebSocket for real-time chat)

**You should see:**
```
[server] INFO  Server running on [http://127.0.0.1:8000]
[vite] VITE vX.X.X ready in XXX ms
[reverb] Application running on 0.0.0.0:8080
```

💡 **Leave this terminal running!**

---

**Option B: If Option A Doesn't Work - Start Services Individually**

Open 4 separate PowerShell terminals and run:

**Terminal 1 - Laravel Server:**
```powershell
cd c:\xampp\htdocs\chat-app
php artisan serve
```
📌 Access at: http://localhost:8000

**Terminal 2 - Queue Worker:**
```powershell
cd c:\xampp\htdocs\chat-app
php artisan queue:listen --tries=1
```

**Terminal 3 - Logs Viewer:**
```powershell
cd c:\xampp\htdocs\chat-app
php artisan pail --timeout=0
```

**Terminal 4 - Frontend Dev Server:**
```powershell
cd c:\xampp\htdocs\chat-app
npm run dev
```

---

## 🌐 Access the Application

Once started, open your browser and go to:

### **http://localhost:8000**

---

## 📝 Test Login Credentials

Default test user is created during migration:

- **Email:** test@example.com
- **Password:** password

Or register a new account using the sign-up link.

---

## 🔄 Laravel Reverb (WebSocket Server)

**Reverb is automatically started** when you run `composer run dev`

- Runs on: `ws://localhost:8080`
- Enables real-time chat messaging
- Handles user status updates
- No additional configuration needed!

---

## 🐛 Troubleshooting

### ❌ **Error: "SQLSTATE[HY000] [2002] No such file or directory"**
- ✅ MySQL is not running
- ✅ Solution: Start MySQL in XAMPP Control Panel

### ❌ **Error: "Class 'PDO' not found"**
- ✅ PHP doesn't have MySQL extension
- ✅ Solution: Enable `php_pdo_mysql.dll` in XAMPP

### ❌ **Error: "npm: command not found"**
- ✅ Node.js not installed
- ✅ Solution: Download and install from nodejs.org, then restart PowerShell

### ❌ **Error: "composer: command not found"**
- ✅ Composer not in PATH
- ✅ Solution: Install Composer from getcomposer.org, then restart PowerShell

### ❌ **Port 8000 already in use**
- ✅ Something else is using the port
- ✅ Solution: Kill the process or use different port: `php artisan serve --port=8001`

### ❌ **Database connection failed**
- ✅ Database name is wrong
- ✅ Solution: Verify database is `laravel2` in phpMyAdmin and `.env`

---

## 📦 Quick Reference - All Commands

```powershell
# 1. Navigate to project
cd c:\xampp\htdocs\chat-app

# 2. Install PHP dependencies
composer install

# 3. Create database in phpMyAdmin (or run this command)
mysql -u root -e "CREATE DATABASE laravel2;"

# 4. Run migrations and seed database
php artisan migrate --seed

# 5. Install Node dependencies
npm install

# 6. Build frontend assets
npm run build

# 7. Start everything at once
composer run dev

# Then open in browser:
# http://localhost:8000
```

---

## 📂 Project Structure

```
chat-app/
├── app/               # PHP classes (Models, Controllers, Events)
├── config/            # Configuration files
├── database/          # Migrations and seeders
├── node_modules/      # JavaScript packages (created by npm install)
├── public/            # Web root (index.php)
├── resources/         # Views, CSS, JavaScript
├── routes/            # URL routes (web.php, channels.php)
├── storage/           # Logs, cache, uploads
├── vendor/            # PHP packages (created by composer install)
├── .env               # Environment variables (DATABASE, APP_KEY, etc.)
├── composer.json      # PHP dependencies
├── package.json       # Node.js dependencies
└── artisan            # Laravel command tool
```

---

## 🚀 Development Tips

**Hot Reload (Auto-refresh on file changes):**
- When running `npm run dev`, changes to CSS/JS automatically reload in browser
- Changes to `.php` files require page refresh

**View Database:**
- Open http://localhost/phpmyadmin
- Select `laravel2` database
- Browse tables

**View Logs in Real-Time:**
- Run `php artisan pail` in a terminal
- Shows all application logs

**Create New User (in Laravel Tinker):**
```powershell
php artisan tinker
> User::factory()->create(['email' => 'user@example.com'])
```

---

## ✅ You're Ready!

Follow all steps above and your chat app will be running perfectly! 🎉

If you get stuck, check the **Troubleshooting** section or ensure:
1. ✅ XAMPP services are running (Apache + MySQL)
2. ✅ Database `laravel2` exists in phpMyAdmin
3. ✅ All commands completed without errors

Happy coding! 💻
