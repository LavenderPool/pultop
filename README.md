# Pultop

## Импорт банков

Единоразовый парсинг списка банков с https://pultop.uz/banks/ (карточка банка, логотип, реквизиты) в таблицу `banks`:

```bash
php artisan banks:parse
```

Опции:

```bash
php artisan banks:parse --dry-run
php artisan banks:parse --limit=2
```

## Рейтинг банков

Парсинг таблицы с https://pultop.uz/banks-of-uzbekistan/ в `bank_rating_snapshots` / `bank_rating_rows`:

```bash
php artisan banks:parse-rating
php artisan banks:parse-rating --dry-run
```

Публичная страница: `/banks-of-uzbekistan`.

## Импорт статей и новостей

Единоразовый импорт материалов из вкладок «Статьи / Новости / Новости банков / События и Акции» с https://pultop.uz/ (WP REST) в таблицу `articles`:

```bash
php artisan articles:parse
```

Опции:

```bash
php artisan articles:parse --dry-run
php artisan articles:parse --limit=5
php artisan articles:parse --category=news
```

Категории: `article`, `news`, `bank_news`, `events`.

Публичные страницы: `/articles`, `/articles/{slug}`, `/category/{stati|novosti|novosti-bankov|sobytiya-i-akcii}`.

## Импорт кредитов

Единоразовый парсинг кредитов с https://pultop.uz/ (admin-ajax `load_more_credits` + детальные страницы `/credits/{slug}/`) в таблицы `credits` / `credit_types` / `credit_rate_rows` / `credit_conditions`:

```bash
php artisan credits:parse
```

Опции:

```bash
php artisan credits:parse --dry-run
php artisan credits:parse --limit=2
php artisan credits:parse --type=avtokredity-v-uzbekistane
```

Типы: `avtokredity-v-uzbekistane`, `dlya-biznesa`, `ipotechnye-kredity-v-uzbekistane`, `bankovskie-mikrozajmy-v-uzbekistane`, `obrazovatelnye-kredity-v-uzbekistane`, `overdraft-v-uzbekistane`, `potrebitelskie-krediti`, `biznesmenam-v-uzbekistane`.

Публичные страницы: хаб `/credits` (и `/vse-kredity-uzbekistana`) с плитками типов и TOP-10 автокредитов; листинг с фильтрами `/credit-type/{type}` и алиасы меню (`/potrebitelskie-krediti/` и т.д.); деталь `/credits/{slug}`.

## Импорт карт

Единоразовый парсинг банковских карт с https://pultop.uz/cards/ (admin-ajax `load_more_cards` + детальные страницы `/cards/{slug}/`) в таблицы `cards` / `card_conditions`:

```bash
php artisan cards:parse
```

Опции:

```bash
php artisan cards:parse --dry-run
php artisan cards:parse --limit=2
```

Публичные страницы: `/cards`, `/cards/{slug}`, алиас меню `/sravnenie-bankovskih-kart`.

## Импорт вкладов

Единоразовый парсинг вкладов с https://pultop.uz/deposits/ (admin-ajax `load_more_deposits` + детальные страницы `/deposits/{slug}/`) в таблицы `deposits` / `deposit_rates` / `deposit_conditions`:

```bash
php artisan deposits:parse
```

Опции:

```bash
php artisan deposits:parse --dry-run
php artisan deposits:parse --limit=2
```

Публичные страницы: `/deposits`, `/deposits/{slug}` (редирект с `/vkladi` → `/deposits`).

Перед показом логотипов на сайте нужен symlink:

```bash
php artisan storage:link
```
