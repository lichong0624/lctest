var Q    = Q || {};
Q.plugin = Q.plugin || {};

Q.plugin.table = function () {
    var self = this;

    self.reload = function (option) {
        var options = {
            insertCon   : ""
            , url       : ""
            , dataType  : 'json'
            , pushState : false
        };
        $.extend(options, option);

        new Q.plugin.page().asyncLoadPage({
            insertCon   : options.insertCon
            , url       : options.url
            , dataType  : 'json'
            , pushState : false
        });
    }
};