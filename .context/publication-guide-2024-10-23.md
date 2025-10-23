# Руководство по публикации MuseWallet SDK на Packagist

**Дата:** 23 октября 2024
**Пакет:** artempuzik/musewallet-sdk-laravel
**Версия:** 1.0.0

## ✅ Текущий статус проекта

Проект готов к публикации! Все необходимые компоненты на месте:

- ✅ Git репозиторий настроен и синхронизирован
- ✅ Все файлы закоммичены
- ✅ README.md подробный и информативный
- ✅ CHANGELOG.md обновлен с датой релиза
- ✅ LICENSE (MIT) присутствует
- ✅ CONTRIBUTING.md и SECURITY.md на месте
- ✅ composer.json правильно настроен
- ✅ Тесты написаны
- ✅ Документация полная

## 📋 Пошаговая инструкция по публикации

### Шаг 1: Проверка GitHub репозитория

1. Убедитесь, что репозиторий публичный:
   - Перейдите на https://github.com/artempuzik/musewallet-sdk-laravel
   - Settings → Danger Zone → Change visibility (должен быть Public)

2. Проверьте настройки репозитория:
   - Описание репозитория заполнено
   - Topics/теги добавлены (laravel, musewallet, payment, card-api, php)
   - Website ссылка на документацию (опционально)

### Шаг 2: Создание релиза на GitHub

```bash
cd /Users/artempuzik/work/nikita/musewallet-sdk

# Закоммитить изменения в CHANGELOG
git add CHANGELOG.md
git commit -m "Update release date in CHANGELOG"
git push origin main

# Создать git тег
git tag -a v1.0.0 -m "Release version 1.0.0 - Initial release"
git push origin v1.0.0
```

Или через GitHub интерфейс:
1. Перейдите на https://github.com/artempuzik/musewallet-sdk-laravel/releases
2. Нажмите "Create a new release"
3. Укажите тег: `v1.0.0`
4. Название: "v1.0.0 - Initial Release"
5. Описание: Скопируйте из CHANGELOG.md секцию [1.0.0]
6. Нажмите "Publish release"

### Шаг 3: Регистрация на Packagist

1. Перейдите на https://packagist.org/
2. Нажмите "Sign in with GitHub" (или создайте аккаунт)
3. Авторизуйтесь через GitHub

### Шаг 4: Публикация пакета на Packagist

1. После входа нажмите "Submit" в верхнем меню
2. Вставьте URL репозитория:
   ```
   https://github.com/artempuzik/musewallet-sdk-laravel
   ```
3. Нажмите "Check"
4. Packagist автоматически проверит composer.json
5. Если все OK, нажмите "Submit"

### Шаг 5: Настройка автоматических обновлений

#### Вариант А: GitHub Webhook (Рекомендуется)

1. На странице вашего пакета в Packagist найдите секцию "GitHub Service Hook"
2. Нажмите на ссылку для настройки webhook
3. Или настройте вручную:
   - GitHub → Settings → Webhooks → Add webhook
   - Payload URL: `https://packagist.org/api/github?username=artempuzik`
   - Content type: `application/json`
   - Secret: (можно оставить пустым)
   - Events: `Just the push event`
   - Active: ✓

#### Вариант Б: GitHub Actions (Альтернатива)

Создайте `.github/workflows/packagist-update.yml`:

```yaml
name: Update Packagist

on:
  push:
    tags:
      - 'v*'
  release:
    types: [published]

jobs:
  packagist:
    runs-on: ubuntu-latest
    steps:
      - name: Update Packagist
        uses: peter-evans/repository-dispatch@v2
        with:
          token: ${{ secrets.PACKAGIST_TOKEN }}
          repository: packagist/packagist
```

### Шаг 6: Проверка публикации

1. Перейдите на https://packagist.org/packages/artempuzik/musewallet-sdk-laravel
2. Убедитесь, что:
   - Версия 1.0.0 отображается
   - README корректно отображается
   - Зависимости правильные
   - Статистика загрузок появилась

### Шаг 7: Тестирование установки

```bash
# В тестовом Laravel проекте
composer require artempuzik/musewallet-sdk-laravel

# Проверьте, что пакет установился
php artisan vendor:publish --tag=musewallet-config
```

## 📝 Публикация обновлений (будущие версии)

### Для patch-версии (1.0.1)
```bash
# 1. Внесите изменения в код
# 2. Обновите CHANGELOG.md
# 3. Коммит и push
git add .
git commit -m "Fix: описание исправления"
git push origin main

# 4. Создайте тег
git tag -a v1.0.1 -m "Release version 1.0.1"
git push origin v1.0.1

# 5. Создайте релиз на GitHub
# Packagist обновится автоматически через webhook
```

