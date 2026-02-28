// Скрипт для извлечения точных CSS значений с Apple Newsroom
// Скопируйте этот код в консоль браузера на странице https://www.apple.com/newsroom/

const extractStyles = () => {
  const results = {};
  
  // 1. Body/page font-family
  const body = document.body;
  const bodyStyles = getComputedStyle(body);
  results.body = {
    fontFamily: bodyStyles.fontFamily,
    backgroundColor: bodyStyles.backgroundColor,
    color: bodyStyles.color
  };
  
  // 2. Navigation links (верхняя навигация)
  const navLink = document.querySelector('nav[aria-label="Global"] a');
  if (navLink) {
    const navStyles = getComputedStyle(navLink);
    results.navigationLinks = {
      fontSize: navStyles.fontSize,
      fontWeight: navStyles.fontWeight,
      letterSpacing: navStyles.letterSpacing,
      color: navStyles.color,
      fontFamily: navStyles.fontFamily
    };
  }
  
  // 3. News card titles (заголовки карточек новостей)
  const cardTitle = document.querySelector('.section-content a h3, .tile-headline, article h3, .article-headline');
  if (cardTitle) {
    const titleStyles = getComputedStyle(cardTitle);
    results.newsCardTitles = {
      fontSize: titleStyles.fontSize,
      fontWeight: titleStyles.fontWeight,
      lineHeight: titleStyles.lineHeight,
      letterSpacing: titleStyles.letterSpacing,
      color: titleStyles.color,
      fontFamily: titleStyles.fontFamily
    };
  }
  
  // 4. Category labels (PRESS RELEASE, UPDATE, etc.)
  const categoryLabel = document.querySelector('.label, .tile-label, .article-label');
  if (categoryLabel) {
    const labelStyles = getComputedStyle(categoryLabel);
    results.categoryLabels = {
      fontSize: labelStyles.fontSize,
      fontWeight: labelStyles.fontWeight,
      color: labelStyles.color,
      textTransform: labelStyles.textTransform,
      letterSpacing: labelStyles.letterSpacing,
      fontFamily: labelStyles.fontFamily
    };
  }
  
  // 5. Dates
  const dateElement = document.querySelector('.date, .tile-date, time');
  if (dateElement) {
    const dateStyles = getComputedStyle(dateElement);
    results.dates = {
      fontSize: dateStyles.fontSize,
      fontWeight: dateStyles.fontWeight,
      color: dateStyles.color,
      fontFamily: dateStyles.fontFamily
    };
  }
  
  // 6. Page background
  results.pageBackground = {
    backgroundColor: bodyStyles.backgroundColor
  };
  
  // 7. Card background
  const card = document.querySelector('.tile, article, .section-content > div');
  if (card) {
    const cardStyles = getComputedStyle(card);
    results.cardBackground = {
      backgroundColor: cardStyles.backgroundColor
    };
  }
  
  // 8. Description/excerpt text
  const description = document.querySelector('.tile-description, .article-description, p');
  if (description) {
    const descStyles = getComputedStyle(description);
    results.descriptionText = {
      fontSize: descStyles.fontSize,
      lineHeight: descStyles.lineHeight,
      color: descStyles.color,
      fontFamily: descStyles.fontFamily
    };
  }
  
  // Дополнительно: попробуем найти более специфичные селекторы
  const allLinks = document.querySelectorAll('.section-content a');
  if (allLinks.length > 0) {
    const firstLink = allLinks[0];
    const linkTitle = firstLink.querySelector('h3, .tile-headline');
    if (linkTitle) {
      const titleStyles = getComputedStyle(linkTitle);
      results.newsCardTitlesSpecific = {
        fontSize: titleStyles.fontSize,
        fontWeight: titleStyles.fontWeight,
        lineHeight: titleStyles.lineHeight,
        letterSpacing: titleStyles.letterSpacing,
        color: titleStyles.color,
        fontFamily: titleStyles.fontFamily
      };
    }
  }
  
  return results;
};

// Выполнить и вывести результаты
const cssValues = extractStyles();
console.log('=== APPLE NEWSROOM CSS VALUES ===');
console.log(JSON.stringify(cssValues, null, 2));

// Также выведем в более читаемом формате
console.log('\n=== FORMATTED OUTPUT ===');
Object.keys(cssValues).forEach(key => {
  console.log(`\n${key.toUpperCase()}:`);
  Object.keys(cssValues[key]).forEach(prop => {
    console.log(`  ${prop}: ${cssValues[key][prop]}`);
  });
});
