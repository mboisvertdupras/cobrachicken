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

## News fetcher

`php artisan news:fetch` gives the site fresh content. It reads the Canadian news RSS and Atom feeds listed in `config/news.php` (CBC national and regional, CTV, Global News, and several independent and northern outlets) and submits new stories through the same code path as the submit form, so each story starts with one point and its author's vote.

- Each feed in `config/news.php` has a name, a url, an optional province, and a politics flag, and its stories get those tags. When the same link appears in several feeds, the story is tagged politics if any of them is a politics feed, and gets a province only if the feeds agree on one.
- Links are compared without `utm_*` parameters, CBC's `cmp` parameter, or the fragment, so a story that is already on the site is not posted again, and neither is the same link from two feeds in one run.
- Items older than the maximum age, items with no date, and items without a usable title or http(s) link are skipped. Titles are decoded and cut to fit the 80-character limit.
- A feed that errors, times out, or is not a feed is logged and skipped. The command fails only when every feed fails.
- Each run posts at most `NEWS_MAX_STORIES_PER_RUN` stories and at most `NEWS_MAX_STORIES_PER_FEED` from one feed, picking each feed's newest stories in turn so that one feed cannot fill the front page. `--limit=` and `--per-feed=` override the caps for a single run.

| Variable | Default | Meaning |
| --- | --- | --- |
| `NEWS_POSTER_USERNAME` | `mbd` | The account the stories are posted as. Matched without regard to case. |
| `NEWS_MAX_STORIES_PER_RUN` | `5` | Most stories posted per run. |
| `NEWS_MAX_STORIES_PER_FEED` | `2` | Most stories posted from one feed per run. |
| `NEWS_MAX_AGE_HOURS` | `48` | Items published longer ago than this are skipped. |
| `NEWS_TIMEOUT_SECONDS` | `10` | Connect and response timeout for each feed. |
| `NEWS_USER_AGENT` | `cobrachicken/1.0` | User-Agent sent to the feeds. CBC's CDN refuses some agents, including Guzzle's default and agents that contain a `+https://` contact link. |

The poster account must already exist. The command never creates it and exits with an error when it is missing. Register the account through the site, or create it from `php artisan tinker` with a random password.

The command is scheduled to run hourly, and a run is skipped if the previous one is still going. The schedule only runs if the server calls the Laravel scheduler, so production needs this cron entry:

```cron
* * * * * cd /path/to/cobrachicken && php artisan schedule:run >> /dev/null 2>&1
```

Alternatively, keep `php artisan schedule:work` running under a process supervisor.

## Where the code lives

- `app/Models/Story.php` holds the ranking, newest, ask, and politics query scopes.
- `app/Models/Concerns/Votable.php` is shared by stories and comments. It records the author's own vote when an item is created and decides whether a viewer sees the upvote arrow or the unvote link.
- `app/Actions/SubmitStory.php` holds the story validation rules and creates a story. The submit form and the news fetcher both use it.
- `app/Console/Commands/FetchNews.php` is the news fetcher. `app/Support/FeedParser.php` reads RSS and Atom, and `app/Support/StoryUrl.php` normalizes links for duplicate checks.
- `app/Actions/ToggleVote.php` adds or removes a vote. It changes points and karma only when a vote row was actually inserted or deleted, so sending the same vote twice has no extra effect.
- `app/Enums/Province.php` is the one list of provinces and territories. Validation, the submit form, the province pages, and the FAQ all read from it.
- `app/Support/PlainText.php` turns story text and comment bodies into HTML. It escapes HTML, splits paragraphs on blank lines, and links URLs.
- `resources/js/app.js` holds all the jQuery.
- The site is styled with Tailwind utility classes in the Blade views. `resources/css/app.css` holds only the few rules that the jQuery toggles (`.nosee`, `.replyform`, `.coll`), and shared form controls live in `resources/views/components/`.

## License

cobrachicken is open source under the [MIT license](LICENSE).
