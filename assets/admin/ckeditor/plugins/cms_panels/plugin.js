CKEDITOR.plugins.add('cms_panels',
{
    lang: 'sk,en,cs',
    requires : ['richcombo'],
    init : function( editor )
    {
        var config = editor.config;
        var lang = editor.lang.cms_panels;

        editor.ui.addRichCombo('cms_panels',
        {
            label : lang.singluar,
            title : lang.singluar,
            voiceLabel : lang.singluar,
            multiSelect : false,

            panel :
            {
                css : [ config.contentsCss, CKEDITOR.skin.path() + 'editor.css' ],
                voiceLabel : lang.singluar
            },

            init : function()
            {
                this.startGroup(lang.plurar);
                for (var this_tag in cms.panels){
                    this.add('<div>' + cms.panels[this_tag][0] + '</div>', cms.panels[this_tag][1], cms.panels[this_tag][1]);
                }
            },

            onClick : function(value)
            {         
                editor.focus();
                editor.fire('saveSnapshot');
                editor.insertHtml(value);
                editor.fire('saveSnapshot');
            }
        });
    }
});