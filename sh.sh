#!/bin/bash
# Скрипт для копирования файлов с удалённого сервера dekan.pro
# Проект: /Users/valentink2410/PhpstormProjects/site/
#
# ВАЖНО: Замените USER на вашего SSH-пользователя (root, или логин хостинга)
# Если хостинг использует другой хост — замените dekan.pro на IP или хост
#
# Примеры пользователей для разных хостингов:
# - VPS/dedicated: root
# - Reg.ru, Beget, Timeweb: обычно ваш логин панели или uXXXXXXX
# - Проверьте данные в панели хостинга (SSH-доступ)

USER="root"   # <-- ИЗМЕНИТЕ на ваш SSH-логин
HOST="dekan.pro"
REMOTE_PATH="/var/www/www-root/data/www/dekan.pro"
LOCAL_PATH="$(cd "$(dirname "$0")" && pwd)"

echo "Копирование с ${USER}@${HOST}:${REMOTE_PATH}"
echo "В локальную папку: ${LOCAL_PATH}"
echo ""

rsync -avz --progress \
  --exclude '.git' \
  --exclude 'node_modules' \
  --exclude '.env' \
  "${USER}@${HOST}:${REMOTE_PATH}/" "${LOCAL_PATH}/"

if [ $? -eq 0 ]; then
  echo ""
  echo "✓ Копирование завершено. Добавляем файлы в Git..."
  cd "$LOCAL_PATH"
  git add -A
  git status
else
  echo ""
  echo "✗ Ошибка. Проверьте:"
  echo "  1. SSH-ключ или пароль"
  echo "  2. Имя пользователя (USER)"
  echo "  3. Хост (HOST)"
  echo "  4. Доступность сервера: ssh ${USER}@${HOST}"
fi
