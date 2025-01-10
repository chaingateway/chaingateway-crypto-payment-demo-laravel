# Laravel Crypto Payments Showcase with Chaingateway API

This project is a **Payment Gateway** built with Laravel, allowing users to pay using **Tron (TRX)** or **USDT (TRC20)**. The gateway uses the **[Chaingateway API](https://chaingateway.io/)** to interact with the blockchain for wallet generation, transaction monitoring, and fund forwarding.

---

## Features

- **Payment Sessions**: Dynamically generate a wallet for each session, track the requested amount, received amount, and payment status.
- **Currency Support**: Choose between **TRX** and **USDT (TRC20)** as payment options.
- **Real-time Webhooks**: Automatically verify transactions and forward funds to a secure **cold wallet**.
- **Simple Integration**: Add payments to your Laravel application with minimal effort.

---

## Why Use Chaingateway?

**[Chaingateway](https://chaingateway.io/)** makes blockchain integrations seamless by providing a powerful and reliable API for:
- Generating blockchain wallets.
- Monitoring incoming payments.
- Sending transactions programmatically.

### Benefits of Chaingateway:
- **Multi-chain Support**: Supports multiple blockchains including Tron, Ethereum, Binance Smart Chain (BSC), Polygon, and Bitcoin.
- **Developer-Friendly API**: Intuitive and easy-to-use API with comprehensive documentation.
- **Secure**: Enterprise-grade security ensures your transactions and wallets are safe.

Explore Chaingateway’s features and get started today:  
👉 [Chaingateway Developers](https://chaingateway.io/developers)  
👉 [API Documentation](https://chaingateway.io/docs)

---

## Disclaimer

This project is a **basic showcase** of how to integrate a payment gateway using Laravel and Chaingateway. While it currently supports **Tron (TRX)** and **USDT (TRC20)**, it can be adapted to work with other blockchains supported by Chaingateway, such as:
- **Ethereum**
- **Polygon**
- **Binance Smart Chain (BSC)**
- **Bitcoin**

**Important:** For production use, consider additional features such as advanced transaction validation, error handling, security measures, and scalability optimizations.

---

## Installation

Follow these steps to deploy the project:

### 1. Clone the Repository

```bash
git clone https://github.com/chaingateway/chaingateway-crypto-payment-demo-laravel.git
cd laravel-payment-gateway
```

### 2. Install Dependencies

Run the following command to install Laravel dependencies:

```bash
composer install
```

### 3. Set Up the Environment

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Edit the `.env` file to configure the following:

```env
APP_NAME=Laravel
APP_URL=http://your-app-url.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CHAINGATEWAY_API_URL=https://api.chaingateway.io/api/v2
CHAINGATEWAY_API_KEY=your_chaingateway_api_key
CHAINGATEWAY_NETWORK=testnet
COLD_WALLET=your_cold_wallet_address
```

- Replace `your_chaingateway_api_key` with the API key you create on **[Chaingateway](https://app.chaingateway.io/user/api-tokens)**.
- Set `CHAINGATEWAY_NETWORK` to `mainnet` for production or `testnet` for testing.

### 4. Run Migrations

Set up the database by running migrations:

```bash
php artisan migrate
```

### 5. Serve the Application

Start the Laravel development server:

```bash
php artisan serve
```

Visit `http://127.0.0.1:8000/payment` in your browser to access the application.

---

## Usage

### Starting a Payment Session

1. Navigate to `/payment`.
2. Enter the **amount** and select the **currency** (TRX or USDT).
3. Click **Start Payment Session** to generate a new wallet.

### Viewing Payment Sessions

After starting a payment session:
- The app displays the wallet address to send funds to.
- It shows the requested amount, received amount, and payment status.

### Payment Processing

1. Once a user sends funds to the generated wallet:
   - The app verifies the transaction using Chaingateway’s webhook.
   - Updates the payment session status.
   - Automatically forwards funds to the configured **cold wallet**.

---

## Project Structure

The project includes the following components:

- **Models**:
  - `PaymentSession`: Tracks session details like `amount`, `received_amount`, `currency`, and `status`.
  - `Wallet`: Stores generated wallet addresses and their private keys.
- **Controllers**:
  - `PaymentController`: Handles payment session creation, display, and webhook processing.
- **Views**:
  - `/payment`: Form to start a payment session.
  - `/payment-session/{id}`: Displays wallet details and session status.

---

## Testing

### Webhook Simulation

To simulate the webhook:
1. Use a tool like Postman to send a `POST` request to `/webhook`.
2. Provide the transaction data in the body of the request.

Example payload:

```json
{
    "id": "9def5bdf-9f40-4c25-8b4b-3b38bdbf154e",
    "webhook_id": "9def5b65-dedc-487e-995a-a940596b218e",
    "from": "TD5QjXcKdS8yhG2uwSgxvbQFdK69ycH8D7",
    "to": "TXbhwYpomVzyU3MjZdFTJk1LV29zm8r9nG",
    "blocknumber": "53510937",
    "datetime": "2025-01-10 12:54:01",
    "tokenid": null,
    "type": "Tron",
    "contractaddress": null,
    "txid": "50c258cffc6eac2a134637d62619440e9299c16281a3a3219468dc078b85565a",
    "amount": 50
}
```

Verify that:
- The `received_amount` is updated.
- The session status changes to **Completed** once the `received_amount` equals the `amount`.

---

## Contributions

Contributions are welcome! If you’d like to add new features or fix bugs, please fork the repository and submit a pull request.

---

## About Chaingateway

Chaingateway is a blockchain API that simplifies blockchain development, making it accessible to developers of all skill levels. Whether you’re building a payment gateway, a decentralized app, or simply need to interact with blockchains, **Chaingateway** provides the tools you need.

Check it out today at **[chaingateway.io](https://chaingateway.io/)**! 🚀
