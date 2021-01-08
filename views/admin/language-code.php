<?php
/**
 *
 * @file language-code.php
 * @description Codes for manipulation language
 *
 * */

// Security check
if(!defined('ABSPATH')) die();

/**
 *
 * @class FMLanguage
 * @description Works with language
 *
 * */
class FMLanguage{

	/**
	 *
	 * @var array $languages
	 * @description Keeps all the language and it's full name
	 *
	 * */
	public $languages;

	/**
	 *
	 * @function __construct
	 * @description Main constructor function
	 *
	 * */
	public function __construct($lc){
		$this->languages = $lc;
	}

	public function find_name($search_code){

		foreach($this->languages as $code => $lang){
			if($search_code == $code) return $lang;
		}

	}

	public function available_languages(){

		global $FileManager;

		$elfinder_files = scandir( plugin_dir_path( __FILE__ ) . ".." . DS . ".." . DS . "elFinder" . DS . "js" . DS . "i18n" );
		for($I = 2, $lang = array(); $I < count($elfinder_files); $I++){

			$file_name = $elfinder_files[$I];
			if( $file_name == 'elfinder.fallback.js' || $file_name == 'help') continue;
			$code = explode('.', $file_name); $code = $code[1];
			$name = $this->find_name($code);

			$lang[] = array(
				'code' => $code,
				'name' => $name,
				'file-url' => $FileManager->url('elFinder/js/i18n/') . $file_name
			);
		}

		return $lang;

	}

}

