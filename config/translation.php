<?php
/**
 * Multilingual configuration
 */
return [
    // Default language 默认语言
    'locale' => 'zh_CN',
    // Fallback language 回退语言，当前语言中无法找到翻译时则尝试使用回退语言中的翻译
    'fallback_locale' => ['en', 'zh_CN'],
    // Folder where language files are stored 语言文件存放的文件夹
    'path' => base_path() . '/resource/translations',
];