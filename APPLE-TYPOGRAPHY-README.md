# 🎨 Apple Newsroom Typography - Извлеченные CSS значения

## 📋 Обзор

Этот репозиторий содержит точные CSS значения типографики, извлеченные из официального сайта Apple Newsroom (https://www.apple.com/newsroom/) 28 февраля 2026 года.

## 📁 Файлы в проекте

### 1. **apple-newsroom-typography.md**
Подробная документация со всеми CSS значениями в формате Markdown. Включает:
- Font-family для разных языков
- Размеры шрифтов для всех элементов
- Line-height, letter-spacing, font-weight
- Цветовую палитру
- Адаптивные breakpoints
- Примеры использования

### 2. **apple-newsroom-styles.css**
Готовый к использованию CSS файл с:
- CSS переменными (Custom Properties)
- Стилями для всех компонентов
- Адаптивными медиа-запросами
- Поддержкой темной темы
- Utility классами

### 3. **apple-newsroom-typography.json**
Структурированные данные в JSON формате для программного использования:
```json
{
  "body": {
    "fontSize": "17px",
    "lineHeight": "1.4705882353",
    "color": "#1d1d1f"
  },
  "newsCards": {
    "title": {
      "fontSize": "21px",
      "fontWeight": "600"
    }
  }
}
```

### 4. **apple-typography-summary.html**
Интерактивная HTML страница с визуальными примерами всех стилей. Откройте в браузере для просмотра.

### 5. **parse-apple-css.php**
PHP скрипт для парсинга CSS файлов и извлечения значений.

### 6. **extract-apple-css.js**
JavaScript код для извлечения computed styles напрямую из браузера.

### 7. **apple-css-extractor.html**
HTML инструмент для извлечения CSS значений через iframe (может быть заблокирован CORS).

## 🚀 Быстрый старт

### Использование CSS файла

```html
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="apple-newsroom-styles.css">
</head>
<body>
    <h1>Заголовок в стиле Apple</h1>
    <div class="tile">
        <div class="tile__category">PRESS RELEASE</div>
        <h3 class="tile__headline">Apple announces new product</h3>
        <div class="tile__date">February 28, 2026</div>
        <p class="tile__description">Lorem ipsum dolor sit amet...</p>
    </div>
</body>
</html>
```

### Использование CSS переменных

```css
.my-element {
    font-family: var(--font-family-text);
    font-size: var(--font-size-body);
    color: var(--color-text-primary);
    background-color: var(--color-bg-white);
}
```

### Использование JSON данных

```javascript
fetch('apple-newsroom-typography.json')
    .then(response => response.json())
    .then(data => {
        console.log(data.body.fontSize); // "17px"
        console.log(data.colors.text.primary); // "#1d1d1f"
    });
```

## 📊 Ключевые значения

### Шрифты
- **Основной:** SF Pro Text
- **Заголовки:** SF Pro Display
- **Базовый размер:** 17px
- **Основной вес:** 600 (Semibold)

### Цвета
- **Текст:** #1d1d1f (почти черный)
- **Вторичный:** #6e6e73 (серый)
- **Ссылки:** #0071e3 (Apple Blue)
- **Фон:** #ffffff (белый)

### Типографика карточек новостей
- **Заголовок:** 21px / 600 / 1.235 line-height
- **Категория:** 14px / 600 / uppercase
- **Дата:** 14px / 400 / #6e6e73
- **Описание:** 17px / 400 / 1.471 line-height

### Breakpoints
- **Desktop Large:** 1441px+
- **Tablet:** ≤1068px
- **Mobile:** ≤734px

## 🎨 Цветовая палитра

### Текст
```css
--color-text-primary: #1d1d1f;
--color-text-secondary: #6e6e73;
--color-text-link: #0071e3;
--color-text-white: #f5f5f7;
```

### Фон
```css
--color-bg-white: #ffffff;
--color-bg-light-gray: #f5f5f7;
--color-bg-medium-gray: #e8e8ed;
--color-bg-dark-gray: #1d1d1f;
--color-bg-black: #000000;
```

### Акценты
```css
--color-apple-blue: #0071e3;
--color-focus: #0071e3;
--color-hover-gray: #e8e8ed;
--color-hover-dark: #333336;
```

## 📱 Адаптивность

Все размеры шрифтов адаптируются на разных устройствах:

| Элемент | Desktop | Tablet | Mobile |
|---------|---------|--------|--------|
| Card Title | 21px | 19px | 17px |
| Featured Title | 40px | 32px | 28px |
| Category | 14px | 12px | 12px |
| Date | 14px | 12px | 12px |

## 🌙 Темная тема

Файл CSS включает поддержку темной темы через класс `.theme-dark`:

```html
<body class="theme-dark">
    <!-- Контент автоматически адаптируется -->
</body>
```

## 🔧 Технические детали

### Извлечение данных
Данные были извлечены из следующих CSS файлов:
- `https://www.apple.com/newsroom/styles/site.built.css`
- `https://www.apple.com/newsroom/styles/tiles.built.css`
- `https://www.apple.com/newsroom/styles/landing.built.css`

### Шрифты
SF Pro загружается с Apple CDN:
```
https://www.apple.com/wss/fonts?families=SF+Pro,v3|SF+Pro+Icons,v3
```

Используемые варианты:
- SF Pro Text: Regular (400), Semibold (600), Bold (700)
- SF Pro Display: Regular (400), Semibold (600), Bold (700)

### Line-height
Apple использует точные дробные значения для line-height:
- Body: 1.4705882353 (25px при 17px font-size)
- Card Title: 1.2353641176 (26px при 21px font-size)
- Featured Title: 1.1 (44px при 40px font-size)

### Letter-spacing
Тонкая настройка кернинга:
- Body: -0.022em
- Card Title: .012em
- H1: -0.003em
- Category: .012em

## 📖 Примеры использования

### Карточка новости
```html
<article class="tile">
    <div class="tile__category">PRESS RELEASE</div>
    <h3 class="tile__headline">Apple accelerates U.S. manufacturing</h3>
    <div class="tile__date">February 24, 2026</div>
    <p class="tile__description">
        Apple today announced a major expansion of its U.S. manufacturing...
    </p>
</article>
```

### Кнопки
```html
<button class="nr-cta-primary">Learn more</button>
<button class="nr-cta-secondary">View all</button>
```

### Заголовки
```html
<h1>Newsroom</h1>
<h2>Latest News</h2>
```

## 🔍 Дополнительные ресурсы

- [Apple Newsroom](https://www.apple.com/newsroom/)
- [SF Pro Font](https://developer.apple.com/fonts/)
- [Apple Design Resources](https://developer.apple.com/design/resources/)

## 📝 Лицензия

Эти данные извлечены из публично доступного сайта Apple для образовательных целей. SF Pro является проприетарным шрифтом Apple Inc.

## 👤 Автор

Извлечено: 28 февраля 2026
Источник: https://www.apple.com/newsroom/

---

**Примечание:** SF Pro - это проприетарный шрифт Apple. Для использования в коммерческих проектах требуется лицензия от Apple. В качестве альтернативы можно использовать системные шрифты:

```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```
