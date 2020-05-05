var dom = require("ace/lib/dom");

dom.importCssString('.ace_editor.ace_editor_fullScreen{height:auto;width:auto;border:0;margin:0;position:fixed!important;top:0;bottom:0;left:0;right:0;z-index:65535}.ace_editor_fullScreen{overflow:hidden}');

require("ace/commands/default_commands").commands.push({
    name: "Toggle Fullscreen",
    bindKey: "Ctrl-F11|Command-F11",
    exec: function(editor) {
        var fullScreen = dom.toggleCssClass(document.body, "ace_editor_fullScreen");
        dom.setCssClass(editor.container, "ace_editor_fullScreen", fullScreen);
        editor.setAutoScrollEditorIntoView(!fullScreen);
        editor.resize();
    }
});