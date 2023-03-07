<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Core\Utils\Capabilities;
use BitApps\FM\Plugin;

\defined('ABSPATH') or exit();
class PreferenceProvider
{
    public $preferences;

    private $_availableLang;

    public function __construct()
    {
        $this->preferences = Config::getOption('preferences', $this->default());
    }

    public function default()
    {
        return [
            'show_url_path'         => 'show',
            'language'              => 'en',
            'size'                  => [
                'width'  => 'auto',
                'height' => '500',
            ],
            'fm_default_view_type'  => 'icons',
            'fm_display_ui_options' => [
                'toolbar',
                'places',
                'tree',
                'path',
                'stat',
            ],
        ];
    }

    /**
     * Saves pref
     * */
    public function saveOptions()
    {
        Config::updateOption('preferences', $this->preferences, true);
    }

    /**
     * Returns all available themes
     *
     * @return array
     */
    public function themes()
    {
        $themeBase = BFM_ROOT_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'themes';
        $themeDirs = scandir($themeBase);
        $themes    = [];
        foreach ($themeDirs as $theme) {
            if ($theme === '.' || $theme === '..') {
                continue;
            }

            $variants = scandir($themeBase . DIRECTORY_SEPARATOR . $theme);
            foreach ($variants as $variant) {
                if ($variant === '.' || $variant === '..') {
                    continue;
                }

                if (
                    is_readable(
                        $themeBase . DIRECTORY_SEPARATOR . $theme
                        . DIRECTORY_SEPARATOR . $variant . DIRECTORY_SEPARATOR
                         . $variant
                         . '.json'
                    )) {
                    $themes[$variant] = BFM_ASSET_URL . "themes/{$theme}/{$variant}/{$variant}.json";
                }
            }
        }

        return $themes;
    }

    /**
     * Returns selected theme from settings
     *
     * @return array
     */
    public function getTheme()
    {
        $theme = 'default';
        if (isset($this->preferences['theme'])) {
            $theme = $this->preferences['theme'];
        }

        return $theme;
    }

    /**
     * Sets theme
     *
     * @param mixed $theme
     *
     * @return array
     */
    public function setTheme($theme)
    {
        $this->preferences['theme'] = $theme;
    }

    /**
     * Sets theme
     *
     * @param string $view
     */
    public function setUrlPathView($view)
    {
        $this->preferences['show_url_path'] = $view;
    }

    /**
     * Returns selected show_url_path from settings
     *
     * @return string
     */
    public function getUrlPathView()
    {
        $view = 'show';
        if (isset($this->preferences['show_url_path'])) {
            $view = $this->preferences['show_url_path'];
        }

        return $view;
    }

    public function getDefaultLangCode()
    {
        if (get_locale() === 'en_US') {
            $code = 'en';
        } else {
            $code = get_locale();
        }

        return $code;
    }

