
### Laravel Shopify Embedded App

This project is an adapter designed to integrate Shopify's embedded app capabilities with a Laravel 11 application, allowing seamless operation within Shopify's admin interface.

### Features

- Authentication and session management tailored for Shopify stores.
- Middleware for ensuring app installation, authentication, and setting Content Security Policy headers compatible with Shopify's embedded environment.
- Integration with Shopify's OAuth flow for app installation and permissions granting.
- A flexible architecture that leverages Laravel's provider and middleware systems for easy Shopify API interaction.

### Prerequisites

- PHP >= 8.2
- Laravel 11.x
- An existing Shopify Partner account and a Shopify API key and secret.

### Installation

1. Clone the repository to your local machine or server:

   ```bash
   git clone https://github.com/iMaGd/shopify-php-laravel.git
   ```

2. Navigate to the project directory:

   ```bash
   cd shopify-php-laravel
   ```

3. Install dependencies via Composer:

   ```bash
   composer install
   ```

4. Copy `.env.example` to `.env` and configure your environment variables, including your Shopify API credentials:

   ```plaintext
   SHOPIFY_APP_NAME=ShopifyApp
   SHOPIFY_API_CLIENT_ID=your_shopify_app_api_key
   SHOPIFY_API_CLIENT_SECRET=your_shopify_app_api_secret
   SHOPIFY_API_SCOPE=read_products,write_products
   SHOPIFY_API_VERSION=2021-10
   SHOPIFY_ROUTE_AUTH_REDIRECT=bridge
   SHOPIFY_ROUTE_AUTH_CALLBACK=auth/shopify/callback
   SHOPIFY_ROUTE_WEBHOOK=shopify/webhook
   ```

5. Run the migrations to set up the necessary database tables:

   ```bash
   php artisan migrate
   ```

6. Use Laravel Herd, or serve your Laravel application:

   ```bash
   php artisan serve
   ```

   Ensure your application is accessible over HTTPS—you may use services like ngrok or expose for local development.

### Configuration

This project requires minimal configuration, thanks to sensible defaults. Ensure your `.env` file is properly set up as mentioned in the "Installation" section.

Furthermore, familiarize yourself with the middleware included in `app/Http/Middleware` for authentication workflows and modify as needed to suit your application.

### Usage

Start by registering your application as a Shopify embedded app through your Shopify Partner dashboard and set the appropriate redirect URLs to match your application's routes.

Next, navigate to your Shopify store's admin panel, and you should be able to install and access your Laravel Shopify app directly within the Shopify admin interface.

Consult the Shopify API documentation for further integration possibilities and to understand the scope and capabilities of your embedded app.

### Contributing

Contributions, issues, and feature requests are welcome. Feel free to check [issues page](https://github.com/your-username/your-repo-name/issues) if you want to contribute.

### License

Distributed under the MIT License. See `LICENSE` for more information.
