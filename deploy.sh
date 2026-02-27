#!/bin/bash
# Деплой файлов из Git на сервер dekan.pro
# Копирует локальные файлы (из репозитория) на удалённый сервер
#
# Использование: ./deploy.sh

USER="root"
HOST="82.146.39.18"
REMOTE_PATH="/var/www/www-root/data/www/dekan.pro"
LOCAL_PATH="$(cd "$(dirname "$0")" && pwd)"

echo "Деплой на ${USER}@${HOST}:${REMOTE_PATH}"
echo "Источник: ${LOCAL_PATH}"
echo ""

rsync -avz --progress \
  -e "ssh -i ~/.ssh/dekan_key" \
  --exclude '.git' \
  --exclude 'node_modules' \
  --exclude '.env' \
  --exclude 'wp-content/uploads' \
  "${LOCAL_PATH}/" "${USER}@${HOST}:${REMOTE_PATH}/"

if [ $? -eq 0 ]; then
  echo ""
  echo "Восстановление прав на uploads (www-root — пользователь PHP-FPM)..."
  ssh -i ~/.ssh/dekan_key ${USER}@${HOST} "chown -R www-root:www-root ${REMOTE_PATH}/wp-content/uploads 2>/dev/null || true"
  echo ""
  echo "Запуск скриптов создания статей и терминов глоссария..."
  ssh -i ~/.ssh/dekan_key ${USER}@${HOST} "cd ${REMOTE_PATH} && php create-post-analytics-article.php 2>/dev/null || true"
  ssh -i ~/.ssh/dekan_key ${USER}@${HOST} "cd ${REMOTE_PATH} && php create-post-analytics-article-part2.php 2>/dev/null || true"
  echo "Создание меню (Живопись, Поэзия, Статьи)..."
  ssh -i ~/.ssh/dekan_key ${USER}@${HOST} "cd ${REMOTE_PATH} && php create-menu-items.php 2>/dev/null || true"
  echo ""
  echo "✓ Деплой завершён. Файлы с Git синхронизированы на сервер."
else
  echo ""
  echo "✗ Ошибка. Проверьте:"
  echo "  1. SSH-ключ или пароль"
  echo "  2. Имя пользователя (USER)"
  echo "  3. Хост (HOST)"
  echo "  4. Доступность сервера: ssh ${USER}@${HOST}"
  exit 1
fi