    public function availableLanguages()
    {
        if (!isset($this->_availableLang)) {
            $allLangs = [
                'LANG'  => __('Default', 'file-manager'),
                'aa'    => __('Afar', 'file-manager'),
                'ab'    => __('Abkhazian', 'file-manager'),
                'ae'    => __('Avestan', 'file-manager'),
                'af'    => __('Afrikaans', 'file-manager'),
                'ak'    => __('Akan', 'file-manager'),
                'am'    => __('Amharic', 'file-manager'),
                'an'    => __('Aragonese', 'file-manager'),
                'ar'    => __('Arabic', 'file-manager'),
                'as'    => __('Assamese', 'file-manager'),
                'av'    => __('Avaric', 'file-manager'),
                'ay'    => __('Aymara', 'file-manager'),
                'az'    => __('Azerbaijani', 'file-manager'),
                'ba'    => __('Bashkir', 'file-manager'),
                'be'    => __('Belarusian', 'file-manager'),
                'bg'    => __('Bulgarian', 'file-manager'),
                'bh'    => __('Bihari', 'file-manager'),
                'bi'    => __('Bislama', 'file-manager'),
                'bm'    => __('Bambara', 'file-manager'),
                'bn'    => __('Bengali', 'file-manager'),
                'bo'    => __('Tibetan', 'file-manager'),
                'br'    => __('Breton', 'file-manager'),
                'bs'    => __('Bosnian', 'file-manager'),
                'ca'    => __('Catalan', 'file-manager'),
                'ce'    => __('Chechen', 'file-manager'),
                'ch'    => __('Chamorro', 'file-manager'),
                'co'    => __('Corsican', 'file-manager'),
                'cr'    => __('Cree', 'file-manager'),
                'cs'    => __('Czech', 'file-manager'),
                'cu'    => __('Church Slavic', 'file-manager'),
                'cv'    => __('Chuvash', 'file-manager'),
                'cy'    => __('Welsh', 'file-manager'),
                'da'    => __('Danish', 'file-manager'),
                'de'    => __('German', 'file-manager'),
                'dv'    => __('Divehi', 'file-manager'),
                'dz'    => __('Dzongkha', 'file-manager'),
                'ee'    => __('Ewe', 'file-manager'),
                'el'    => __('Greek', 'file-manager'),
                'en'    => __('English', 'file-manager'),
                'eo'    => __('Esperanto', 'file-manager'),
                'es'    => __('Spanish', 'file-manager'),
                'et'    => __('Estonian', 'file-manager'),
                'eu'    => __('Basque', 'file-manager'),
                'fa'    => __('Persian', 'file-manager'),
                'ff'    => __('Fulah', 'file-manager'),
                'fi'    => __('Finnish', 'file-manager'),
                'fj'    => __('Fijian', 'file-manager'),
                'fo'    => __('Faroese', 'file-manager'),
                'fr'    => __('French', 'file-manager'),
                'fr_CA' => __('Française', 'file-manager'),
                'fy'    => __('Western Frisian', 'file-manager'),
                'ga'    => __('Irish', 'file-manager'),
                'gd'    => __('Scottish Gaelic', 'file-manager'),
                'gl'    => __('Galician', 'file-manager'),
                'gn'    => __('Guarani', 'file-manager'),
                'gu'    => __('Gujarati', 'file-manager'),
                'gv'    => __('Manx', 'file-manager'),
                'ha'    => __('Hausa', 'file-manager'),
                'he'    => __('Hebrew', 'file-manager'),
                'hi'    => __('Hindi', 'file-manager'),
                'ho'    => __('Hiri Motu', 'file-manager'),
                'hr'    => __('Croatian', 'file-manager'),
                'ht'    => __('Haitian', 'file-manager'),
                'hu'    => __('Hungarian', 'file-manager'),
                'hy'    => __('Armenian', 'file-manager'),
                'hz'    => __('Herero', 'file-manager'),
                'ia'    => __('Interlingua (International Auxiliary Language Association)', 'file-manager'),
                'id'    => __('Indonesian', 'file-manager'),
                'ie'    => __('Interlingue', 'file-manager'),
                'ig'    => __('Igbo', 'file-manager'),
                'ii'    => __('Sichuan Yi', 'file-manager'),
                'ik'    => __('Inupiaq', 'file-manager'),
                'io'    => __('Ido', 'file-manager'),
                'is'    => __('Icelandic', 'file-manager'),
                'it'    => __('Italian', 'file-manager'),
                'iu'    => __('Inuktitut', 'file-manager'),
                'ja'    => __('Japanese', 'file-manager'),
                'jp'    => __('Japanese', 'file-manager'),
                'jv'    => __('Javanese', 'file-manager'),
                'ka'    => __('Georgian', 'file-manager'),
                'kg'    => __('Kongo', 'file-manager'),
                'ki'    => __('Kikuyu', 'file-manager'),
                'kj'    => __('Kwanyama', 'file-manager'),
                'kk'    => __('Kazakh', 'file-manager'),
                'kl'    => __('Kalaallisut', 'file-manager'),
                'km'    => __('Khmer', 'file-manager'),
                'kn'    => __('Kannada', 'file-manager'),
                'ko'    => __('Korean', 'file-manager'),
                'kr'    => __('Kanuri', 'file-manager'),
                'ks'    => __('Kashmiri', 'file-manager'),
                'ku'    => __('Kurdish', 'file-manager'),
                'kv'    => __('Komi', 'file-manager'),
                'kw'    => __('Cornish', 'file-manager'),
                'ky'    => __('Kirghiz', 'file-manager'),
                'la'    => __('Latin', 'file-manager'),
                'lb'    => __('Luxembourgish', 'file-manager'),
                'lg'    => __('Ganda', 'file-manager'),
                'li'    => __('Limburgish', 'file-manager'),
                'ln'    => __('Lingala', 'file-manager'),
                'lo'    => __('Lao', 'file-manager'),
                'lt'    => __('Lithuanian', 'file-manager'),
                'lu'    => __('Luba-Katanga', 'file-manager'),
                'lv'    => __('Latvian', 'file-manager'),
                'mg'    => __('Malagasy', 'file-manager'),
                'mh'    => __('Marshallese', 'file-manager'),
                'mi'    => __('Maori', 'file-manager'),
                'mk'    => __('Macedonian', 'file-manager'),
                'ml'    => __('Malayalam', 'file-manager'),
                'mn'    => __('Mongolian', 'file-manager'),
                'mr'    => __('Marathi', 'file-manager'),
                'ms'    => __('Malay', 'file-manager'),
                'mt'    => __('Maltese', 'file-manager'),
                'my'    => __('Burmese', 'file-manager'),
                'na'    => __('Nauru', 'file-manager'),
                'nb'    => __('Norwegian Bokmal', 'file-manager'),
                'nd'    => __('North Ndebele', 'file-manager'),
                'ne'    => __('Nepali', 'file-manager'),
                'ng'    => __('Ndonga', 'file-manager'),
                'nl'    => __('Dutch', 'file-manager'),
                'nn'    => __('Norwegian Nynorsk', 'file-manager'),
                'no'    => __('Norwegian', 'file-manager'),
                'nr'    => __('South Ndebele', 'file-manager'),
                'nv'    => __('Navajo', 'file-manager'),
                'ny'    => __('Chichewa', 'file-manager'),
                'oc'    => __('Occitan', 'file-manager'),
                'oj'    => __('Ojibwa', 'file-manager'),
                'om'    => __('Oromo', 'file-manager'),
                'or'    => __('Oriya', 'file-manager'),
                'os'    => __('Ossetian', 'file-manager'),
                'pa'    => __('Panjabi', 'file-manager'),
                'pi'    => __('Pali', 'file-manager'),
                'pl'    => __('Polish', 'file-manager'),
                'ps'    => __('Pashto', 'file-manager'),
                'pt'    => __('Portuguese', 'file-manager'),
                'pt_BR' => __('Portuguese(Brazil)', 'file-manager'),
                'qu'    => __('Quechua', 'file-manager'),
                'rm'    => __('Raeto-Romance', 'file-manager'),
                'rn'    => __('Kirundi', 'file-manager'),
                'ro'    => __('Romanian', 'file-manager'),
                'ru'    => __('Russian', 'file-manager'),
                'rw'    => __('Kinyarwanda', 'file-manager'),
                'sa'    => __('Sanskrit', 'file-manager'),
                'sc'    => __('Sardinian', 'file-manager'),
                'sd'    => __('Sindhi', 'file-manager'),
                'se'    => __('Northern Sami', 'file-manager'),
                'sg'    => __('Sango', 'file-manager'),
                'si'    => __('Sinhala', 'file-manager'),
                'sk'    => __('Slovak', 'file-manager'),
                'sl'    => __('Slovenian', 'file-manager'),
                'sm'    => __('Samoan', 'file-manager'),
                'sn'    => __('Shona', 'file-manager'),
                'so'    => __('Somali', 'file-manager'),
                'sq'    => __('Albanian', 'file-manager'),
                'sr'    => __('Serbian', 'file-manager'),
                'ss'    => __('Swati', 'file-manager'),
                'st'    => __('Southern Sotho', 'file-manager'),
                'su'    => __('Sundanese', 'file-manager'),
                'sv'    => __('Swedish', 'file-manager'),
                'sw'    => __('Swahili', 'file-manager'),
                'ta'    => __('Tamil', 'file-manager'),
                'te'    => __('Telugu', 'file-manager'),
                'tg'    => __('Tajik', 'file-manager'),
                'th'    => __('Thai', 'file-manager'),
                'ti'    => __('Tigrinya', 'file-manager'),
                'tk'    => __('Turkmen', 'file-manager'),
                'tl'    => __('Tagalog', 'file-manager'),
                'tn'    => __('Tswana', 'file-manager'),
                'to'    => __('Tonga', 'file-manager'),
                'tr'    => __('Turkish', 'file-manager'),
                'ts'    => __('Tsonga', 'file-manager'),
                'tt'    => __('Tatar', 'file-manager'),
                'tw'    => __('Twi', 'file-manager'),
                'ty'    => __('Tahitian', 'file-manager'),
                'ug'    => __('Uighur', 'file-manager'),
                'ug_CN' => __('Uighur(China)', 'file-manager'),
                'uk'    => __('Ukrainian', 'file-manager'),
                'ur'    => __('Urdu', 'file-manager'),
                'uz'    => __('Uzbek', 'file-manager'),
                've'    => __('Venda', 'file-manager'),
                'vi'    => __('Vietnamese', 'file-manager'),
                'vo'    => __('Volapuk', 'file-manager'),
                'wa'    => __('Walloon', 'file-manager'),
                'wo'    => __('Wolof', 'file-manager'),
                'xh'    => __('Xhosa', 'file-manager'),
                'yi'    => __('Yiddish', 'file-manager'),
                'yo'    => __('Yoruba', 'file-manager'),
                'za'    => __('Zhuang', 'file-manager'),
                'zh'    => __('Chinese', 'file-manager'),
                'zh_CN' => __('Chinese(China)', 'file-manager'),
                'zh_TW' => __('Chinese(Taiwan)', 'file-manager'),
                'zu'    => __('Zulu', 'file-manager'),
            ];

            $dirs = scandir(BFM_FINDER_DIR . 'js/i18n');
            foreach ($dirs as $lang) {
                if (\in_array($lang, ['.', '..', 'help'])) {
                    continue;
                }

                $lang = str_replace(['elfinder.', '.js'], '', $lang);

                if (isset($allLangs[$lang])) {
                    $this->_availableLang[$lang] = $allLangs[$lang];
                }
            }
        }

        return $this->_availableLang;
    }

