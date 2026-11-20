# cobrachicken

cobrachicken is a community news site for Canada in the style of Hacker News. Readers submit links and questions about Canadian news, upvote what they find worth reading, and discuss it in threaded comments. The name comes from "cobra chicken", the Canadian nickname for the Canada goose.

It is a Laravel 13 app that uses Postgres for storage, Fortify for accounts, and jQuery for the parts of the page that update without a reload.

## What the site does

- The front page ranks stories with the Hacker News formula, so upvotes pull a story up and age pulls it down. `/newest` lists stories by submission time, `/ask` lists text posts, and `/comments` lists the latest comments.
- A story can carry a province or territory tag. `/provinces` links to a ranked list for each of the 13.
- A story can carry a politics tag. The **show politics** checkbox on the front page hides tagged stories there and remembers the choice in a cookie.
- Upvotes, unvotes, replies, and collapsing a comment thread happen in place. Voting and replying also work with JavaScript turned off.
- `/rss` is an RSS feed of the front page.
- People sign in with a username. Uniqueness and login ignore case, so `Demo` and `demo` are the same account.

## Run it locally

You need PHP 8.3 or later with the `pdo_pgsql` extension, Composer, Node 22.18 or later in the 22 line, 24.11 or later in the 24 line, or Node 26 and later, and a running Postgres server.

1. Create two databases, one for the app and one for the tests:

	```sh
	createdb cobrachicken
	createdb cobrachicken_test
	```

2. Copy `.env.example` to `.env`. Set `DB_USERNAME` and `DB_PASSWORD` to your Postgres credentials.
3. Install the dependencies, generate the app key, run the migrations, and build the front end:

	```sh
	composer run setup
	```

4. Fill the database with sample data:

	```sh
	php artisan db:seed
	```

	The seeder creates 60 sample stories with comments and votes. Every sample link points to `example.ca` or `news.example.com`. It also creates the account `demo` with the password `password`.

5. Start the development processes, including the app server and Vite:

	```sh
	composer run dev
	```

6. Open http://localhost:8000.

Locally, Laravel writes password reset emails to `storage/logs/laravel.log` because `MAIL_MAILER` is `log`.

## Run the tests

The tests run on Postgres because the ranking query uses Postgres SQL. `phpunit.xml` points them at the `cobrachicken_test` database. They read the host, username, and password from your `.env`.

```sh
php artisan test --compact
```

## Where the code lives

- `app/Models/Story.php` holds the ranking, newest, ask, and politics query scopes.
- `app/Models/Concerns/Votable.php` is shared by stories and comments. It records the author's own vote when an item is created and decides whether a viewer sees the upvote arrow or the unvote link.
- `app/Actions/ToggleVote.php` adds or removes a vote. It changes points and karma only when a vote row was actually inserted or deleted, so sending the same vote twice has no extra effect.
- `app/Enums/Province.php` is the one list of provinces and territories. Validation, the submit form, the province pages, and the FAQ all read from it.
- `app/Support/PlainText.php` turns story text and comment bodies into HTML. It escapes HTML, splits paragraphs on blank lines, and links URLs.
- `resources/js/app.js` holds all the jQuery.
- The site is styled with Tailwind utility classes in the Blade views. `resources/css/app.css` holds only the few rules that the jQuery toggles (`.nosee`, `.replyform`, `.coll`), and shared form controls live in `resources/views/components/`.

## License

cobrachicken is open source under the [MIT license](LICENSE).
