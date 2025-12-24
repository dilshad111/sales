# Project Deployment Guide for Client PC (Windows 11)

Follow these steps to set up the **Sales Management System** on a new computer running **Windows 11**.

### 1. Required Software
Install the following software on the client's PC:
*   **XAMPP**: Download and install XAMPP (with PHP 8.2 or higher). Start **Apache** and **MySQL** from the XAMPP Control Panel.
*   **Git**: Download and install Git for Windows.
*   **Composer**: Download and install Composer (the dependency manager for PHP).
*   **Node.js**: Download and install Node.js (this includes **npm**).

---

### 2. Setup Steps

Open **Windows Terminal** (Right-click Start -> Terminal) or **Command Prompt (CMD)** and follow these commands:

#### Step 1: Clone the Project
Navigate to your desired folder (e.g., `C:\xampp\htdocs`) and run:
```bash
git clone https://github.com/dilshad111/sales.git
cd sales
```

#### Step 2: Install PHP Dependencies
```bash
composer install
```

#### Step 3: Install Frontend Dependencies
```bash
npm install
```

#### Step 4: Setup Environment File
```bash
copy .env.example .env
```

#### Step 5: Generate Application Key
```bash
php artisan key:generate
```

#### Step 6: Create Database
1. Open your browser and go to `http://localhost/phpmyadmin`.
2. Create a new database named `sales_db` (or whatever name you prefer).

#### Step 7: Configure Database in .env
Open the `.env` file in a text editor (Notepad) and update these lines:
```env
DB_DATABASE=sales_db
DB_USERNAME=root
DB_PASSWORD=
```

#### Step 8: Run Migrations and Seed Data
This will create the table structures and initial settings/users.
```bash
php artisan migrate --seed
```

#### Step 9: Build Assets
```bash
npm run build
```

#### Step 10: Run the Project
```bash
php artisan serve
```
The project will now be accessible at: `http://127.0.0.1:8000`

---

### 3. Extra Tips (Optional: Virtual Host)
If you want to access the project via a custom URL like `http://sales.test` instead of localhost:8000:
1. Edit `C:\Windows\System32\drivers\etc\hosts` and add: `127.0.0.1 sales.test`.
2. Configure a Virtual Host in XAMPP's `httpd-vhosts.conf` pointing to the `/public` directory of the project.
