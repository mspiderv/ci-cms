/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';

    config.language = cms.admin_lang;
    config.skin = cms.ckeditor_skin;;
    config.width = 960;
    config.extraPlugins = 'cms_panels,cms_positions'; // devtools
    config.filebrowserBrowseUrl = cms.base_url + 'admin/file_manager';
    config.entities = false;
    config.autoGrow_maxHeight = 600;

    //config.entities_greek = false;
    //config.entities_latin = false;
    //config.htmlEncodeOutput = false;

    /*config.contentsCss = cms.assets + 'themes/default/css/content.css';
    config.bodyClass = 'content';

    config.stylesSet = [
        { name: 'Caption', element: 'p', attributes: { 'class': 'dox_caption'} },
        { name: 'NoticeMe', element: 'span', styles: { 'background-color': 'Yellow'} }
    ];*/

    config.toolbarGroups =
    [
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'insert' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
        { name: 'links' },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'tools' },
        { name: 'cms', items : [ 'cms_panels', 'cms_positions' ] }
    ];	
	
};

CKEDITOR.dtd.$removeEmpty.a = 0;
CKEDITOR.dtd.$removeEmpty.i = 0;
CKEDITOR.dtd.$removeEmpty.span = 0;
