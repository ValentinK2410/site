<?php
/**
 * Plugin Name: DekanPro Transliterate
 * Plugin URI: https://dekan.pro/
 * Description: Автоматическая транслитерация кириллических URL (ЧПУ) в латиницу для WordPress
 * Version: 1.0.0
 * Author: DekanPro
 * Author URI: https://dekan.pro/
 * License: GPL v2 or later
 * Text Domain: dekanpro-transliterate
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для транслитерации URL
 */
class DekanPro_Transliterate {

    /**
     * Таблица транслитерации кириллицы в латиницу
     */
    private static $transliteration_table = array(
        // Русский алфавит
        'а' => 'a',   'б' => 'b',   'в' => 'v',   'г' => 'g',   'д' => 'd',
        'е' => 'e',   'ё' => 'yo',  'ж' => 'zh',  'з' => 'z',   'и' => 'i',
        'й' => 'y',   'к' => 'k',   'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',   'с' => 's',   'т' => 't',
        'у' => 'u',   'ф' => 'f',   'х' => 'h',   'ц' => 'ts',  'ч' => 'ch',
        'ш' => 'sh',  'щ' => 'sch', 'ъ' => '',    'ы' => 'y',   'ь' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        // Заглавные буквы
        'А' => 'A',   'Б' => 'B',   'В' => 'V',   'Г' => 'G',   'Д' => 'D',
        'Е' => 'E',   'Ё' => 'Yo',  'Ж' => 'Zh',  'З' => 'Z',   'И' => 'I',
        'Й' => 'Y',   'К' => 'K',   'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',   'С' => 'S',   'Т' => 'T',
        'У' => 'U',   'Ф' => 'F',   'Х' => 'H',   'Ц' => 'Ts',  'Ч' => 'Ch',
        'Ш' => 'Sh',  'Щ' => 'Sch', 'Ъ' => '',    'Ы' => 'Y',   'Ь' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        
        // Украинский алфавит (дополнительные буквы)
        'і' => 'i',   'ї' => 'yi',  'є' => 'ye',  'ґ' => 'g',
        'І' => 'I',   'Ї' => 'Yi',  'Є' => 'Ye',  'Ґ' => 'G',
    );

    /**
     * Инициализация плагина
     */
    public static function init() {
        // Фильтр для транслитерации slug при сохранении записи
        add_filter('sanitize_title', array(__CLASS__, 'transliterate'), 9);
        
        // Фильтр для транслитерации имени файла при загрузке
        add_filter('sanitize_file_name', array(__CLASS__, 'transliterate'), 9);
        
        // Добавляем страницу настроек в админку
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
    }

    /**
     * Транслитерация строки
     * 
     * @param string $title Исходная строка
     * @return string Транслитерированная строка
     */
    public static function transliterate($title) {
        // Заменяем кириллические символы на латинские
        $title = strtr($title, self::$transliteration_table);
        
        // Заменяем пробелы и специальные символы на дефисы
        $title = preg_replace('/[^a-zA-Z0-9\-_.]/', '-', $title);
        
        // Убираем множественные дефисы
        $title = preg_replace('/-+/', '-', $title);
        
        // Убираем дефисы в начале и конце
        $title = trim($title, '-');
        
        // Приводим к нижнему регистру
        $title = strtolower($title);
        
        return $title;
    }

    /**
     * Добавление страницы в меню админки
     */
    public static function add_admin_menu() {
        add_options_page(
            'DekanPro Transliterate',
            'Транслитерация URL',
            'manage_options',
            'dekanpro-transliterate',
            array(__CLASS__, 'settings_page')
        );
    }

    /**
     * Страница настроек плагина
     */
    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1>DekanPro Transliterate</h1>
            <p>Плагин автоматически транслитерирует кириллические URL в латиницу.</p>
            
            <h2>Как это работает</h2>
            <p>При создании новой записи или страницы, кириллический заголовок автоматически преобразуется в латинский URL.</p>
            
            <h3>Пример:</h3>
            <table class="widefat" style="max-width: 600px;">
                <thead>
                    <tr>
                        <th>Заголовок</th>
                        <th>URL (slug)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Как сделать липкий сайдбар</td>
                        <td><code>kak-sdelat-lipkiy-saydbar</code></td>
                    </tr>
                    <tr>
                        <td>Привет мир</td>
                        <td><code>privet-mir</code></td>
                    </tr>
                    <tr>
                        <td>Статья о WordPress</td>
                        <td><code>statya-o-wordpress</code></td>
                    </tr>
                </tbody>
            </table>
            
            <h2>Таблица транслитерации</h2>
            <p>Используется стандарт ГОСТ 7.79-2000 (ISO 9):</p>
            <p><code>а→a, б→b, в→v, г→g, д→d, е→e, ё→yo, ж→zh, з→z, и→i, й→y, к→k, л→l, м→m, н→n, о→o, п→p, р→r, с→s, т→t, у→u, ф→f, х→h, ц→ts, ч→ch, ш→sh, щ→sch, ы→y, э→e, ю→yu, я→ya</code></p>
            
            <h2>Статус</h2>
            <p style="color: green; font-weight: bold;">✓ Плагин активен и работает</p>
        </div>
        <?php
    }
}

// Запускаем плагин
DekanPro_Transliterate::init();
