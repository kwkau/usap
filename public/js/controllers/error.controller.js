Controller.create("errorCntrl", {
    init: function () {
        var that = this;
        this.ErrorResources.fetchErrors({
            callback: function (o) {
                Error_mdl.display_mode = "append";
                Error_mdl.populate(o);
                that.ErrorResources.refreshErrors({
                    callback: function (o) {
                        Error_mdl.populate(o);
                    }
                });
            }
        });
    }
});