    public function getLangCode()
    {
        return
        isset($this->preferences['language'])
         && isset($this->availableLanguages()[(string) $this->preferences['language']])
        ? $this->preferences['language']
        : $this->getDefaultLangCode();
    }

    public function getLangUrl()
    {
        $langUrl = BFM_FINDER_URL . 'js/i18n/elfinder.' . $this->getLangCode() . '.js';

        if (file_exists($langUrl)) {
            $langUrl = BFM_FINDER_URL . 'js/i18n/elfinder.en.js';
        }

        return $langUrl;
    }

    public function setLang($lang)
    {
        $this->preferences['language']
            = isset($this->availableLanguages()[$lang])
        ? $lang
        : $this->getDefaultLangCode();
    }

    public function setRootPath($path)
    {
        $this->preferences['root_folder_path'] = $this->realPath($path);
    }

    public function getRootPath()
    {
        $defaultPath = Capabilities::check('manage_options')
         ? ABSPATH : Plugin::instance()->permissions()->getDefaultPublicRootPath();

        return isset($this->preferences['root_folder_path'])
        ? $this->preferences['root_folder_path'] : $defaultPath;
    }

    public function realPath($path)
    {
        if (\is_null($path)) {
            return $path;
        }

        // whether $path is unix or not
        $unipath = \strlen($path) == 0 || $path[0] != '/';
        // attempts to detect if path is relative in which case, add cwd
        if (strpos($path, ':') === false && $unipath) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }

