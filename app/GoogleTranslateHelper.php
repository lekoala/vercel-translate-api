<?php

namespace App;

use Exception;

/**
 * Google translate api client
 */
class GoogleTranslateHelper
{
    /**
     * Translate from google public api
     *
     * @param string $sourceText
     * @param string $targetLang
     * @param string $sourceLang
     * @return string
     */
    public static function translate($sourceText, $targetLang = null, $sourceLang = null)
    {
        if (!$sourceLang) {
            $sourceLang = 'auto';
        }
        if (!$targetLang) {
            $targetLang = 'en';
        }

        $targetLang = substr($targetLang, 0, 2);

        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl="
            . $sourceLang . "&tl=" . $targetLang . "&dt=t&q=" . urlencode($sourceText);

        $result = file_get_contents($url);
        if (!$result) {
            throw new Exception("Failed to fetch content from $url");
        }
        $data = json_decode($result, JSON_OBJECT_AS_ARRAY);
        if (!$data) {
            throw new Exception("Failed to decode json : " . json_last_error_msg());
        }

        // array:9 [▼
        // 0 => array:1 [▼
        //   0 => array:5 [▼
        //     0 => "TargetTextHere"
        //     1 => "SourceTextHere"
        //     2 => null
        //     3 => null
        //     4 => 1
        //   ]
        // ]
        // 1 => null
        // 2 => "en"
        // 3 => null
        // 4 => null
        // 5 => null
        // 6 => 1.0
        // 7 => []
        // 8 => array:4 [▼
        //   0 => array:1 [▼
        //     0 => "en"
        //   ]
        //   1 => null
        //   2 => array:1 [▼
        //     0 => 1.0
        //   ]
        //   3 => array:1 [▼
        //     0 => "en"
        //   ]
        // ]

        $translatedText = $data[0][0][0];

        // ensure case is preserved
        if (self::startsWithUpper($sourceText)) {
            $translatedText = self::mbUcFirst($translatedText);
        } else {
            $translatedText = self::mbLcFirst($translatedText);
        }

        return $translatedText;
    }

    protected static function startsWithUpper($string, $encoding = 'UTF-8')
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        return mb_strtolower($firstChar, $encoding) != $firstChar;
    }

    protected static function mbUcFirst($string, $encoding = "UTF-8")
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    protected static function mbLcFirst($string, $encoding = "UTF-8")
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtolower($firstChar, $encoding) . $then;
    }
}
