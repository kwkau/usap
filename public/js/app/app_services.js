//services
var host = 'localhost';
Controller.service("SharedResource", function (resource) {
    return resource("http://"+host+"/usap/dropzone/:operation",
        {operation: "initialise"},
        {
            flag: {
                method: "POST",
                params: {
                    operation: "flag"
                }
            },
            setBookmark: {
                method: "POST",
                params: {
                    operation: "set_bookmark"
                }
            },
            search: {
                method: "POST",
                params: {
                    operation: "search"
                }
            },
            fetchUploads: {
                method: "POST",
                params: {
                    operation: "fetch_upload"
                }
            },
            startUpSocket:{
                type: "websocket",
                params: {
                    url: "ws://"+host+":1290/usap",
                    port: 1290,
                    type: "startUpSocket"
                }
            }
        }
    )
}).service("DropZoneResource", function(resource){
    return resource("http://"+host+"/usap/dropzone/:operation",
        {operation: "initialise"},
        {
            fetchForum: {
                method: "POST",
                params: {
                    operation: "get_forums"
                }
            },
            loadUser: {
                method: "POST",
                params: {
                    operation: "get_data"
                }
            },
            fetchPost: {
                method: "POST",
                params: {
                    operation: "get_posts"
                }
            },
            fetchGroups: {
                method: "POST",
                params: {
                    operation: "load_groups"
                }
            },
            fetchUploads: {
                method: "POST",
                params: {
                    operation: "get_uploads"
                }
            },
            fetchFriends: {
                method: "POST",
                params: {
                    operation: "load_friends"
                }
            },
            fetchBookmarks: {
                method: "POST",
                params: {
                    operation: "load_bookmarks"
                }
            },
            forumSocket:{
                type: "websocket",
                params: {
                    url: "ws://"+host+":1250/usap",
                    port: 1250,
                    type: "forumSocket"
                }
            },
            postSocket:{
                type: "websocket",
                params: {
                    url: "ws://"+host+":1260/usap",
                    port: 1260,
                    type: "postSocket"
                }
            },
            notiSocket:{
                type: "websocket",
                params: {
                    url: "ws://"+host+":1270/usap",
                    port: 1270,
                    type: "notiSocket"
                }
            },
            profileSocket:{
                type: "websocket",
                params: {
                    url: "ws://"+host+":1295/usap",
                    port: 1295,
                    type: "profileSocket"
                }
            }
        }
    )
}).service("GroupResource", function (resource) {
    return resource("http://"+host+"/usap/group/:operation",
    {operation:"initialise"},
    {
        loadGroup: {
            method: "POST",
                params: {
                operation: "get_grp_data"
            }
        },
        groupJoin: {
            method: "POST",
                params: {
                operation: "grp_mem_join"
            }
        },
        groupForumSocket:{
            type: "websocket",
            params: {
                url: "ws://"+host+":1300/usap",
                port: 1300,
                type: "groupForumSocket"
            }
        },
        groupPostSocket:{
        type: "websocket",
            params: {
                url: "ws://"+host+":1310/usap",
                port: 1310,
                type: "groupPostSocket"
        }
    }
    });
}).service("DepartmentResource", function (resource) {
    return resource("http://"+host+"/usap/department/:operation",
        {operation:"initialise"},
        {
            loadDepartment: {
                method: "post",
                params:{
                    operation:"get_dep_data"
                }
            },
            departmentForumSocket:{
                type:"websocket",
                params:{
                    url:"ws://"+host+":1320/usap",
                    port:1320,
                    type:"depForumSocket"
                }
            },
            departmentPostSocket:{
                type:"websocket",
                params:{
                    url:"ws://"+host+":1330/usap",
                    port:1330,
                    type:"depPostSocket"
                }
            }
        }
    )
}).service("DashboardResource", function (resource) {
    return resource("http://"+host+"/usap/dashboard/:operation",
        {operation:"initialise"},
        {
            deleteUser:{
                method:"POST",
                params:{
                    operation:"del_user"
                }
            },
            restoreUser:{
                method:"POST",
                params:{
                    operation:"res_user"
                }
            },
            search: {
                method: "POST",
                params: {
                    operation: "search"
                }
            },
            fetchFlaggedPost: {
                method: "POST",
                params: {
                    operation: "flagged_posts"
                }
            },
            fetchFlaggedForum: {
                method: "POST",
                params: {
                    operation: "flagged_forums"
                }
            }
        }
    )
}).service("ErrorResources", function (resource) {
    return resource("http://"+host+"/usap/errors/:operation",
        {operation:"initialize"},
        {
            fetchErrors:{
                method: "POST",
                params: {
                    operation: "load_errors"
                }
            },
            refreshErrors:{
                method:"POST",
                repeat: true,
                interval: 3000,
                params:{
                    operation:"refresh_errors"
                }
            }
        }
    );
});


