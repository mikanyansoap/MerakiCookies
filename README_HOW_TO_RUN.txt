======================================================
         MERAKI COOKIES - SYSTEM DOCUMENTATION
======================================================

Hello! This file contains the instructions for running the Meraki Cookies website, both locally for development and publicly for presentations.

------------------------------------------------------
PART 1: HOW TO RUN LOCALLY (ON YOUR COMPUTER)
------------------------------------------------------
Because this website uses a PHP backend and a MySQL database, you CANNOT simply double-click the HTML files to open them. If you do, the forms, database connections, and logins will break.

Follow these steps to run the site correctly:

1. Open XAMPP Control Panel.
2. Start both "Apache" and "MySQL".
3. Open your terminal / Command Prompt / PowerShell.
4. Navigate to the folder containing this project using the `cd` command. 
   (e.g., cd C:\Users\Sophia Rynelle Anne\Downloads\meraki-20260627T071055Z-3-001\meraki)
5. Type the following command to start the PHP development server:
   php -S localhost:8000
6. Keep that terminal open! Now open your web browser and go to:
   http://localhost:8000/html/Home.html

Important Local Credentials:
- Default Admin Account: admin@meraki.com
- Default Admin Password: admin123


======================================================
                   END OF GUIDE
======================================================
