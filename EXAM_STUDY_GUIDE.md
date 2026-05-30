# PowerHousePortal - Exam Study Guide

This document contains detailed explanations of the core technical concepts required for the exam, mapped directly to the **PowerHousePortal** project.

---

## 1. Apache Webserver & `.htaccess`

### The Concept

**Apache** is a web server software. It acts as the "Receptionist" of your application. It receives requests from browsers, decides which PHP file should handle them, and sends the response back.

What is Apache?
Apache is a Web Server software. Its primary job is to:

- Listen for incoming requests (usually on port 80).
- Find the right file to run (like page-index.php).
- Hand off PHP files to the PHP Engine to execute.
- Send the final result (HTML/JSON) back to the user's browser.

**`.htaccess`** is a configuration file used by Apache to change its behavior for a specific folder. Its most common use in modern web dev is **URL Rewriting**.

What is .htaccess?
The .htaccess file is a configuration file used by Apache to modify how it treats specific directories. In your project, you are using it for URL Rewriting.

### Implementation in PowerHousePortal

In your project, the `.htaccess` file creates "Clean URLs."

**Example Rule:**

```apache
RewriteRule ^house/([^/]+)$ /pages/page-house.php?address_slug=$1 [NC,L,QSA]
```

- **Pattern:** `^house/([^/]+)$` matches URLs like `/house/oestergade-8`.
- **Target:** `/pages/page-house.php?address_slug=$1` tells Apache to actually run the PHP file in the `pages` folder and pass the slug as a `$_GET` variable.

How your project uses it (The "Exam Answer")
In a basic setup, you would have to visit http://localhost/pages/page-house.php?address_slug=abc. That looks messy and is bad for SEO
(Search Engine Optimization).

Look at your .htaccess rules:

1 # 1. The Home Page
2 RewriteRule ^$ pages/page-index.php [NC,L]

- What it does: When the user goes to / (empty path), Apache internally loads pages/page-index.php. The user never sees the folder name
  "pages" in their browser.

1 # 2. Clean Property URLs
2 RewriteRule ^house/([^/]+)$ /pages/page-house.php?address_slug=$1 [NC,L,QSA]

- The Magic: This captures everything after house/ (like oestergade-8-6270-toender) and maps it to the address_slug variable inside
  page-house.php.
- Why it's cool: It allows your site to have professional-looking links like powerhouse.com/house/pretty-villa-in-toender.

### Why use it?

