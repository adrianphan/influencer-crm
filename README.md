# Marie Collab CRM

Laravel + Blade CRM for tracking influencer outreach to local businesses and PR agencies.

## Core Features

- Lead management with direct business and PR agency pipelines
- AI-assisted draft generation for outreach email, DM, and follow-up messages
- Creator profile management
- Interaction timeline logging
- Lead finder workflow with Add to CRM flow
- Authentication via Laravel Breeze

## Local Setup

1. Install dependencies:

```bash
composer install
npm install
```

2. Create environment file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure your database in `.env`, then run:

```bash
php artisan migrate
```

4. Optional front-end build:

```bash
npm run dev
```

5. Run the app:

```bash
php artisan serve
```

## Authentication and Access

- CRM routes are protected by the `auth` middleware.
- Unauthenticated users are redirected to login.
- Logout is available in the top navigation.

## Deployment Readiness Checklist

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Set a secure `APP_KEY`
- Configure production database credentials
- Configure `APP_URL` to your domain
- Configure `OPENAI_API_KEY` (if AI generation is enabled)
- Run `php artisan config:cache` and `php artisan route:cache`

## Security Notes

- No API keys should be hardcoded in source files.
- Keep secrets only in environment variables.
- `.env` is ignored by git and should never be committed.
