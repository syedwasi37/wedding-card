# Firebase guestbook setup

The invitation remains hosted on Netlify. Guestbook wishes are stored in Cloud Firestore, so the PHP API and Hostinger database are not used by the page.

## Finish connecting the page

1. In Firebase Console, open the `wasi-wedding` project. Its web app config is already set in `index.html`.
2. Create a **Cloud Firestore** database.
3. Open Firestore **Rules**, paste the contents of `firestore.rules`, and publish them.
4. Redeploy the updated site to Netlify.

The page creates the `wedding_guestbook` collection automatically when the first wish is submitted. Wishes are publicly readable so visitors can see them; visitors can add a wish but cannot edit or delete stored wishes through the page. The rules validate the name and message lengths. Firebase web app config is public app configuration; never add a service account/private key to the page.

The existing `api/` PHP files can stay in the repository, but the page no longer calls them.