1.  **User Experience (UX):** Links are easier to read (`/house/villa` vs `/pages/page-house.php?id=123`).
2.  **Security:** It hides your internal folder structure (users don't see that your files are in a folder called `pages/`).
3.  **SEO:** Search engines prefer "clean" URLs with keywords rather than query strings.

### Key Terms for the Censor:

- RewriteEngine On: Enables the rewriting module.
- Regular Expressions (^, $, [^/]+): The patterns used to match URLs.
- Flags ([NC, L]):
  - NC = Case-Insensitive (doesn't matter if you type House or house).
  - L = Last (stop processing more rules if this one matches).
  - QSA = Query String Append (keep other variables like ?test=1).

Exam Question Tip: If the censor asks "Why don't you just link directly to the files?", you answer: "Because clean URLs are better for User
Experience (UX), easier to remember, and much better for Search Engines (SEO)."

---

## 2. REST APIs (Representational State Transfer)

### The Concept

A **REST API** is a set of rules that allow two systems (usually a Frontend and a Backend) to talk to each other.

- **The Client (Frontend):** Sends a request (e.g., "Give me all villas under 2M").
- **The Server (Backend):** Processes the request and sends back data (usually in **JSON** format).

### Implementation in PowerHousePortal

Your project has a dedicated `apis/` folder. These scripts don't output full HTML pages; they output **data**.

**Example: `api-city-suggestions.php`**

1.  **Request:** The frontend sends a `GET` request to `api-city-suggestions.php?city=To`.
2.  **Processing:** The PHP script queries the database for cities starting with "To".
3.  **Response:** The script returns a JSON array: `["Tønder", "Tommerup"]`.

**Key Components of a REST Request:**

1.  **Endpoint (URL):** The address, e.g., `/apis/api-search.php`.
2.  **Method:** Usually `GET` (for fetching data) or `POST` (for sending data).
3.  **Parameters:** Data passed in the URL (like `?min_price=500000`).
4.  **Status Codes:**
    - `200 OK`: Everything went fine.
    - `404 Not Found`: The endpoint or item doesn't exist.
    - `500 Internal Server Error`: The PHP code crashed.

**Example 2 (The MixHTML "Component" approach): `api-get-item.php`**
Unlike a standard JSON API, this endpoint returns a snippet of **HTML** wrapped in a `<browser>` tag.

- **How it works:** When you click a house on the map, the frontend calls this API. The PHP script generates the HTML for that specific house card.
- **The Benefit:** The frontend doesn't need to know how to build the house card; it just "swaps" the old HTML for the new HTML provided by the server. This is a hybrid approach between traditional websites and modern APIs.

### Why use it?

1.  **Separation of Concerns:** The backend only cares about data and logic; the frontend only cares about interaction and layout.
2.  **Speed:** You can update parts of the page (like a property detail aside) without reloading the whole website. This is called **AJAX** (Asynchronous JavaScript and XML/JSON).
3.  **Reusability:** You could build a Mobile App tomorrow, and it could use the exact same API endpoints as your website.

---

## 3. MVC (Model-View-Controller)

### The Concept

MVC is an architectural pattern that separates an application into three main logical components:

1.  **Model:** Manages the data and business logic (The "What").
2.  **View:** Handles the layout and display (The "Look").
3.  **Controller:** Handles user input and coordinates between the Model and View (The "Brain").

### Implementation in PowerHousePortal

While your project is a "Lite" version of MVC (it doesn't use a heavy framework like Laravel), the separation is still clear:

- **Model:**
  - `db.php`: The central connection point.
  - SQL queries inside your PHP files: These represent your data logic.
- **View:**
  - `pages/`: Contains your HTML templates (`page-index.php`, `page-house.php`).
  - `static/`: CSS and JavaScript that control the visual experience.
- **Controller:**
  - `apis/`: These scripts act as controllers. They receive a request (via `$_GET`), ask the Model for data, and then choose a View (JSON or HTML) to return.

### Why use it?

1.  **Organization:** It prevents "Spaghetti Code" where database queries and HTML tags are mixed in a confusing way.
2.  **Maintainability:** If you want to change the database, you only change the Model. If you want to change the design, you only change the View.
3.  **Teamwork:** One person can work on the CSS (View) while another works on the SQL queries (Model) without getting in each other's way.

---

## 4. Object-Oriented Programming (OOP)

### The Concept

OOP is a way of organizing code where we group data and functions into "Objects." In your project, the best example of OOP is the **PDO** (PHP Data Object) used in `db.php`.

### Implementation in PowerHousePortal

1.  **Classes vs. Objects**:
    - **The Class:** `PDO` is a built-in PHP blueprint that knows how to communicate with databases.
    - **The Object:** In `db.php`, when you write `$_db = new PDO(...)`, you are creating a "living" instance of that blueprint. `$_db` is the object you use throughout the project.

2.  **Methods (Actions)**:
    - Instead of separate functions, you call methods directly on the object.
    - Example: `$_db->prepare($sql)` or `$stmt->execute()`. These are actions the object "knows" how to do.

3.  **Data Representation (The "Item" Object)**:
    - Even though your properties are stored in a database table (`items`), in a full OOP system, each row would be a `Property` object.
    - **Encapsulation:** Instead of writing formatting code everywhere, a `Property` object would have a method like `$property->getFormattedPrice()` that handles the `number_format` logic internally.

4.  **Inheritance (`extends`)**:
    - If the project grew, you might have a base `Listing` class.
    - A `VillaListing` and `ApartmentListing` would both **inherit** from `Listing`. They share the same basics (price, address) but could have different methods (e.g., a Villa has `getGardenSize()`, while an Apartment has `getFloorLevel()`).

### Why use it?

1.  **Encapsulation:** It keeps the database logic (PDO) hidden inside the object. You don't need to know _how_ PDO works; you just need to know which methods to call.
2.  **Security:** Using the PDO object allows for **Prepared Statements**, which is the best way to prevent **SQL Injection** attacks.
3.  **Maintenance:** If you want to change how a property price is displayed, you change it in one class method, and it updates across the entire website.

### How I could have used OOP (Refactoring Comparison)

In the exam, you can demonstrate deeper understanding by explaining how you would "upgrade" your current code to a full OOP structure.

| Current Approach (Procedural-lite)                                       | Full OOP Approach (The "Professional" Way)                       |
| :----------------------------------------------------------------------- | :--------------------------------------------------------------- |
| **Data:** Fetched as a simple associative array.                         | **Data:** Fetched as a `Property` object.                        |
| **Logic:** The address slug is generated manually in `api-get-item.php`. | **Logic:** The `Property` class would have a method `getSlug()`. |
| **Database:** Global `$_db` variable used everywhere.                    | **Database:** A `PropertyRepository` class handles all queries.  |

**Example of a Full OOP "Property" Class:**

```php
class Property {
    public $price;
    public $road_name;
    public $house_number;

    // Logic is hidden inside the object
    public function getSlug() {
        return str_replace(' ', '_', $this->road_name) . "-" . $this->house_number;
    }

    public function getFormattedPrice() {
        return number_format($this->price, 0, ',', '.') . " kr.";
    }
}
```

**How it would be used in an API:**
Instead of `fetch()`, you would use `fetchObject('Property')`.
Then, instead of `$item['item_price']`, you would just call `$item->getFormattedPrice()`.

**Why this is better:** If you need the slug in 5 different files, you don't have to copy-paste the `str_replace` logic. You just call the method. This follows the **DRY (Don't Repeat Yourself)** principle.

---

## 5. Database Connections & PDO

### The Concept

**PDO (PHP Data Objects)** is the modern standard for connecting to databases in PHP. It acts as an abstraction layer, meaning your code looks the same whether you use MySQL, MariaDB, or PostgreSQL.

### Implementation in PowerHousePortal (`db.php`)

You use a **`try-catch`** block to establish the connection:

```php
try {
  $_db = new PDO($dbConnection, $dbUserName, $dbPassword, $options);
} catch(PDOException $ex) {
  echo $ex; // Handle connection errors gracefully
  exit();
}
```

### Key Security Feature: Prepared Statements

This is the most important part of database connections for your exam. In your API files, you don't put variables directly into the SQL string. You use **placeholders**.

**Bad (Unsafe):**
`"SELECT * FROM items WHERE item_pk = $id"` (Vulnerable to SQL Injection)

**Good (Safe - Your Project):**

```php
$q = $_db->prepare('SELECT * FROM items WHERE item_pk = :item_pk');
$q->bindValue(':item_pk', $item_pk);
$q->execute();
```

- **The Benefit:** The database "pre-compiles" the query. It treats the value of `:item_pk` strictly as data, not as code. Even if a hacker types `123; DROP TABLE items`, the database will just look for a house with that exact (and weird) ID.

### Why use it?

1.  **Security:** Blocks 100% of SQL Injection attacks when used correctly.
2.  **Error Handling:** The `try-catch` block prevents the user from seeing sensitive database details (like your username) if the connection fails.
3.  **Portability:** It's easier to move the project from a local MariaDB to a live server or even a different type of database in the future.

---

## 6. Microservices

### The Concept

A **Microservice** is a software architecture where an application is composed of small, independent services that communicate over a network (usually via HTTP/REST). Instead of one "Monolith" that does everything, you have multiple specialized services.

### Implementation in PowerHousePortal (`seed.php`)

Your project acts as a "Consumer" of an external microservice.

1.  **The Service:** The **Boligsiden API** is a microservice. Its only job is to provide real estate data.
2.  **The Interaction:** Your `seed.php` script uses `file_get_contents()` to request data from `https://api.boligsiden.dk/search/map/cases`.
3.  **The Integration:** Your project takes the "output" of that microservice and saves it into your own database so your frontend can use it.

### Why use it?

### Why use it?

1.  **Specialization:** You don't have to build a system to collect house data from every realtor in the country. You just use a service that is already the "expert" in that data.
2.  **Agility:** If Boligsiden changes how they store data, you only need to update your `seed.php` file, not your entire website's UI.
3.  **Efficiency:** You only fetch the data you need (e.g., houses in a specific zip code) rather than downloading the entire Danish housing market.

### Future Growth: If the project grew...

As the project scales, you would move high-load or specialized tasks into their own Microservices.

**Example: Image Processing Service**

- **The Problem:** Property photos are usually high-resolution. Processing (resizing, watermarking, optimizing) hundreds of photos at once on your main PHP server would slow down the entire website for users.
- **The Microservice Solution:**
  1.  Create a separate, small server (perhaps using Node.js or Go) just for images.
  2.  When a realtor uploads a photo to your PHP app, the app sends the "raw" image to the **Image Service**.
  3.  The Image Service does the heavy lifting, saves the optimized versions, and sends back a "Success" message to your PHP app.
- **Result:** Your main website stays fast and responsive because the "heavy work" is happening elsewhere.

**Other Future Examples:**

- **Notification Service:** A microservice that handles sending emails and SMS alerts when a new house matches a user's saved search.
- **Auth Service:** A centralized service that handles logins for multiple related apps (e.g., the portal, a realtor dashboard, and a mobile app).

**Exam Answer:** _"If PowerHousePortal were to scale to thousands of users, I would transition from a Monolith to a Microservice architecture. For instance, I would decouple image processing into its own service. This prevents CPU-intensive tasks from affecting the user experience on the main site, allowing each service to scale independently based on demand."_

---

### Final Exam Tip: The "Big Picture"

If the censor asks how all these pieces fit together, you can say:
_"A user visits a **Clean URL** handled by **Apache**. The **Controller** receives the request and asks the **Model (PDO)** for data. The Model provides the data as **Objects (OOP)**. That data is then sent back through a **REST API** to the **View**, which updates the map for the user."_
