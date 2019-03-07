function tinyplugin() {
    return "[optinlocker]";
}

(function() {
    tinymce.create('tinymce.plugins.optinlocker', {
        init : function(ed, url){
            ed.addButton('optinlocker', {
                title : 'Wrap content in Opt-In Content Locker container',
                onclick : function() {
					ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
					tinyMCE.activeEditor.selection.setContent('[optinlocker]' + ilc_sel_content + '[/optinlocker]')
                },
                image: url + "/../images/locker.png"
            });
        },
        getInfo : function() {
            return {
                longname : 'Opt-In Content Locker',
                author : 'Ivan Churakov',
                authorurl : 'http://codecanyon.net/user/ichurakov?ref=ichurakov',
                infourl : 'http://halfdata.com/milkyway/subscribe-unlock.html',
                version : '2.00'
            };
        }
    });
    tinymce.PluginManager.add('optinlocker', tinymce.plugins.optinlocker);
    
})();