# Point of Sale System Integrated with MPESA

<p align="center">
  <a href="https://www.jminnovatechsolution.co.ke" target="_blank">
    <img src="https://www.jminnovatechsolution.co.ke/assets/img/iconbg-512.png" width="300" alt="JM Innovatech Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen" alt="License"></a>
</p>

## About

This is a modern **Point of Sale (POS) system** built with **Laravel**, designed for businesses in Kenya to manage sales, inventory, and transactions efficiently.  
The system is fully integrated with **MPESA Paybill** and **STK Push** for real-time payment confirmation, making financial operations seamless and automated.  
It includes a **user-friendly admin panel** to manage products, orders, customers, and payments.

## Features

- **Real-time MPESA integration** (Paybill & STK Push)
- Order creation, management, and tracking
- Inventory and stock management
- Automated payment confirmation
- User-friendly admin dashboard
- Sales and profit reporting
- Secure authentication and role-based access
- Optimized for performance and reliability

## Live Demo

[https://pos.jminnovatechsolution.co.ke](https://pos.jminnovatechsolution.co.ke)

## Technology Stack

- **Backend:** Laravel (PHP Framework)
- **Frontend:** Bootstrap 5, Vanilla JS
- **Database:** MySQL
- **Other:** MPESA Daraja API integration, AJAX for real-time updates

## Installation & Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/pos-system.git
Navigate into the project:

bash
Copy code
cd pos-system
Install dependencies:

bash
Copy code
composer install
npm install && npm run dev
Configure your .env file:

Database credentials

MPESA Paybill credentials and callback URLs

Run migrations:

bash
Copy code
php artisan migrate --seed
Serve the application:

bash
Copy code
php artisan serve
Contributing
Contributions are welcome! Please fork the repository and submit pull requests. Ensure clean commits and follow coding standards.

License
This project is licensed under the MIT License.

