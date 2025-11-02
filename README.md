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

6. The plugin is located at `widget-company-directory/`
7. Company data is provided in the `data/` folder (both JSON and CSV formats)



## Evaluation Criteria


### Import Process

I implemented the data import using the JSON file because it offered an easy-to-parse format compared to CSV. The import was handled through a custom PHP script that programmatically creates “company” posts using the WordPress wp_insert_post function. Admin users can navigate to Tools → Import Companies and click the ;Start Import; button to trigger an import from the predefined data/companies_json.json file, making the process fully repeatable. This approach could be improved in the future by allowing users to upload their own JSON file directly from the admin screen.

### Architecture Decisions

For data storage, I chose to use a custom post type system for companies, with key custom meta fields rating, benefits, cons, and free trial stored as custom post meta. This setup keeps each company self-contained and easily queryable, while leveraging WordPress’ REST API and editor interface. For list management, I implemented a custom Gutenberg block that allows content editors to create company lists by choosing from existing company posts. On the frontend, I used a dynamic PHP render callback to display each selected company's full details.

### Editor Workflow

To create and manage recommended company lists, editors can simply add the "Company List" Gutenberg block to any post or page in the WordPress editor. Within the block sidebar, they can select a company from a dropdown menu, and add them to the list with one click. They can also edit the list title directly in the block and reorder or remove companies as needed. Once published, the selected companies are automatically displayed on the frontend 

### How to Add New Companies and the Block

To add new companies to the directory, editors can navigate to the Companies post type in the WordPress admin menu and click "Add New". From there, they enter the company information just like a regular post. Once the company is published, it becomes available in the Company List block dropdown.

### Tradeoffs and Considerations

One tradeoff I made was choosing not to use plugins, even though some may have provided similar functionality. This decision saved time that would have otherwise gone into learning and customizing a bloated plugin for what was a simple and focused task. Due to time constraints, I also relied on AI to move quickly. With more time, I would review the code more thoroughly to remove redundancies, ensure best practices, and verify that every piece of logic is necessary and optimized. I would also add features like company categorization or tagging, improve the import interface with progress feedback, and enhance the frontend UI with search, filtering, or comparison tools to give visitors more ways to explore the directory.



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
