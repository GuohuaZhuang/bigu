/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'zh-cn';
	// config.uiColor = '#AADC6E';
	config.filebrowserImageUploadUrl = '/util/upload.php?type=img';
	config.filebrowserFlashUploadUrl = '/util/upload.php?type=flash';
	// code snippet
	config.extraPlugins = 'syntaxhighlight,eqneditor';
	config.syntaxhighlight_lang = 'cpp';
	config.syntaxhighlight_hideControls = true;
	
	config.font_names =
	'微软雅黑/"Microsoft YaHei", 微软雅黑, Lucida, Verdana, "Hiragino Sans GB", STHeiti, "WenQuanYi Micro Hei", "Droid Sans Fallback", SimSun, sans-serif;' + 
    CKEDITOR.config.font_names;
};