        // resolve path parts (single dot, double dot and double delimiters)
        $path      = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts     = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ($part == '.') {
                continue;
            }

            if ($part == '..') {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        $path = implode(DIRECTORY_SEPARATOR, $absolutes);
        // resolve any symlinks
        if (file_exists($path) && linkinfo($path) > 0) {
            $path = readlink($path);
        }

        // put initial separator that could have been lost
        return !$unipath ? '/' . $path : $path;
    }

    public function setRootUrl($url)
    {
        $this->preferences['root_folder_url'] = $url;
    }

    public function getRootUrl()
    {
        $defaultUrl = Capabilities::check('manage_options')
         ? Config::get('SITE_URL') : Plugin::instance()->permissions()->getDefaultPublicRootURL();

        return isset($this->preferences['root_folder_url'])
        ? $this->preferences['root_folder_url'] : $defaultUrl;
    }

    public function setRootVolumeName($name)
    {
        $this->preferences['fm_root_folder_name'] = $name;
    }

    public function getRootVolumeName()
    {
        return isset($this->preferences['fm_root_folder_name'])
        ? $this->preferences['fm_root_folder_name'] : basename(ABSPATH);
    }

    public function setWidth($width)
    {
        $this->preferences['size']['width'] = $width;
    }

    public function getWidth()
    {
        return isset($this->preferences['size']['width'])
        ? $this->preferences['size']['width'] : 'auto';
    }

