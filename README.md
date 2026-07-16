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

Перед показом логотипов на сайте нужен symlink:

```bash
php artisan storage:link
```
