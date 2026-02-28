# 🎨 Apple Newsroom - Точные CSS значения типографики

Извлечено: 28 февраля 2026
Источник: https://www.apple.com/newsroom/

## 1. Body / Page Font-Family

**Основной шрифт:**
```css
font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
```

**Для разных языков:**
- **Арабский:** `"SF Pro AR", "SF Pro AR Text", "SF Pro Text", "SF Pro Gulf", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif`
- **Японский:** `"SF Pro JP", "SF Pro Text", "SF Pro Icons", "Hiragino Kaku Gothic Pro", "ヒラギノ角ゴ Pro W3", "メイリオ", "Meiryo", "ＭＳ Ｐゴシック", "Helvetica Neue", "Helvetica", "Arial", sans-serif`
- **Корейский:** `"SF Pro KR", "SF Pro Text", "SF Pro Icons", "Apple Gothic", "HY Gulim", "MalgunGothic", "HY Dotum", "Lexi Gulim", "Helvetica Neue", "Helvetica", "Arial", sans-serif`
- **Китайский (упрощенный):** `"SF Pro SC", "SF Pro Text", "SF Pro Icons", "PingFang SC", "Helvetica Neue", "Helvetica", "Arial", sans-serif`
- **Тайский:** `"SF Pro TH", "SF Pro Text", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif`

**Body базовые стили:**
```css
body {
    font-size: 17px;
    line-height: 1.4705882353; /* ~25px */
    color: rgb(29, 29, 31); /* #1d1d1f */
    background-color: rgb(255, 255, 255); /* #ffffff */
    font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
}
```

## 2. Navigation Links (Верхняя навигация)

```css
nav a {
    font-size: 12px;
    font-weight: 400; /* Regular */
    letter-spacing: -0.01em; /* примерно */
    color: #f5f5f7; /* на темном фоне */
    font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
}
```

**Для локальной навигации Newsroom:**
```css
.ac-ln-menu-link {
    font-size: 14px;
    font-weight: 400;
    line-height: 1.4285914286;
    letter-spacing: -0.016em;
}
```

## 3. News Card Titles (Заголовки карточек новостей)

**Стандартные карточки (2up, 1up):**
```css
.tile__headline {
    font-size: 21px;
    font-weight: 600; /* Semibold */
    line-height: 1.2353641176; /* ~26px */
    letter-spacing: .012em;
    color: #1d1d1f;
    font-family: "SF Pro Display", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
}

@media only screen and (max-width: 1068px) {
    .tile__headline {
        font-size: 19px;
        line-height: 1.2631578947;
    }
}

@media only screen and (max-width: 734px) {
    .tile__headline {
        font-size: 17px;
        line-height: 1.2352941176;
    }
}
```

**Большие карточки (featured):**
```css
.tile-big .tile__headline {
    font-size: 40px;
    font-weight: 600;
    line-height: 1.1;
    letter-spacing: 0em;
}

@media only screen and (max-width: 1068px) {
    .tile-big .tile__headline {
        font-size: 32px;
        line-height: 1.125;
    }
}

@media only screen and (max-width: 734px) {
    .tile-big .tile__headline {
        font-size: 28px;
        line-height: 1.1428571429;
    }
}
```

## 4. Category Labels (PRESS RELEASE, UPDATE, и т.д.)

```css
.tile__category {
    font-size: 14px;
    font-weight: 600; /* Semibold */
    color: #6e6e73;
    text-transform: uppercase;
    letter-spacing: .012em;
    line-height: 1.4285914286;
    font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
}

@media only screen and (max-width: 1068px) {
    .tile__category {
        font-size: 12px;
        line-height: 1.3333733333;
    }
}
```

**Цвета для разных типов:**
- **PRESS RELEASE:** `#6e6e73` (серый)
- **UPDATE:** `#6e6e73` (серый)
- **QUICK READ:** `#6e6e73` (серый)
- **PHOTOS:** `#6e6e73` (серый)
- **APPLE STORIES:** `#6e6e73` (серый)

## 5. News Card Dates (Даты)

```css
.tile__date {
    font-size: 14px;
    font-weight: 400; /* Regular */
    color: #6e6e73;
    line-height: 1.4285914286;
    font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
}

@media only screen and (max-width: 1068px) {
    .tile__date {
        font-size: 12px;
        line-height: 1.3333733333;
    }
}
```

## 6. Page Background Color

```css
body {
    background-color: #ffffff; /* белый */
}

/* Для темной темы */
.theme-dark body {
    background-color: #000000; /* черный */
}
```

**Дополнительные фоновые цвета:**
- Светло-серый (для секций): `#f5f5f7`
- Средне-серый: `#e8e8ed`
- Темно-серый: `#1d1d1f`

## 7. Card Background Color

