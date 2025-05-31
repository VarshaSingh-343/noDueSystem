# No Due System

## Overview

The **No Due System** is a web-based application designed to manage and streamline the no dues clearance process for students. It ensures that students have cleared all dues from various departments before they can request a refund. The system provides four panels for different user roles: **Admin, Account, Department**, and **Student**.

## Installation

### Prerequisites
- **XAMPP** or any LAMP/WAMP/MAMP stack.
- **PHP 7.4** or higher.
- **MySQL 5.7** or higher.

### Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/VarshaSingh-343/noDueSystem.git

2. **Move the project folder:** 
    Move the project folder to your web server's root directory (e.g., htdocs for XAMPP).

3. **Create a database in MySQL and import the SQL file:**

    - CREATE DATABASE noduesproject;
    - USE noduesproject;
    - SOURCE noduesproject.sql;

4. **Start your web server and navigate to the application:** 
    Open your browser and go to: http://localhost/noduesproject

### How to Use the Application
**Student**
- Log in or register.
- Submit a no dues request.
- Upload documents (e.g., canceled check).
- Track approval progress.
- Wait for refund once fully approved.

**Department**
- Log in with department credentials.
- View student requests.
- Approve or reject requests with comments.

**Account**
- Log in to see students who cleared all dues.
- Initiate refunds for eligible students.

**Admin**
- Manage users and departments.
- View students, departments, and refund details.
- Monitor reports and system activity.

### Contributing
Contributions are welcome! Please fork the repository and submit a pull request for any improvements or fixes.