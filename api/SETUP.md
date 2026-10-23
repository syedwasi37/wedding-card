# Hostinger guestbook API setup

The invitation stays on Netlify. This PHP API must be uploaded to the Hostinger website/hosting plan that owns the `u966926210_wasiwedding` database. PHP connects to that database locally; the Netlify page only calls the API over HTTPS.

1. In Hostinger File Manager for that website, create `public_html/api/`.
2. Upload `guestbook.php`, `.htaccess`, and `config.example.php` from this folder.
3. In File Manager, copy or rename `config.example.php` to `config.php`. In `config.php`, set `DB_USER` to the database username shown in Hostinger Database Management and set `DB_PASS` to that database's password. Keep `DB_HOST` as `localhost`. Never put the password in the Netlify files or GitHub.
4. Open the `u966926210_wasiwedding` database in phpMyAdmin, select the SQL tab, and run the contents of `schema.sql`.
5. The API URL is `https://any2convert.com/api/guestbook.php`.
6. The Netlify site's `index.html` is configured to use that API URL. The API allows the supplied origin, `https://mahnoor-wasif.netlify.app`.

After redeploying the Netlify site, guestbook entries will be stored in MySQL and shown to visitors. If the invitation is also opened through a custom domain, add that exact HTTPS origin to `$allowedOrigins` in `guestbook.php`.
