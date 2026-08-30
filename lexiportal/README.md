# LexPortal PHP Prototype

This folder converts the provided static LexPortal HTML preview into a small Vanilla PHP application backed by SQLite.

## Features Carried Over

- Public homepage with trust indicators, quick search, feature cards, research preview, client portal teaser, and latest resources.
- Research center with keyword/type/subject filters.
- Resource library by legal category.
- AI legal assistant demo with deterministic Ugandan-law responses and saved question history.
- Client/practitioner registration and login using PHP sessions and password hashes.
- Client portal with cases, documents, messages, and document upload persistence.
- Lawyers directory and consultation/contact form.
- Basic admin snapshot for database counts and contact requests.
- Separate admin sign-in using the seeded admin account.
- Theme toggle, responsive navigation, modal search/login/register, filter chips, and browser voice search where supported.

## Run

```bash
php -S localhost:8000 -t lexiportal
```

Demo accounts:

- Client: `client@demo.lexportal.ug` / `demo1234`
- Lawyer: `lawyer@demo.lexportal.ug` / `demo1234`
- Admin: `admin@lexportal.ug` / `AdminDemo#2026`

The SQLite database is created automatically at `data/lexiportal.sqlite`.
