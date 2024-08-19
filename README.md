# Vanguard Experiment Feedback API

## Overview

Vanguard Experiment Feedback API is a lightweight Laravel application designed to gather and manage feedback for Vanguard's experiments and feature flags. This API-driven tool allows users to submit feedback and provides endpoints for retrieving and searching through collected feedback.

## Features

- **Feedback Submission**: Users can submit feedback about experiments without authentication.
- **Feedback Retrieval**: Authenticated endpoints for viewing and searching feedback.
- **Email Support**: Optional email collection for follow-ups.

## Requirements

- PHP 8.3+
- Composer

## Quick Start

1. Clone the repository:
   ```
   git clone https://github.com/your-repo/vanguard-feedback-collector.git
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Copy `.env.example` to `.env` and configure your environment variables:
   ```
   cp .env.example .env
   ```

4. Generate application key:
   ```
   php artisan key:generate
   ```

5. Run migrations:
   ```
   php artisan migrate
   ```

6. Start the development server:
   ```
   php artisan serve
   ```

## API Endpoints

- `POST /api/feedback`: Submit feedback
- `GET /api/feedback`: Retrieve feedback (requires API key)
- `GET /api/feedback/search`: Search feedback (requires API key)

For detailed API documentation, please refer to our [API Documentation](https://docs.vanguardbackup.com/api/introduction).

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for more details.

## License

This project is licensed under the MIT Licence - see the [LICENCE](LICENSE) file for details.

## Contact

For any queries or support, please contact us at support@vanguardbackup.com.
