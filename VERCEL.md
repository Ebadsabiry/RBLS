# Deploy RBLS to Vercel

1. In Vercel, choose **Add New > Project** and import `Ebadsabiry/RBLS`.
2. Use the repository root and **Other** as the framework. The committed `vercel.json` skips npm installation and runs `node scripts/build-vercel.mjs` to copy public assets into `vercel-public`, excluding PHP source and dotfiles. Leave those overrides as configured. Use **Node.js 22.x** (also specified in `package.json`).
3. Add `APP_KEY` to the Production and Preview environment variables. Generate a new production key locally:

   ```powershell
   php artisan key:generate --show
   ```

   Copy the complete `base64:...` value into Vercel. Do not commit it, and keep the same key across redeployments. This command prints a new key without changing the local `.env` file.

4. Add `APP_URL=https://your-project.vercel.app` for Production, or set it to your custom domain after connecting one. Without an explicit URL, the deployment URL is used. Leave `APP_URL` unset for Preview so each preview uses its own URL.
5. Click **Deploy**. Check `/`, `/about`, `/services`, `/projects`, `/company-profile`, `/contact`, images, and the company profile/portfolio downloads.

## Enable contact form delivery

Vercel uses email delivery instead of local inquiry files. Configure a real SMTP account under **Settings > Environment Variables**, then redeploy:

| Variable | Value |
| --- | --- |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | Your SMTP provider's hostname |
| `MAIL_PORT` | Usually `587` (STARTTLS) or `465` (implicit TLS) |
| `MAIL_SCHEME` | `smtp` for port 587; `smtps` for port 465 |
| `MAIL_USERNAME` | Your SMTP username |
| `MAIL_PASSWORD` | Your SMTP password or app password |
| `MAIL_FROM_ADDRESS` | A sender address approved by your provider |
| `MAIL_FROM_NAME` | `Roman Bahram Logistic Services` |

Messages go to `info@romanbahram.com`; optionally override with `INQUIRY_RECIPIENT`. The visitor's email is used as Reply-To. No SMTP account or credentials are included in this repository.

Until SMTP is configured, pages and direct phone/email links work, but form submissions show a delivery error. The form only reports success after the transport accepts the email. Provider acceptance does not guarantee inbox delivery; test using a real inquiry after deployment.

## Runtime behavior

- PHP runs through the pinned community runtime [`vercel-php@0.9.0`](https://github.com/vercel-community/php), which uses PHP 8.5. See [Vercel runtimes](https://vercel.com/docs/functions/runtimes).
- Only the generated `vercel-public` assets are served as static content. PHP source and dotfiles are excluded from that directory. The homepage explicitly routes to Laravel before static directory-index resolution; other application requests go through `api/index.php` as well.
- Production defaults, encrypted cookie sessions, stderr logs, synchronous mail, and writable framework paths are configured in `bootstrap/vercel.php`. Dashboard variables override these defaults. No database is required for the current pages or mail submission flow.
- `/tmp` is only used for disposable framework files. Inquiries are never archived there in mail mode. Keep the Vercel default `INQUIRY_DELIVERY=mail`.
- The default file cache provides a per-instance five-per-minute contact limit. For a limit shared across serverless instances, configure an external database/Redis cache supported by Laravel and set `CACHE_STORE` with its connection variables. Cookie sessions have size limits; use an external session store if the form grows substantially.
- `.env`, private inquiries, local backups, SQLite databases, vendor files, and node_modules are excluded from uploads. The PHP runtime installs production Composer dependencies from `composer.lock`.
- Local XAMPP behavior continues to save inquiries privately and optionally notify by mail.

## Checks

```powershell
php tests/website-smoke.php
php tests/vercel-smoke.php
node tests/site-interactions.mjs
node tests/vercel-routing.mjs
```

The serverless checks use a temporary key and capture mail in memory. They never send real email.

## Troubleshooting

- If the homepage displays or downloads PHP source, deploy the latest commit. Earlier configuration served `public/index.php` as a static directory index. The generated output directory now excludes it.
- If `/up` works but normal pages display **Server Error**, check that `APP_KEY` is set to a valid generated key for the deployment's environment. Generating it locally does not add it to Vercel. Save the variable and redeploy; environment changes do not update existing deployments.
- If pages still fail after redeployment, open the deployment's **Runtime Logs** for the exact Laravel exception. Keep `APP_DEBUG=false` on the public website.