### Для minor-версии (1.1.0)
```bash
git tag -a v1.1.0 -m "Release version 1.1.0 - New features"
git push origin v1.1.0
```

### Для major-версии (2.0.0)
```bash
git tag -a v2.0.0 -m "Release version 2.0.0 - Breaking changes"
git push origin v2.0.0
```

## 🔧 Дополнительные настройки

### Добавление бейджей в README

Бейджи уже добавлены в README.md:
- [![Latest Version](https://img.shields.io/packagist/v/artempuzik/musewallet-sdk.svg)](https://packagist.org/packages/artempuzik/musewallet-sdk)
- [![Total Downloads](https://img.shields.io/packagist/dt/artempuzik/musewallet-sdk.svg)](https://packagist.org/packages/artempuzik/musewallet-sdk)
- [![License](https://img.shields.io/packagist/l/artempuzik/musewallet-sdk.svg)](https://packagist.org/packages/artempuzik/musewallet-sdk)

> ⚠️ **Важно:** В README бейджи указывают на `artempuzik/musewallet-sdk`, но в composer.json пакет называется `artempuzik/musewallet-sdk-laravel`. Нужно решить, какое имя использовать.

### Настройка Security Policy

Файл SECURITY.md уже создан. Дополнительно:
1. GitHub → Settings → Security → Security policy
2. Настройте GitHub Security Advisories для уведомлений об уязвимостях

### Настройка Composer версий

В composer.json уже правильно настроен semantic versioning:
```json
"minimum-stability": "stable",
"prefer-stable": true
```

## 🚨 Возможные проблемы и решения

### Проблема 1: Packagist не видит пакет
**Решение:**
- Проверьте, что репозиторий публичный
- Проверьте валидность composer.json: `composer validate`
- Убедитесь, что есть хотя бы один git тег

### Проблема 2: Webhook не работает
**Решение:**
- Проверьте настройки webhook в GitHub
- Проверьте логи webhook в GitHub Settings → Webhooks
- Можно обновить вручную на странице пакета в Packagist (кнопка "Update")

### Проблема 3: Несоответствие имени пакета
**Текущая ситуация:**
- composer.json: `artempuzik/musewallet-sdk-laravel`
- README бейджи: `artempuzik/musewallet-sdk`

**Решение:** Выберите одно название и используйте везде.

### Проблема 4: Composer не может установить пакет
**Решение:**
- Подождите 5-10 минут после публикации
- Очистите Composer cache: `composer clear-cache`
- Попробуйте: `composer require artempuzik/musewallet-sdk-laravel --prefer-source`

## 📊 Мониторинг и аналитика

После публикации отслеживайте:
1. **Packagist статистика:**
   - Количество загрузок
   - Количество установок
   - Популярные версии

2. **GitHub статистика:**
   - Stars, Forks, Issues
   - Contributors
   - Traffic (Insights → Traffic)

3. **GitHub Insights:**
   - Community profile
   - Dependency graph
   - Security alerts

## 🎯 Чек-лист перед публикацией

- [x] Репозиторий публичный на GitHub
- [x] composer.json корректно заполнен
- [x] README.md полный и информативный
- [x] CHANGELOG.md обновлен с датой релиза
- [x] LICENSE файл присутствует
- [x] Тесты написаны и проходят
- [x] Git тег v1.0.0 создан
- [ ] GitHub release создан
- [ ] Пакет опубликован на Packagist
- [ ] Webhook настроен
- [ ] Тестовая установка прошла успешно

## 📚 Полезные ссылки

- **Packagist:** https://packagist.org/
- **Composer документация:** https://getcomposer.org/doc/
- **Semantic Versioning:** https://semver.org/
- **GitHub Releases:** https://docs.github.com/en/repositories/releasing-projects-on-github
- **Laravel Package Development:** https://laravel.com/docs/packages

## 🎉 После публикации

1. Анонсируйте пакет:
   - Laravel News (https://laravel-news.com/submit)
   - Reddit r/laravel
   - Twitter/X
   - Dev.to / Medium статья

2. Добавьте пакет в списки:
   - Awesome Laravel packages
   - Laravel News Package List

3. Мониторьте Issues и Pull Requests
4. Отвечайте на вопросы пользователей
5. Регулярно обновляйте документацию

---

**Автор:** Artem Puzik
**Email:** artem.puzik.it@gmail.com
**GitHub:** https://github.com/artempuzik