global $fm_languages;
$fm_languages = new FMLanguage(
array(
 "LANG" => __("Default", 'file-manager'),
 "aa" => __("Afar", 'file-manager'),
 "ab" => __("Abkhazian", 'file-manager'),
 "ae" => __("Avestan", 'file-manager'),
 "af" => __("Afrikaans", 'file-manager'),
 "ak" => __("Akan", 'file-manager'),
 "am" => __("Amharic", 'file-manager'),
 "an" => __("Aragonese", 'file-manager'),
 "ar" => __("Arabic", 'file-manager'),
 "as" => __("Assamese", 'file-manager'),
 "av" => __("Avaric", 'file-manager'),
 "ay" => __("Aymara", 'file-manager'),
 "az" => __("Azerbaijani", 'file-manager'),
 "ba" => __("Bashkir", 'file-manager'),
 "be" => __("Belarusian", 'file-manager'),
 "bg" => __("Bulgarian", 'file-manager'),
 "bh" => __("Bihari", 'file-manager'),
 "bi" => __("Bislama", 'file-manager'),
 "bm" => __("Bambara", 'file-manager'),
 "bn" => __("Bengali", 'file-manager'),
 "bo" => __("Tibetan", 'file-manager'),
 "br" => __("Breton", 'file-manager'),
 "bs" => __("Bosnian", 'file-manager'),
 "ca" => __("Catalan", 'file-manager'),
 "ce" => __("Chechen", 'file-manager'),
 "ch" => __("Chamorro", 'file-manager'),
 "co" => __("Corsican", 'file-manager'),
 "cr" => __("Cree", 'file-manager'),
 "cs" => __("Czech", 'file-manager'),
 "cu" => __("Church Slavic", 'file-manager'),
 "cv" => __("Chuvash", 'file-manager'),
 "cy" => __("Welsh", 'file-manager'),
 "da" => __("Danish", 'file-manager'),
 "de" => __("German", 'file-manager'),
 "dv" => __("Divehi", 'file-manager'),
 "dz" => __("Dzongkha", 'file-manager'),
 "ee" => __("Ewe", 'file-manager'),
 "el" => __("Greek", 'file-manager'),
 "en" => __("English", 'file-manager'),
 "eo" => __("Esperanto", 'file-manager'),
 "es" => __("Spanish", 'file-manager'),
 "et" => __("Estonian", 'file-manager'),
 "eu" => __("Basque", 'file-manager'),
 "fa" => __("Persian", 'file-manager'),
 "ff" => __("Fulah", 'file-manager'),
 "fi" => __("Finnish", 'file-manager'),
 "fj" => __("Fijian", 'file-manager'),
 "fo" => __("Faroese", 'file-manager'),
 "fr" => __("French", 'file-manager'),
 "fy" => __("Western Frisian", 'file-manager'),
 "ga" => __("Irish", 'file-manager'),
 "gd" => __("Scottish Gaelic", 'file-manager'),
 "gl" => __("Galician", 'file-manager'),
 "gn" => __("Guarani", 'file-manager'),
 "gu" => __("Gujarati", 'file-manager'),
 "gv" => __("Manx", 'file-manager'),
 "ha" => __("Hausa", 'file-manager'),
 "he" => __("Hebrew", 'file-manager'),
 "hi" => __("Hindi", 'file-manager'),
 "ho" => __("Hiri Motu", 'file-manager'),
 "hr" => __("Croatian", 'file-manager'),
 "ht" => __("Haitian", 'file-manager'),
 "hu" => __("Hungarian", 'file-manager'),
 "hy" => __("Armenian", 'file-manager'),
 "hz" => __("Herero", 'file-manager'),
 "ia" => __("Interlingua (International Auxiliary Language Association)", 'file-manager'),
 "id" => __("Indonesian", 'file-manager'),
 "ie" => __("Interlingue", 'file-manager'),
 "ig" => __("Igbo", 'file-manager'),
 "ii" => __("Sichuan Yi", 'file-manager'),
 "ik" => __("Inupiaq", 'file-manager'),
 "io" => __("Ido", 'file-manager'),
 "is" => __("Icelandic", 'file-manager'),
 "it" => __("Italian", 'file-manager'),
 "iu" => __("Inuktitut", 'file-manager'),
 "ja" => __("Japanese", 'file-manager'),
 "jp" => __("Japanese", 'file-manager'),
 "jv" => __("Javanese", 'file-manager'),
 "ka" => __("Georgian", 'file-manager'),
 "kg" => __("Kongo", 'file-manager'),
 "ki" => __("Kikuyu", 'file-manager'),
 "kj" => __("Kwanyama", 'file-manager'),
 "kk" => __("Kazakh", 'file-manager'),
 "kl" => __("Kalaallisut", 'file-manager'),
 "km" => __("Khmer", 'file-manager'),
 "kn" => __("Kannada", 'file-manager'),
 "ko" => __("Korean", 'file-manager'),
 "kr" => __("Kanuri", 'file-manager'),
 "ks" => __("Kashmiri", 'file-manager'),
 "ku" => __("Kurdish", 'file-manager'),
 "kv" => __("Komi", 'file-manager'),
 "kw" => __("Cornish", 'file-manager'),
 "ky" => __("Kirghiz", 'file-manager'),
 "la" => __("Latin", 'file-manager'),
 "lb" => __("Luxembourgish", 'file-manager'),
 "lg" => __("Ganda", 'file-manager'),
 "li" => __("Limburgish", 'file-manager'),
 "ln" => __("Lingala", 'file-manager'),
 "lo" => __("Lao", 'file-manager'),
 "lt" => __("Lithuanian", 'file-manager'),
 "lu" => __("Luba-Katanga", 'file-manager'),
 "lv" => __("Latvian", 'file-manager'),
 "mg" => __("Malagasy", 'file-manager'),
 "mh" => __("Marshallese", 'file-manager'),
 "mi" => __("Maori", 'file-manager'),
 "mk" => __("Macedonian", 'file-manager'),
 "ml" => __("Malayalam", 'file-manager'),
 "mn" => __("Mongolian", 'file-manager'),
 "mr" => __("Marathi", 'file-manager'),
 "ms" => __("Malay", 'file-manager'),
 "mt" => __("Maltese", 'file-manager'),
 "my" => __("Burmese", 'file-manager'),
 "na" => __("Nauru", 'file-manager'),
 "nb" => __("Norwegian Bokmal", 'file-manager'),
 "nd" => __("North Ndebele", 'file-manager'),
 "ne" => __("Nepali", 'file-manager'),
 "ng" => __("Ndonga", 'file-manager'),
 "nl" => __("Dutch", 'file-manager'),
 "nn" => __("Norwegian Nynorsk", 'file-manager'),
 "no" => __("Norwegian", 'file-manager'),
 "nr" => __("South Ndebele", 'file-manager'),
 "nv" => __("Navajo", 'file-manager'),
 "ny" => __("Chichewa", 'file-manager'),
 "oc" => __("Occitan", 'file-manager'),
 "oj" => __("Ojibwa", 'file-manager'),
 "om" => __("Oromo", 'file-manager'),
 "or" => __("Oriya", 'file-manager'),
 "os" => __("Ossetian", 'file-manager'),
 "pa" => __("Panjabi", 'file-manager'),
 "pi" => __("Pali", 'file-manager'),
 "pl" => __("Polish", 'file-manager'),
 "ps" => __("Pashto", 'file-manager'),
 "pt" => __("Portuguese", 'file-manager'),
 "pt_BR" => __("Portuguese(Brazil)", 'file-manager'),
 "qu" => __("Quechua", 'file-manager'),
 "rm" => __("Raeto-Romance", 'file-manager'),
 "rn" => __("Kirundi", 'file-manager'),
 "ro" => __("Romanian", 'file-manager'),
 "ru" => __("Russian", 'file-manager'),
 "rw" => __("Kinyarwanda", 'file-manager'),
 "sa" => __("Sanskrit", 'file-manager'),
 "sc" => __("Sardinian", 'file-manager'),
 "sd" => __("Sindhi", 'file-manager'),
 "se" => __("Northern Sami", 'file-manager'),
 "sg" => __("Sango", 'file-manager'),
 "si" => __("Sinhala", 'file-manager'),
 "sk" => __("Slovak", 'file-manager'),
 "sl" => __("Slovenian", 'file-manager'),
 "sm" => __("Samoan", 'file-manager'),
 "sn" => __("Shona", 'file-manager'),
 "so" => __("Somali", 'file-manager'),
 "sq" => __("Albanian", 'file-manager'),
 "sr" => __("Serbian", 'file-manager'),
 "ss" => __("Swati", 'file-manager'),
 "st" => __("Southern Sotho", 'file-manager'),
 "su" => __("Sundanese", 'file-manager'),
 "sv" => __("Swedish", 'file-manager'),
 "sw" => __("Swahili", 'file-manager'),
 "ta" => __("Tamil", 'file-manager'),
 "te" => __("Telugu", 'file-manager'),
 "tg" => __("Tajik", 'file-manager'),
 "th" => __("Thai", 'file-manager'),
 "ti" => __("Tigrinya", 'file-manager'),
 "tk" => __("Turkmen", 'file-manager'),
 "tl" => __("Tagalog", 'file-manager'),
 "tn" => __("Tswana", 'file-manager'),
 "to" => __("Tonga", 'file-manager'),
 "tr" => __("Turkish", 'file-manager'),
 "ts" => __("Tsonga", 'file-manager'),
 "tt" => __("Tatar", 'file-manager'),
 "tw" => __("Twi", 'file-manager'),
 "ty" => __("Tahitian", 'file-manager'),
 "ug" => __("Uighur", 'file-manager'),
 "ug_CN" => __("Uighur(China)", 'file-manager'),
 "uk" => __("Ukrainian", 'file-manager'),
 "ur" => __("Urdu", 'file-manager'),
 "uz" => __("Uzbek", 'file-manager'),
 "ve" => __("Venda", 'file-manager'),
 "vi" => __("Vietnamese", 'file-manager'),
 "vo" => __("Volapuk", 'file-manager'),
 "wa" => __("Walloon", 'file-manager'),
 "wo" => __("Wolof", 'file-manager'),
 "xh" => __("Xhosa", 'file-manager'),
 "yi" => __("Yiddish", 'file-manager'),
 "yo" => __("Yoruba", 'file-manager'),
 "za" => __("Zhuang", 'file-manager'),
 "zh" => __("Chinese", 'file-manager'),
 "zh_CN" => __("Chinese(China)", 'file-manager'),
 "zh_TW" => __("Chinese(Taiwan)", 'file-manager'),
 "zu" => __("Zulu", 'file-manager'), 'file-manager')
);