```css
.tile {
    background-color: #ffffff; /* белый */
}

/* Для темной темы */
.theme-dark .tile {
    background-color: #1d1d1f; /* темно-серый */
}
```

**Специальные карточки:**
- Quick Read: `#f5f5f7` (светло-серый фон)
- Featured cards: `transparent` или `#ffffff`

## 8. Description/Excerpt Text

```css
.tile__description {
    font-size: 17px;
    font-weight: 400; /* Regular */
    line-height: 1.4705882353; /* ~25px */
    color: #1d1d1f;
    letter-spacing: -0.022em;
    font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
}

@media only screen and (max-width: 1068px) {
    .tile__description {
        font-size: 17px;
        line-height: 1.4705882353;
    }
}

@media only screen and (max-width: 734px) {
    .tile__description {
        font-size: 17px;
        line-height: 1.4705882353;
    }
}
```

## 9. Дополнительные элементы

### Heading Styles

**H1 (Page Title):**
```css
h1 {
    font-size: 48px;
    font-weight: 600;
    line-height: 1.0833333333;
    letter-spacing: -0.003em;
    color: #1d1d1f;
    font-family: "SF Pro Display", "SF Pro Icons", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
}
```

**H2 (Section Headings):**
```css
h2 {
    font-size: 40px;
    font-weight: 600;
    line-height: 1.1;
    letter-spacing: 0em;
    color: #1d1d1f;
}
```

### Links

```css
a {
    color: #0071e3; /* Apple Blue */
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

a:focus {
    outline: 2px solid #0071e3;
    outline-offset: 2px;
}
```

### Buttons (CTA)

```css
.nr-cta-primary {
    font-size: 17px;
    font-weight: 400;
    line-height: 1.1764805882;
    letter-spacing: -0.022em;
    color: #ffffff;
    background-color: #0071e3;
    border-radius: 980px;
    padding: 12px 24px;
}

.nr-cta-secondary {
    font-size: 17px;
    font-weight: 400;
    line-height: 1.1764805882;
    letter-spacing: -0.022em;
    color: #0071e3;
    background-color: transparent;
    border: 1px solid #0071e3;
    border-radius: 980px;
    padding: 12px 24px;
}
```

## 10. Цветовая палитра

### Основные цвета текста:
- **Основной текст:** `#1d1d1f` (почти черный)
- **Вторичный текст:** `#6e6e73` (серый)
- **Ссылки:** `#0071e3` (синий Apple)
- **Белый текст (на темном):** `#f5f5f7` (почти белый)

### Фоновые цвета:
- **Белый:** `#ffffff`
- **Светло-серый:** `#f5f5f7`
- **Средне-серый:** `#e8e8ed`
- **Темно-серый:** `#1d1d1f`
- **Черный:** `#000000`

### Акцентные цвета:
- **Apple Blue:** `#0071e3`
- **Focus Blue:** `#0071e3`
- **Hover Gray:** `#e8e8ed`
- **Dark Hover:** `#333336`

## 11. Font Weights (используемые)

- **400** - Regular (обычный текст)
- **500** - Medium (редко используется)
- **600** - Semibold (заголовки, labels)
- **700** - Bold (редко используется)

## 12. Responsive Breakpoints

```css
/* Desktop (Large) */
@media only screen and (min-width: 1441px) { }

/* Desktop (Standard) */
@media only screen and (max-width: 1068px) { }

/* Tablet / Mobile */
@media only screen and (max-width: 734px) { }
```

## 13. Специфичные классы и их применение

### Tile Variants:
- `.tile-1up` - одна колонка
- `.tile-2up` - две колонки
- `.tile-big` - большая featured карточка
- `.tile-regular` - стандартная карточка
- `.tile-quick-read` - Quick Read карточка
- `.tile-as` - Apple Stories карточка

### Typography Classes:
- `.typography-headline` - стиль заголовка
- `.typography-body` - стиль основного текста
- `.typography-label` - стиль метки
- `.typography-caption` - стиль подписи

## Примечания:

1. **SF Pro** - это проприетарный шрифт Apple, который загружается с их CDN
2. Все размеры шрифтов адаптивны и меняются на разных breakpoints
3. Line-height часто указывается в относительных единицах (без единиц измерения)
4. Letter-spacing используется для тонкой настройки кернинга
5. Цвета используют как HEX, так и RGB форматы
6. Темная тема (`.theme-dark`) меняет цвета на инверсные

---

**Источники:**
- https://www.apple.com/newsroom/styles/site.built.css
- https://www.apple.com/newsroom/styles/tiles.built.css
- https://www.apple.com/newsroom/styles/landing.built.css
- https://www.apple.com/wss/fonts?families=SF+Pro,v3|SF+Pro+Icons,v3
