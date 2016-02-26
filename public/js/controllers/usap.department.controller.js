

(function($){

})(window.jQuery);


(function ($) {
    $(function () {
        Controller.create("usap.departmentCntrl",{
            elements:usap_ele,
            events:{
                submit:
                    [
                        {target: "forum_form", handler: "dep_forum_submit_handler"},
                        {target: "post_form", handler: "dep_post_submit_handler"}
                    ],
                keyup:[
                    {target: "forum_usap_pod_wrapper", selector: "textarea", handler: "dep_forum_comment_post", type:"delegate"},
                    {target: "post_usap_pod_wrapper", selector: "textarea", handler: "dep_post_comment_post", type:"delegate"}
                ],
                click:[
                    {target:"forum_usap_pod_wrapper", selector: ".comment-num", handler:"dep_forum_comments_load", type:"delegate"},
                    {target:"forum_usap_pod_wrapper", selector: ".smiley", handler:"dep_forum_comment_smiley", type:"delegate"},
                    {target:"post_usap_pod_wrapper", selector: ".comment-num", handler:"dep_post_comment_load", type:"delegate"},
                    {target:"post_usap_pod_wrapper", selector: ".comments-cont-header .smiley", handler:"dep_post_smiley", type:"delegate"},
                    {target:"post_usap_pod_wrapper", selector: ".comment-pod-wrapper .smiley", handler:"dep_post_comment_smiley", type:"delegate"}
                ]
            },
            shared:{
                methods:general,
                properties:{
                    elements: usap_ele,
                    events: ussap_events
                }
            },
            init: function () {
                this.start("department");
            },

            /*-------------------------------------------
             * ignition for all websockets on this page
             *-----------------------------------------*/
            socket_start: function () {
                //startup socket
                this.startup_socket = this.SharedResource.startUpSocket;
                this.startup_socket.start({
                    onopen: this.startup_open,
                    onmessage: this.startup_get,
                    onclose: this.startup_close,
                    scope:this
                });

                //Group forum socket
                this.dep_forum_sckt = this.DepartmentResource.departmentForumSocket;
                this.dep_forum_sckt.start({
                    onopen:this.forum_open,
                    onmessage: this.forum_get,
                    onclose: this.forum_close,
                    onerror: this.forum_err,
                    scope: this
                });

                //Group post socket
                this.dep_post_socket = this.DepartmentResource.departmentPostSocket;
                this.dep_post_socket.start({
                    onopen:this.post_open,
                    onmessage: this.post_get,
                    onclose: this.post_close,
                    onerror: this.post_err,
                    scope: this
                });
            },
            forum_err: function (o) {
                alert(o);
            } ,
            forum_get: function (o) {
                /*alert(o.data);*/
                var packet = this.dep_forum_sckt.chck_data(o.data);
                if(packet && packet.packet_type == "dep_forum_load"){
                    this.dep_forum_sckt.fire("dep_forum_load_ev",packet);
                }else if(packet && packet.packet_type == "dep_forum_creation"){
                    this.dep_forum_sckt.fire("dep_forum_creation_ev",packet);
                }else if(packet && packet.packet_type == "dep_forum_comment_creation"){
                    this.dep_forum_sckt.fire("dep_forum_comment_creation_ev",packet);
                }else if(packet && packet.packet_type == "dep_forum_comment_load"){
                    this.dep_forum_sckt.fire("dep_forum_comment_load_ev",packet);
                }else if(packet && packet.packet_type == "dep_forum_comment_smiley_del"){
                    this.dep_forum_sckt.fire("dep_forum_comment_smiley_del_ev",packet);
                }else if(packet && packet.packet_type == "dep_forum_comment_smiley_creation"){
                    this.dep_forum_sckt.fire("dep_forum_comment_smiley_ev",packet);
                }

            },
            forum_close: function (o) {

            },
            forum_open: function (o) {
                this.dep_forum_sckt.post({"user_id":this.id, "dep_id": this.dep.id});

                /*-------------------------------
                 * set group forum socket events
                 *-----------------------------*/
                this.dep_forum_sckt.bind("dep_forum_load_ev", $.proxy(this.dep_forum_load_handler,this));
                this.dep_forum_sckt.bind("dep_forum_creation_ev", $.proxy(this.dep_forum_creation_handler,this));
                this.dep_forum_sckt.bind("dep_forum_comment_creation_ev", $.proxy(this.dep_forum_comment_creation_handler,this));
                this.dep_forum_sckt.bind("dep_forum_comment_load_ev", $.proxy(this.dep_forum_comment_load_handler,this));
                this.dep_forum_sckt.bind("dep_forum_comment_smiley_del_ev", $.proxy(this.dep_forum_comment_smiley_del_handler,this));
                this.dep_forum_sckt.bind("dep_forum_comment_smiley_ev", $.proxy(this.dep_forum_comment_smiley_handler,this));
            },
            dep_forum_comment_smiley_del_handler: function (packet) {
                var smiley_data = packet.payload;
                //subtract from smiley number
                this.smiley_process(smiley_data,"subtract");
            },
            dep_forum_comment_smiley_handler: function (packet) {
                var smiley_data = packet.payload;
                //add to smiley number
                this.smiley_process(smiley_data);
            },
            dep_forum_load_handler: function (packet) {
                this.frm_spinner = false;
                Forum_mdl.display_mode = "append:top";
                Forum_mdl.populate(packet.payload);
            },
            dep_forum_creation_handler: function (packet) {
                Forum_mdl.display_mode = "append:top";
                Forum_mdl.populate(packet.payload);
            },
            dep_forum_comment_creation_handler: function (packet) {
                //check if the forum has loaded its comments
                var that = this;
                this.forum_usap_pod_wrapper.find(".usap-pod").each(function () {
                    if($(this).data("mgcid") == packet.payload.forum_magic_id){
                        if($(this).data("commentLoad")){//comments loaded
                            that.comment_render(packet.payload.forum_magic_id,[packet.payload]);
                        }else{//comments not loaded
                            var com_num = $(this).find("*[data-sw-increm]");
                            com_num.text((isNaN(parseInt(com_num.text()))?0:parseInt(com_num.text()))+1);
                        }
                    }

                });
            },
            dep_forum_comment_load_handler: function (packet) {
                this.comment_render(packet.payload.forum_magic_id,packet.payload.comments,"forum",true,"top");
            },

            /*----------------------------------
             * group post socket event handlers
             *--------------------------------*/
            post_get: function (o) {

                var packet = this.dep_post_socket.chck_data(o.data);
                if(packet.packet_type == "dep_post_load"){
                    this.dep_post_socket.fire("dep_post_load_ev",packet);
                }else if(packet.packet_type == "dep_post_creation"){
                    this.dep_post_socket.fire("dep_post_creation_ev",packet);
                }else if(packet.packet_type == "dep_post_smiley_creation"){
                    this.dep_post_socket.fire("dep_post_smiley_creation_ev",packet);
                }else if(packet.packet_type == "dep_post_smiley_del"){
                    this.dep_post_socket.fire("dep_post_smiley_del_ev",packet);
                }else if(packet.packet_type == "dep_post_comment_creation"){
                    this.dep_post_socket.fire("dep_post_comment_creation_ev",packet);
                }else if(packet.packet_type == "dep_post_comment_load"){
                    this.dep_post_socket.fire("dep_post_comment_load_ev",packet);
                }else if(packet.packet_type == "dep_post_comment_smiley_creation"){
                    this.dep_post_socket.fire("dep_post_comment_smiley_creation_ev",packet);
                }else if(packet.packet_type == "dep_post_comment_smiley_del"){
                    this.dep_post_socket.fire("dep_post_comment_smiley_del_ev",packet);
                }
            },
            post_close: function (o) {

            },
            post_err: function (o) {

            },
            post_open: function (o) {
                this.dep_post_socket.post({"user_id":this.id, "dep_id": this.dep.id});

                /*----------------------------
                 * set grp_post_socket events
                 *--------------------------*/
                this.dep_post_socket.bind("dep_post_load_ev", $.proxy(this.dep_post_load_handler,this));
                this.dep_post_socket.bind("dep_post_creation_ev", $.proxy(this.dep_post_creation_handler,this));
                this.dep_post_socket.bind("dep_post_smiley_creation_ev", $.proxy(this.dep_post_smiley_creation_handler,this));
                this.dep_post_socket.bind("dep_post_smiley_del_ev", $.proxy(this.dep_post_smiley_del_handler,this));
                this.dep_post_socket.bind("dep_post_comment_creation_ev", $.proxy(this.dep_post_comment_creation_handler,this));
                this.dep_post_socket.bind("dep_post_comment_load_ev", $.proxy(this.dep_post_comment_load_handler,this));
                this.dep_post_socket.bind("dep_post_comment_smiley_creation_ev", $.proxy(this.dep_post_comment_smiley_creation_handler,this));
                this.dep_post_socket.bind("dep_post_comment_smiley_del_ev", $.proxy(this.dep_post_comment_smiley_del_handler,this));
            },
            dep_post_comment_smiley_del_handler: function (packet) {
                this.post_comment_smiley_process(packet.payload,"subtract");
            },
            dep_post_comment_smiley_creation_handler: function (packet) {
                this.post_comment_smiley_process(packet.payload);
            },
            dep_post_comment_load_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                this.comment_render(packet.payload.post_magic_id,packet.payload.comments,"post",true,"top");
            },
            dep_post_comment_creation_handler: function (packet) {
                //check if the for has loaded its comments
                var that = this;
                this.post_usap_pod_wrapper.find(".usap-pod").each(function () {
                    if($(this).data("mgcid") == packet.payload.post_magic_id){
                        if($(this).data("commentLoad")){//comments loaded
                            that.comment_render(packet.payload.post_magic_id,[packet.payload],"post");
                        }else{//comments not loaded
                            var com_num = $(this).find("*[data-sw-increm]");
                            com_num.text((isNaN(parseInt(com_num.text()))?0:parseInt(com_num.text()))+1);
                        }
                    }

                });
            },
            dep_post_smiley_creation_handler: function (packet) {
                this.post_smiley_process(packet.payload);
            },
            dep_post_smiley_del_handler: function (packet) {
                this.post_smiley_process(packet.payload,"subtract");
            },
            dep_post_creation_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                if(this.dep_post_socket.loaded){//post have been loaded
                    Post_mdl.display_mode = "append:top";
                    Post_mdl.populate(packet.payload);
                }else{
                    //notify the user of a new post
                    alert("you have a new post");
                }
            },
            dep_post_load_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                this.post_spinner = false;
                Post_mdl.display_mode = "append:bottom";
                Post_mdl.populate(packet.payload);
                this.dep_post_socket.loaded = true;
            },

            /*----------------------------
             * group forum event handlers
             *--------------------------*/
            dep_forum_submit_handler: function (e) {
                //handler to create forums
                var that = e.data.that;
                //validate forum form

                var parent = $(this).parent();
                if(that.form_check(parent)){
                    var post_data = {};

                    Forum_form_mdl.records[0]['magic_id'] = that.uguid();
                    Forum_form_mdl.records[0]['user_prof'] = that.user_prof;
                    Forum_form_mdl.records[0]['type'] = "department";
                    Forum_form_mdl.records[0]['created_at'] =  that.timestamp();

                    post_data.forum_data = Forum_form_mdl.records[0];
                    post_data.forum_data.dep_id = that.dep.id;
                    post_data.type = "forum";
                    //check if the socket is open
                    if(that.dep_forum_sckt.check()){
                        try{
                            //render the just created forum to the user
                            Forum_mdl.display_mode = "append:top";
                            Forum_mdl.populate(post_data.forum_data);
                        }catch (Exception){
                            alert (Exception);
                            return false
                        }

                        //send the forum to the server
                        that.dep_forum_sckt.post(post_data);
                        that.form_reset(parent);

                        /*-------------------------------------------------------------------------------------
                         * send a notification to the members of the group that a group forum has been created
                         *-----------------------------------------------------------------------------------*/
                        that.create_dep_noti({
                            group_id:that.dep.id,
                            to_mgcid:post_data.forum_data.magic_id,
                            to_type:"forum",
                            text:" Created a Forum in "+that.dep.name,
                            created_at:that.timestamp()
                        });
                    }else{
                        alert("sorry your forum could not be created try again later")
                    }

                }else{
                    //todo: create popup system
                    alert("fill all the fields in the form please");
                }

                return false;
            },
            dep_forum_comment_post: function (e) {
                var that = e.data.that;
                if(e.keyCode == 13){
                    //the enter key has been pressed
                    if(!that.isEmpty(this)){
                        var socket_data = {},field = $(this),
                            forum_comment_data = {
                                text: field.val(),
                                user_prof: that.user_prof,
                                created_at: that.timestamp(),
                                forum_magic_id: field.parents(".usap-pod").data("mgcid"),
                                magic_id: that.uguid(),
                                forum_type: field.parents(".usap-pod").data("type"),
                                user_id: field.parents(".usap-pod").data("id"),
                                smileys:[]
                            };
                        field.val("");

                        //check the comments for this forum have been loaded
                        if(field.parents(".usap-pod").data("commentLoad")){//comments have been loaded
                            that.comment_render(forum_comment_data.forum_magic_id,[forum_comment_data]);
                        }else{//comments not loaded
                            //load previous comments before you add the new one
                            //obtain the magic id of the forum
                            var post_data = {
                                type: "forum_comment_load",
                                magic_id: forum_comment_data.forum_magic_id
                            };

                            if (that.dep_forum_sckt.check()) {
                                //send the grp_forum_comment to the server if the user is connected to it
                                that.dep_forum_sckt.post(post_data);
                                field.parents(".usap-pod").data("commentLoad",true);
                            }
                            that.comment_render(forum_comment_data.forum_magic_id,[forum_comment_data]);
                        }



                        socket_data.forum_comment_data = forum_comment_data;
                        socket_data.type = "forum_comment";


                        //send the forum_comment to the server if the user is connected to it
                        that.dep_forum_sckt.post(socket_data);

                        //send a notification to the user the forum belongs to
                        if(field.parents(".usap-pod").data("id") != that.id){
                            that.create_dep_noti({
                                target_id: forum_comment_data.user_id,
                                to_mgcid: forum_comment_data.forum_magic_id,
                                to_type: "forum",
                                text: that.user_prof.full_name+that.forum_comment_noti_text,
                                created_at: forum_comment_data.created_at
                            });
                        }

                    }else{
                        $(this).val("");
                    }

                }
            },
            dep_forum_comments_load: function (e) {
                if(!$(this).parents(".usap-pod").data("commentLoad")) {
                    var that = e.data.that,
                    //obtain the magic id of the forum
                        post_data = {
                            type: "forum_comment_load",
                            magic_id: $(this).parents(".usap-pod").data("mgcid")
                        };

                    //send the forum_comment to the server if the user is connected to it
                    that.dep_forum_sckt.post(post_data);
                    $(this).parents(".usap-pod").data("commentLoad",true);

                }
            },
            dep_forum_comment_smiley: function (e) {
                var that = e.data.that,
                    smiley_box = $(this);

                that.forum_comnt_smiley_engine(smiley_box,that,that.dep_forum_sckt);
            },

            /*---------------------------
             * group post event handlers
             *-------------------------*/
            post_pane_click: function (e) {
                //time to load our posts
                var that = e.data.that;
                if(!$(this).data("loaded")){
                    that.post_spinner = true;
                    that.dep_post_socket.post({type:"post_load", dep_id: that.dep.id});
                    $(this).data("loaded",true);
                }
            },
            dep_post_submit_handler: function (e) {
                try {
                    Post_form_mdl.records[0]['target'] = "department";
                    var that = e.data.that,
                        form = $(this),
                        file_data = form.find("input[type='file']")[0].files[0],
                        file_present = form.find("input[type='file']")[0].files.length > 0,post_data;
                    if (!that.isEmpty(form.find("textarea")) || file_present) {
                        Post_form_mdl.records[0]['content_type'] = file_present ? "multimedia" : "text";
                        Post_form_mdl.records[0]['magic_id'] = that.uguid();
                        Post_form_mdl.records[0]['user_prof'] = that.user_prof;
                        Post_form_mdl.records[0]['created_at'] = that.timestamp();
                        Post_form_mdl.records[0]['dep_id'] = that.dep.id;



                        if (file_present) {//we have a file to deal with
                            that.upld_spinner = true;

                            //check if the file size is within the allowed range 1 mb = 1,000,000
                            if(file_data.size <= 1000000 && file_data.type.search(/image.*/) >= 0){
                                /*
                                 var formdata = new FormData();
                                 formdata.append("file_name",file[0].name);
                                 formdata.append("post-pic",file[0]);
                                 */

                                //upload the file using ajax
                                jQuery.ajax({
                                    data:file_data,
                                    processData: false,
                                    url: "http://localhost/usap/dropzone/upload_post_pic",
                                    type: "POST",
                                    dataType: "text",
                                    beforeSend: function(xhr, settings){
                                        xhr.setRequestHeader("Cache-Control", "no-cache");
                                        xhr.setRequestHeader("X-File-Name", file_data.name);
                                        xhr.setRequestHeader("X-File-Size", file_data.size);
                                        xhr.setRequestHeader("X-File-Type", file_data.type);
                                    },
                                    success: function (o) {
                                        that.upld_spinner = false;
                                        Post_form_mdl.records[0]["pic_url"] = o;
                                        post_data = {
                                            type: "post",
                                            post_data: Post_form_mdl.records[0]
                                        };
                                        //send the post data to the post socket
                                        /*alert("sending: "+JSON.stringify(post_data));*/
                                        that.dep_post_socket.post(post_data, function () {
                                            //render the just created post with its picture as a base64 encoded data url
                                            var post_reader = new FileReader();
                                            post_reader.onloadend = function (e) {
                                                that.uploading = false;
                                                Post_form_mdl.records[0]['pic_url']= e.target.result;

                                                Post_mdl.display_mode = "append:top";
                                                Post_mdl.populate(Post_form_mdl.records[0]);
                                                Post_form_mdl.records[0]["post_text"] = "";
                                            };
                                            post_reader.onerror = function (e) {
                                                alert(JSON.stringify(e));
                                            };

                                            post_reader.readAsDataURL(file_data.slice(0, file_data.size));
                                        });
                                    }
                                });


                            }else{
                                alert("the file is too big or you uploading an invalid file, you can only upload pictures");
                            }
                        }else{
                            //no picture selected
                            post_data = {
                                type: "post",
                                post_data: Post_form_mdl.records[0]
                            };
                            //send the post data to the post socket
                            that.dep_post_socket.post(post_data);

                            //render the just created post
                            Post_mdl.display_mode = "append:top";
                            Post_mdl.populate(Post_form_mdl.records[0]);
                            Post_form_mdl.records[0]["post_text"]="";
                        }

                        //reset error classes
                        form.find("select").removeClass("usap-error");
                        form.find("textarea").removeClass("usap-error");
                        form.find("input[type='file']").removeClass("usap-error");
                        //empty our form inputs
                        that.form_reset(form);
                        form.find("input[type='file']")[0].files = [];
                    } else {
                        that.isEmpty(form.find("select")) ? form.find("select").addClass("usap-error"): form.find("select").removeClass("usap-error");
                        that.isEmpty(form.find("textarea")) ? form.find("textarea").addClass("usap-error"): form.find("textarea").removeClass("usap-error");
                        !file_present?form.find("input[type='file']").addClass("usap-error"):form.find("input[type='file']").removeClass("usap-error");
                    }

                }catch (er){
                    alert(er)
                }
                return false;
            },
            dep_post_comment_load: function (e) {
                if(!$(this).parents(".usap-pod").data("commentLoad")) {
                    var that = e.data.that,
                    //obtain the magic id of the post
                        post_data = {
                            type: "post_comment_load",
                            magic_id: $(this).parents(".usap-pod").data("mgcid")
                        };

                    //send the post_comment_load request to the server
                    that.dep_post_socket.post(post_data);
                    $(this).parents(".usap-pod").data("commentLoad",true);

                }
            },
            dep_post_smiley: function (e) {
                var that = e.data.that,
                    post_smiley_box = $(this),
                //obtain the data needed to create the smiley
                    post_target = post_smiley_box.parents(".usap-pod"),
                    post_data = {
                        post_smiley_data:{
                            target: post_target.data("target"),
                            user_id: post_target.data("id"),
                            post_magic_id: post_target.data("mgcid")
                        },
                        type: "post_smiley"
                    };


                //check if the user has liked the post
                that.smiley_box_process(post_smiley_box,that.dep_post_socket,post_data,"post_smiley_del");

            },
            dep_post_comment_post: function (e) {
                var that = e.data.that;
                if(e.keyCode == 13){
                    if(!that.isEmpty(this)){
                        var socket_data = {},field = $(this),
                            mgcid = field.parents(".usap-pod").data("mgcid"),
                            post_comment_data = {
                                text:field.val(),
                                user_prof:that.user_prof,
                                created_at:that.timestamp(),
                                post_magic_id: mgcid,
                                magic_id: that.uguid(),
                                target: field.parents(".usap-pod").data("target"),
                                user_id: field.parents(".usap-pod").data("id"),
                                smileys:[]
                            };
                        field.val("");

                        //check the comments for this forum have been loaded
                        if(field.parents(".usap-pod").data("commentLoad")){//comments have been loaded
                            that.comment_render(mgcid,[post_comment_data],"post");
                        }else{//comments not loaded
                            //load previous comments before you add the new one
                            var post_data = {
                                type: "post_comment_load",
                                magic_id: mgcid
                            };


                            //send the post_comment_load request to the server
                            that.dep_post_socket.post(post_data);
                            field.parents(".usap-pod").data("commentLoad",true);

                            that.comment_render(mgcid,[post_comment_data],"post");
                        }



                        socket_data.post_comment_data = post_comment_data;
                        socket_data.type = "post_comment";
                        //send the post_comment to the server if the user is connected to it
                        that.dep_post_socket.post(socket_data);

                        //send a notification to the user the post belongs to
                        if(post_comment_data.user_id != that.id){
                            that.create_dep_noti({
                                department_id: that.dep.id,
                                target_id: post_comment_data.user_id,
                                to_mgcid: mgcid,
                                to_type: "post",
                                text: that.user_prof.full_name + that.post_comment_noti_text,
                                created_at: post_comment_data.created_at
                            });
                        }

                    }else{
                        $(this).val("");
                    }
                }
            },
            dep_post_comment_smiley: function (e) {
                var that = e.data.that,
                    smiley_box = $(this),
                //obtain data needed to create the smiley
                    post_target = smiley_box.parents(".usap-pod"),
                    post_comment = smiley_box.parents(".comment-pod"),
                    post_data ={
                        post_comment_smiley_data:{
                            target: post_target.data("target"),
                            user_id: post_target.data("id"),
                            comment_magic_id: post_comment.data("comment"),
                            post_magic_id: post_target.data("mgcid")
                        },
                        type:"post_comment_smiley"
                    };

                //check if the user has liked the comment
                that.comment_smiley_box_process(smiley_box,that.dep_post_socket,post_data,"post_comment_smiley_del");
            }
        })
    });
})(window.jQuery);