var PlayerRefferals = {

    msg_success_copy: "",

    pcCopyToclipboard: function (id) {
        this.copyToClipboard(id);
        MessageBox.success(this.msg_success_copy);
    },
    mobileCopyToClipboard: function (id) {
        this.copyToClipboard(id);
        $("#suc").show().delay(500).fadeOut();
    },
    copyToClipboard: function (id) {
        var copyElement = document.getElementById(id);
        var range = document.createRange();
        range.selectNode(copyElement);
        window.getSelection().removeAllRanges(); 
        window.getSelection().addRange(range);
        document.execCommand('copy');
    }
}