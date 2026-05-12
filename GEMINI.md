# PowerHousePortal

PowerHousePortal is a real estate property portal built with PHP, MariaDB, and interactive frontend components using Leaflet and MixHTML. It allows users to browse property listings on a map, search for specific areas, and view detailed information about individual houses.

## Tech Stack
- **Backend:** PHP 8.2 (Apache)
- **Database:** MariaDB 10.6
- **Cache/Session:** Redis (extension enabled)
- **Frontend:** 
  - [Leaflet.js](https://leafletjs.com/) for interactive maps.
  - [MixHTML](https://github.com/the-m-smith/mixhtml) for partial DOM updates and reactive behavior.
  - Vanilla CSS for styling.
- **Environment:** Docker & Docker Compose.

## Project Structure
- `apis/`: Contains PHP scripts that act as JSON API endpoints for searching, filtering, and fetching item details.
- `pages/`: PHP templates for the main application pages (index and house details).
- `static/`: Client-side assets including `app.js`, `app.css`, and `mixhtml.js`.
- `database/`: SQL initialization scripts and schema definitions.
- `db.php`: Central database connection configuration using PDO.
- `.htaccess`: Apache configuration for URL rewriting and routing.
- `Dockerfile` & `docker-compose.yml`: Configuration for containerized development.

## Getting Started

### Prerequisites
- Docker and Docker Compose installed.

### Running the Project
1. Start the containers:
   ```bash
   docker compose up -d
   ```
2. The application will be available at `http://localhost`.
3. PHPMyAdmin is available at `http://localhost:8080`.

### Database Setup & Seeding
- The database is automatically initialized by MariaDB.
- To seed the database with property data, you can run the seed script:
  - Visit `http://localhost/seed.php` in your browser.
  - This script fetches data from external sources and populates the `items` table.

## Development Conventions
- **Routing:** Handled via `.htaccess`. Clean URLs like `/house/{address_slug}` are mapped to internal PHP pages.
- **Data Access:** All database interactions should use the PDO instance `$_db` defined in `db.php`.
- **Frontend Interaction:** Use `mix-get` and other MixHTML attributes to perform asynchronous requests and update the UI without full page reloads.
- **Maps:** `static/app.js` contains the logic for initializing the Leaflet map and handling marker interactions.

## Key Files
- `pages/page-index.php`: The main entry point featuring the map and search interface.
- `pages/page-house.php`: The detail page for a specific property.
- `apis/api-filter-items.php`: Logic for filtering properties based on user criteria.
- `apis/seed.php`: Script for importing property data.
