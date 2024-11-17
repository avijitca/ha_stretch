# CodeIgniter RESTful API Project

This project is a RESTful API developed using the CodeIgniter framework. It provides CRUD (Create, Read, Update, Delete) operations that can be accessed via HTTP requests using tools like Postman. The API is designed to be run locally for development and testing purposes.

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Testing with Postman](#testing-with-postman)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## Features

- Developed using **CodeIgniter** (PHP framework)
- Implements a **RESTful API** with CRUD operations
- Supports JSON input and output
- Database integration using **MySQL**

## Prerequisites

Before running this project, ensure you have the following installed:

- [PHP (7.4)](https://www.php.net/downloads.php)
- [MySQL](https://dev.mysql.com/downloads/)
- [Apache](https://httpd.apache.org/download.cgi) or [Nginx](https://nginx.org/en/download.html) server
- [Postman](https://www.postman.com/downloads/) (for API testing)

## Installation

Follow these steps to set up the project locally:

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/your-repository.git
   cd your-repository

## Configuration

1. CodeIgniter Configuration
    Update application/config/config.php (optional):
    $config['base_url'] = 'http://localhost/your-repository/';

## Database Setup

1. Create MySQL Database
    CREATE DATABASE lending

2. Import the provided db.sql file into lending database

3. Update the application/config/database.php file with your local database configuration settings.

## Testing with Postman

1. Open Postman and create a new request.
2. Set the request type (GET, POST, PUT, DELETE).
3. Enter the API endpoint URL (e.g., http://localhost/ha_stretch/create_loan).
4. For POST/PUT requests, use the Body tab to send JSON data.
5. Click Send and view the response.

## Troubleshooting

-  500 Internal Server Error: Ensure your database credentials are correct in the database.php file.

-  404 Not Found: Check if your base URL is correctly set up in config.php.

-  Database Connection Issues: Verify your MySQL server is running.


## License

MIT License

Copyright (c) 2024 Avijit Chakravarty

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