    public function setHeight($height)
    {
        $this->preferences['size']['height'] = $height;
    }

    public function getHeight()
    {
        return isset($this->preferences['size']['height'])
        ? $this->preferences['size']['height'] : '500';
    }

    public function setVisibilityOfHiddenFile($visibility)
    {
        $this->preferences['fm-show-hidden-files'] = $visibility;
    }

    public function getVisibilityOfHiddenFile()
    {
        return \array_key_exists('fm-show-hidden-files', $this->preferences)
         && $this->preferences['fm-show-hidden-files'] ? true : false;
    }

    public function setPermissionForHiddenFolderCreation($permission)
    {
        $this->preferences['fm-create-hidden-files-folders'] = $permission;
    }

    public function isHiddenFolderAllowed()
    {
        return \array_key_exists('fm-create-hidden-files-folders', $this->preferences)
         && $this->preferences['fm-create-hidden-files-folders'] ? true : false;
    }

    public function setPermissionForTrashCreation($permission)
    {
        $this->preferences['fm-create-trash-files-folders'] = $permission;
    }

    public function isTrashAllowed()
    {
        return \array_key_exists('fm-create-trash-files-folders', $this->preferences)
         && $this->preferences['fm-create-trash-files-folders'] ? true : false;
    }

    public function setViewType($type)
    {
        $this->preferences['fm_default_view_type'] = $type;
    }

    public function getViewType()
    {
        return isset($this->preferences['fm_default_view_type']) ? $this->preferences['fm_default_view_type'] : 'icons';
    }

    public function setRememberLastDir($remember)
    {
        $this->preferences['fm-remember-last-dir'] = $remember;
    }

    public function getRememberLastDir()
    {
        return \array_key_exists('fm-remember-last-dir', $this->preferences)
        && $this->preferences['fm-remember-last-dir']
         ? $this->preferences['fm-remember-last-dir'] : false;
    }

    public function setClearHistoryOnReload($clearHistory)
    {
        $this->preferences['fm-clear-history-on-reload'] = $clearHistory;
    }

    public function getClearHistoryOnReload()
    {
        return \array_key_exists('fm-clear-history-on-reload', $this->preferences)
        && $this->preferences['fm-clear-history-on-reload']
         ? $this->preferences['fm-clear-history-on-reload'] : false;
    }

    public function setUiOptions($options)
    {
        $this->preferences['fm_display_ui_options'] = $options;
    }

    public function getUiOptions()
    {
        return isset($this->preferences['fm_display_ui_options'])
         ? $this->preferences['fm_display_ui_options'] : ['toolbar', 'places', 'tree', 'path', 'stat'];
    }
}
