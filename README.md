# Progmix CMS

Welcome to Progmix CMS! This repository contains the source code for Progmix CMS, a content management system.

## Getting Started

Follow these steps to set up and run Progmix CMS on your local machine.

### Prerequisites

- [Git](https://git-scm.com/)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/)
- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/)

### Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/your-username/progmix-cms.git
    ```

2.  \*Setting up an Empty Database\*\*

    ```bash
    1.  cp .env.example .env
    2.  Open the .env file.
    3.  Locate the database configuration section.
    4.  Modify the database name to your preference.
    ```

3.  **Install Composer dependencies:**

    ```bash
    composer install --ignore-platform-reqs
    ```

4.  **Migrate the Database**
    ```
    php artisan migrate
    ```
    **To migrate a plugin migrations:**
    ```
    php artisan plugin:migrate author/plugin-name
    ```
5.  **Install NPM packages:**

    ```bash
    npm install
    ```

6.  **Compile assets:**

    ````bash
    npm run build:frontend
    npm run watch:frontend
    npm run build:cms
    npm run build:plugins ```

    **To build specfic plugin assets:**
    ````

    npm run build:plugin --plugin=plugin_folder_name

    ```

    ```

7.  **Create symbolic link for storage:**

    ```bash
    php artisan storage:link
    ```

8.  **Generate application key:**

        ```bash
        php artisan key:generate

    // Not Needed Anymore

    ```

    ```

9.  **Admin Control Panel (admin-cp) credentials:**

    ```bash
    php artisan db:seed

    admin@progmix.dev
    h55XdscJ053vsADnJ8
    ```

### Usage

You can now run Progmix CMS locally:

```bash
php artisan serve
# CMS
```
