# Roman Bahram Logistic Services website

A Laravel website with Home, About, Services, Projects, Company Profile, and Contact pages. Content is based on the supplied RBLS company profile and past experience portfolio.

## Deploy to Vercel

See [VERCEL.md](VERCEL.md) for repository import, the required `APP_KEY`, and SMTP setup. Deployment routing and serverless Laravel defaults are included. The current pages use the committed public CSS/JavaScript and do not need a Vite build.

## Run locally

From the project directory:

```powershell
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```

Open http://127.0.0.1:8000. XAMPP can also serve the application's public directory. Configure a virtual host with its document root pointing to public; do not expose the project root.

The public stylesheets and JavaScript are served directly. No npm build or external animation library is needed for these pages.

## Design and content

- Shared layout: resources/views/layouts/app.blade.php
- Pages: resources/views/pages/
- Shared design and interactions: public/css/site.css and public/js/site.js
- Homepage artwork and layout: public/css/home.css
- Portfolio records: resources/data/projects.json, containing all 53 supplied assignments
- Original downloadable materials: public/RBLS Data/

The site uses a lightweight animated route network, shorter scroll reveals, keyboard-accessible navigation, combined portfolio search and filters, and the visitor's reduced-motion preference. Background canvas rendering pauses outside the viewport and when the tab is hidden. Content remains readable without JavaScript.

The supplied profile contains one inconsistent reference to a 2019 establishment date. The website uses 2022, which is repeated in the corporate details and president's message. The portfolio identifies Norwegian Afghanistan Committee as NAC; it is retained as recorded. Annual turnover is presented as approximate and attributed to the supplied profile.

## Inquiries

POST /contact validates inquiries, limits submissions to five per minute per client, rejects the honeypot field, and saves accepted inquiries under storage/app/private/inquiries/<reference>.json. This folder is outside the public web directory. Do not copy it into public. Restrict server access and manage retention according to your organization's needs.

On Vercel, `INQUIRY_DELIVERY=mail` sends directly through a configured mail transport and only acknowledges successful sending. It does not archive inquiries on the temporary filesystem. See the deployment guide for SMTP variables.

The default MAIL_MAILER=log does not deliver email. Accepted inquiries are still saved privately. To notify info@romanbahram.com, configure a real mail transport and the sender identity in .env, then clear the configuration cache. No mail credentials were invented or changed. SMTP notification errors leave the saved inquiry intact and record only its reference in the application log.

## Verification

A standalone application check is available without PHPUnit:

```powershell
C:\xampp\php\php.exe tests/website-smoke.php
```

It verifies all pages, shared assets, headings, image and document paths, 53 portfolio records, inquiry validation and private storage, locally captured notification addressing, and rate limits. It uses an isolated temporary storage directory and sends no real email.

Frontend interaction checks run without browser dependencies:

```powershell
node tests/site-interactions.mjs
```

These check combined portfolio filters, empty states, reset, mobile navigation controls, Escape handling, duplicate-submission prevention, and restoring the form after back navigation.

Additional PHPUnit feature tests are in tests/Feature/SiteTest.php. PHPUnit is not currently installed in this checkout; install Composer development dependencies before running artisan test.

Original view, controller, and route files were backed up under storage/app/site-backup-20261009/ before the update.
