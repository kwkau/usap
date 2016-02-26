
Controller.create("usap.dashboardCntrl",{
    elements:{search_btn: "#srch", del_btn:"#user-results"},
    events:{
        click:[
            {target:"search_btn", handler:"admin_search_handler"},
            {target:"del_btn", selector:".del-btn", handler:"user_del", type:"delegate"},
            {target:"del_btn", selector:".res-btn", handler:"user_restore", type:"delegate"}
        ]
    },
    shared:general,
    init: function(){
        /*---------------------------------
         * fetch and display flagged posts
         *-------------------------------*/
        this.DashboardResource.fetchFlaggedPost({
            callback: function (o) {
                Post_mdl.display_mode = "apply";
                Post_mdl.populate(o);
            }
        });


        /*----------------------------------
         * fetch and display flagged forums
         *--------------------------------*/
        this.DashboardResource.fetchFlaggedForum({
            callback: function (o) {
                Forum_mdl.display_mode = "apply";
                Forum_mdl.populate(o);
            }
        });
    },
    admin_search_handler: function (e) {
        var that = e.data.that,ele = this;
        /*-------------------------------------
         * send the search query to the server
         *-----------------------------------*/
        var q_bx = $(this).parents(".panel-title").find("input");
        if(!that.isEmpty(q_bx)){
            that.DashboardResource.search({
                data: {query:q_bx.val()},
                callback: function (o) {
                    var data = JSON.parse(o);
                    SearchUser_mdl.display_mode = "apply";
                    SearchUser_mdl.populate(data.users);
                }
            });
        }

    },
    user_del: function (e) {
        var that = e.data.that,ele =$(this),
        id = ele.parents(".list-group-item").data('id');
        that.DashboardResource.deleteUser({
            data:{id:id},
            callback: function (o) {
                if(o == 1){
                    ele.replaceWith(function (i, text) {
                        return $("<button>", {
                            type: "button",//change the type
                            class: "res-btn btn btn-success pull-right"
                        }).text("Restore");
                    });
                }
            }
        });
    },
    user_restore: function (e) {
        var that = e.data.that,ele =$(this),
        id = ele.parents(".list-group-item").data('id');
        that.DashboardResource.restoreUser({
            data:{id:id},
            callback: function (o) {
                if(o == 1){
                    ele.replaceWith(function (i, text) {
                        return $("<button>", {
                            type: "button",//change the type
                            class: "del-btn btn btn-danger pull-right"
                        }).text("Black List");
                    });
                }
            }
        });
    }
});