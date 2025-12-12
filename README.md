# Widget Company Directory Plugin

## Overview

WordPress plugin that manages a directory of widget companies and allows editors to create curated, sorted "Recommended Lists" for frontend display.

### Description 

Directory plugin designed for ease of use by non-technical editors, offering a simple and intuitive interface to view and edit company details. Users can build curated “Recommended Lists” by selecting and ranking specific companies, then easily display these customized lists on frontend pages for visitors to explore.

## Getting Started (Detailed)

### Prerequisites

- Node.js (v18 or higher) and npm
- Docker Desktop installed and running
- Git
- A code editor

### Setup Instructions

1. Clone this repository:
   ```bash
   git clone <repository-url>
   cd web-take-home
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Start the WordPress environment:
   ```bash
   npm run env:start
   ```

   This will:
   - Download and start WordPress in Docker
   - Auto-install and activate the plugin
   - Set up the database
   - Map the data directory to the plugin

4. Build the block assets:
   ```bash
   cd widget-company-directory
   npm install
   npm run build
   ```

   Or for development with auto-rebuild:
   ```bash
   npm run start
   ```

5. Access WordPress:
   - **WordPress Site:** http://localhost:8888
   - **Admin Dashboard:** http://localhost:8888/wp-admin
     - Username: `admin`
     - Password: `password`

6. Activate Widget Company CPT and Widget Company Import Plugin
7. Import companies by going to Tools->Import Companies click "Start Import" button






## Project Structure

```
web-take-home/
├── data/                          # Company data files
│   ├── companies_data.json
│   └── companies_data.csv
├── widget-company-directory/      # Your WordPress plugin
│   ├── widget-company-directory.php  # Main plugin file
│   ├── src/                       # Source files
│   │   ├── blocks/
│   │   │   └── company-list/      # Gutenberg block (starter)
│   │   │       ├── block.json
│   │   │       ├── edit.js
│   │   │       ├── editor.css
│   │   │       └── style.css
│   │   └── index.js
│   ├── build/                     # Built assets (generated)
│   ├── includes/                  # Core plugin classes
│   ├── admin/                     # Admin-specific functionality
│   ├── public/                    # Frontend-specific functionality
│   ├── assets/                    # Additional CSS, JS, images
│   └── package.json
├── .wp-env.json                   # WordPress environment config
├── package.json                   # Project dependencies
└── README.md                      # This file
```
