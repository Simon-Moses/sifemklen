# SIFEMKLEN Cleaning Service Management System

BIT 6093 Practical Skills Application (PSA).

## Stack
HTML5, CSS3, Bootstrap 5, JavaScript, PHP 8+, MySQL, PDO, phpMyAdmin, XAMPP/Apache.

## Setup
1. Install XAMPP and start Apache + MySQL.
2. Copy this folder to `C:\xampp\htdocs\sifemklen\`.
3. Open phpMyAdmin and import `database/sifemklen.sql`. The script creates `sifemklen` automatically.
4. Check `config/database.php` (default XAMPP root user has a blank password).
5. Open `http://localhost/sifemklen/`.

## Default admin
Email: `admin@sifemklen.local`
Password: `Admin@12345`

The SQL stores only a PHP password hash. Change the password after first login through a secure admin/user workflow before deployment.

## Customer
Use Register to create a customer account. Customers can book services, track booking status and view payments.

## Workflow
Customer registration → booking → Pending → Admin confirmation → Staff assignment → In Progress → Completed. Each booking automatically gets a pending payment record. Admin payment updates are logged.

## Security
PDO prepared statements, password_hash/password_verify, sessions, session regeneration, CSRF tokens, role checks, customer ownership checks, output escaping, validation and controlled deletion are implemented.

## Troubleshooting
- Database error: confirm MySQL is running and database credentials in `config/database.php`.
- 404: confirm the folder is exactly `htdocs/sifemklen` and Apache is running.
- CSS missing: confirm the project URL is `/sifemklen/`.
- Import failure: import the SQL into phpMyAdmin using the supplied `database/sifemklen.sql`.
