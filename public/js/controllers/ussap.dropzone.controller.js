
(function($){
    $(function(){
        Controller.create("ussap.dropzoneCntrl", {
            events: {
                submit:
                [
                    {target: "forum_form", handler: "forum_submit_handler"},
                    {target: "post_form", handler: "post_submit_handler"},
                    {target: "upload_form", handler: "upload_submit_handler"}
                ],
                click:
                    [
                        {target:"forum_usap_pod_wrapper", selector: ".comment-num", handler:"forum_comments_load", type:"delegate"},
                        {target:"forum_usap_pod_wrapper", selector: ".smiley", handler:"forum_comment_smiley", type:"delegate"},
                        {target:"post_usap_pod_wrapper", selector: ".comment-num", handler:"post_comment_load", type:"delegate"},
                        {target:"post_usap_pod_wrapper", selector: ".comments-cont-header .smiley", handler:"post_smiley", type:"delegate"},
                        {target:"post_usap_pod_wrapper", selector: ".comment-pod-wrapper .smiley", handler:"post_comment_smiley", type:"delegate"}
                    ],
                keyup:
                [
                    {target: "forum_usap_pod_wrapper", selector: "textarea", handler: "forum_comment_post", type:"delegate"},
                    {target: "noti_tab", selector: "*[data-top='forum'] textarea", handler: "noti_forum_comment_post", type:"delegate"},
                    {target: "post_usap_pod_wrapper", selector: "textarea", handler: "post_comment_post", type:"delegate"},
                    {target: "noti_tab", selector: "*[data-top='post'] textarea", handler: "noti_post_comment_post", type:"delegate"}
                ],
                scroll:[{target:"window",handler:"winscroll"}]
            },
            shared:{
                methods:general,
                properties:{
                    elements: usap_ele,
                    events: ussap_events
                }
            },

            /*----------------
             * run our app
             *---------------*/
            init : function() {
                this.start("user");
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

                //user forum socket
                this.frm_socket = this.DropZoneResource.forumSocket;
                this.frm_socket.start({
                    onopen: this.frmOpen,
                    onmessage: this.frmGet,
                    onclose: this.frmClose,
                    onerror: this.frmError,
                    scope:this
                });

                //user post socket
                this.post_socket = this.DropZoneResource.postSocket;
                this.post_socket.start({
                    onopen: this.postOpen,
                    onmessage: this.postGet,
                    onclose: this.postClose,
                    onerror: this.postErr,
                    scope:this
                });
            },
            /*-----------------------------
             * forum socket event handlers
             *---------------------------*/
            frmGet: function (o) {
                //todo: check to see if a reconnection has occurred

                    /*alert(o.data);*/
                    var packet = this.frm_socket.chck_data(o.data);
                    if(packet && packet.packet_type == "forum_load"){
                        this.frm_socket.fire("forum_load_ev",packet);
                    }else if(packet && packet.packet_type == "forum_creation"){
                        this.frm_socket.fire("forum_creation_ev",packet);
                    }else if(packet && packet.packet_type == "forum_comment_creation"){
                        this.frm_socket.fire("forum_comment_creation_ev",packet);
                    }else if(packet && packet.packet_type == "forum_comment_load"){
                        /*alert(o);*/
                        this.frm_socket.fire("forum_comment_load_ev",packet);
                    }else if(packet && packet.packet_type == "forum_comment_smiley_creation"){
                        this.frm_socket.fire("forum_comment_smiley_creation_ev",packet);
                    }else if(packet && packet.packet_type == "forum_comment_smiley_del"){
                        this.frm_socket.fire("forum_comment_smiley_del_ev",packet);
                    }

            },
            frmClose: function (o) {

            },
            frmError: function (o) {
                /*this.frm_socket.reconnect();*/
            },
            frmOpen: function (o) {
                this.frm_socket.post(this.id);

                /*-------------------------
                 * set forum socket events
                 *------------------------*/
                this.frm_socket.bind("forum_comment_smiley_del_ev", $.proxy(this.forum_comment_smiley_del_handler,this));
                this.frm_socket.bind("forum_comment_smiley_creation_ev", $.proxy(this.forum_comment_smiley_handler,this));
                this.frm_socket.bind("forum_comment_load_ev", $.proxy(this.forum_comment_load_handler,this));
                this.frm_socket.bind("forum_comment_creation_ev", $.proxy(this.forum_comment_creation_handler,this));
                this.frm_socket.bind("forum_creation_ev", $.proxy(this.forum_creation_handler,this));
                this.frm_socket.bind("forum_load_ev", $.proxy(this.forum_load_handler,this));
            },
            forum_comment_smiley_del_handler: function (packet) {
                var smiley_data = packet.payload;
                //subtract from smiley number
                this.smiley_process(smiley_data,"subtract");
            },
            forum_comment_smiley_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                var smiley_data = packet.payload;
                //add to smiley number
                this.smiley_process(smiley_data);
            },
            forum_comment_load_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                this.comment_render(packet.payload.forum_magic_id,packet.payload.comments,"forum",true,"top");
            },
            forum_comment_creation_handler: function (packet) {
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
            forum_creation_handler: function (packet) {
                Forum_mdl.display_mode = "append:top";
                Forum_mdl.populate(packet.payload);
            },
            forum_load_handler: function (packet) {
                this.frm_spinner = false;
                Forum_mdl.display_mode = "append:top";
                Forum_mdl.populate(packet.payload);
            },

            /*----------------------------
             * post socket event handlers
             *--------------------------*/
            postGet: function (o) {
                /*alert(o.data);*/
                var packet = this.post_socket.chck_data(o.data);
                if(packet && packet.packet_type == "post_load"){
                    this.post_socket.fire("post_load_ev",packet);
                }else if(packet && packet.packet_type == "post_creation"){
                    this.post_socket.fire("post_creation_ev",packet);
                }else if(packet && packet.packet_type == "post_smiley_creation"){
                    this.post_socket.fire("post_smiley_creation_ev",packet);
                }else if(packet && packet.packet_type == "post_smiley_del"){
                    this.post_socket.fire("post_smiley_del_ev",packet);
                }else if(packet && packet.packet_type == "post_comment_creation"){
                    this.post_socket.fire("post_comment_creation_ev",packet);
                }else if(packet && packet.packet_type == "post_comment_load"){
                    this.post_socket.fire("post_comment_load_ev",packet);
                }else if(packet && packet.packet_type == "post_comment_smiley_creation"){
                    this.post_socket.fire("post_comment_smiley_creation_ev",packet);
                }else if(packet && packet.packet_type == "post_comment_smiley_del"){
                    this.post_socket.fire("post_comment_smiley_del_ev",packet);
                }
            },
            postOpen: function (o) {
                this.post_spinner = true;
                this.post_socket.post(this.id);

                /*-----------------
                 * set post events
                 *---------------*/
                this.post_socket.bind("post_comment_smiley_del_ev", $.proxy(this.post_comment_smiley_del_handler,this));
                this.post_socket.bind("post_comment_smiley_creation_ev", $.proxy(this.post_comment_smiley_creation_handler,this));
                this.post_socket.bind("post_comment_load_ev", $.proxy(this.post_comment_load_handler,this));
                this.post_socket.bind("post_comment_creation_ev", $.proxy(this.post_comment_creation_handler,this));
                this.post_socket.bind("post_smiley_del_ev", $.proxy(this.post_smiley_del_handler,this));
                this.post_socket.bind("post_smiley_creation_ev", $.proxy(this.post_smiley_creation_handler,this));
                this.post_socket.bind("post_creation_ev", $.proxy(this.post_creation_handler,this));
                this.post_socket.bind("post_load_ev", $.proxy(this.post_load_handler,this));
            },
            postClose: function (o) {

            },
            postErr: function (o) {
                alert(JSON.stringify(o));
            },
            post_comment_smiley_del_handler: function (packet) {
                this.post_comment_smiley_process(packet.payload,"subtract");
            },
            post_smiley_del_handler: function (packet) {
                this.post_smiley_process(packet.payload,"subtract");
            },
            post_comment_smiley_creation_handler: function (packet) {
                this.post_comment_smiley_process(packet.payload);
            },
            post_comment_load_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                this.comment_render(packet.payload.post_magic_id,packet.payload.comments,"post",true,"top");
            },
            post_comment_creation_handler: function (packet) {
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
            post_smiley_creation_handler: function (packet) {
                this.post_smiley_process(packet.payload);
            },
            post_creation_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                if(this.post_socket.loaded){//post have been loaded
                    Post_mdl.display_mode = "append:top";
                    Post_mdl.populate(packet.payload);
                }else{
                    //notify the user of a new post
                    alert("you have a new post");
                }
            },
            post_load_handler: function (packet) {
                /*alert(JSON.stringify(packet));*/
                this.post_spinner = false;
                Post_mdl.display_mode = "append:bottom";
                Post_mdl.populate(packet.payload);
                this.post_socket.loaded = true;
            },

            /*-----------
             * page code
             *----------*/
            winscroll: function (e) {
                var that = e.data.that;
                if (that.document.height() <= that.window.scrollTop() + that.window.height()) {

                }
            },


            /*-----------------------
             * forum event handlers
             *----------------------*/
            forum_submit_handler: function (e) {
                //handler to create forums
                var that = e.data.that;
                //validate forum form
                /*--------------------------------------------------------------------
                 * should we send a notification when some one creates a user forum?
                 * i say know but what the hell, things can change
                 *------------------------------------------------------------------*/
                var parent = $(this).parent();
                if(that.form_check(parent)){
                    var post_data = {};

                    Forum_form_mdl.records[0]['magic_id'] = that.uguid();
                    Forum_form_mdl.records[0]['user_prof'] = that.user_prof;
                    Forum_form_mdl.records[0]['type'] = Forum_form_mdl.records[0]['type'].replace(/[^friendgeneral]/, function () {return ""});
                    Forum_form_mdl.records[0]['created_at'] =  that.timestamp();

                    post_data.forum_data = Forum_form_mdl.records[0];
                    post_data.type = "forum";
                    //check if the socket is open
                    if(that.frm_socket.check()){
                        try{
                            //render the just created forum to the user
                            Forum_mdl.display_mode = "append:top";
                            Forum_mdl.populate(post_data.forum_data);
                        }catch (Exception){
                            alert (Exception);
                            return false
                        }

                        //send the forum to the server
                        that.frm_socket.post(post_data);
                        that.form_reset(parent);
                    }else{
                        alert("sorry your forum could not be created try again later")
                    }

                }else{
                    //todo: create popup system
                    alert("fill all the fields in the form please");
                }

                return false;
            },
            forum_comment_post: function (e) {
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
                        if(field.parents(".usap-pod").data("commentLoad") == true){//comments have been loaded
                            that.comment_render(forum_comment_data.forum_magic_id,[forum_comment_data]);
                        }else{//comments not loaded
                            //load previous comments before you add the new one
                            //obtain the magic id of the forum
                            var post_data = {
                                type: "forum_comment_load",
                                magic_id: forum_comment_data.forum_magic_id
                            };

                            if (that.frm_socket.check()){
                                //send the forum_comment to the server if the user is connected to it
                                that.frm_socket.post(post_data);
                                field.parents(".usap-pod").data("commentLoad",true);
                            }
                            that.comment_render(forum_comment_data.forum_magic_id,[forum_comment_data]);
                        }

                        socket_data.forum_comment_data = forum_comment_data;
                        socket_data.type = "forum_comment";

                            //send the forum_comment to the server if the user is connected to it
                            that.frm_socket.post(socket_data);

                        //send a notification to the user the forum belongs to
                        if(field.parents(".usap-pod").data("id") != that.id){
                            that.create_user_noti({
                                target_id: forum_comment_data.user_id,
                                to_mgcid: forum_comment_data.forum_magic_id,
                                to_type: "forum",
                                text: that.user_prof.full_name + that.forum_comment_noti_text,
                                created_at: forum_comment_data.created_at
                            });
                        }

                    }else{
                        $(this).val("");
                    }

                }
            },
            forum_comments_load: function (e) {
                if(!$(this).parents(".usap-pod").data("commentLoad")) {
                    var that = e.data.that,
                    //obtain the magic id of the forum
                        post_data = {
                            type: "forum_comment_load",
                            magic_id: $(this).parents(".usap-pod").data("mgcid")
                        };

                        //send the forum_comment to the server if the user is connected to it
                            /*alert("post data: "+JSON.stringify(post_data));*/
                            that.frm_socket.post(post_data);
                            $(this).parents(".usap-pod").data("commentLoad",true);

                }
            },
            forum_comment_smiley: function (e) {
                var that = e.data.that,
                smiley_box = $(this);

                that.forum_comnt_smiley_engine(smiley_box,that,that.frm_socket);
            },

            /*---------------------
             * post event handlers
             *-------------------*/
            post_pane_click: function (e) {
                //time to load our posts
                var that = e.data.that;
                if(!$(this).data("loaded")){
                    that.post_spinner = true;
                    that.post_socket.post({type:"post_load"});
                    $(this).data("loaded",true);
                }
            },
            post_submit_handler: function (e) {
                try {
                    Post_form_mdl.records[0]['target'] = Post_form_mdl.records[0]['target'].replace(/[^friendgeneral]/, function () {return ""});
                    var that = e.data.that,
                        form = $(this),
                        file_data = form.find("input[type='file']")[0].files[0],
                        file_present = form.find("input[type='file']")[0].files.length > 0,post_data;
                    if (!that.isEmpty(form.find("select")) && (!that.isEmpty(form.find("textarea")) || file_present)) {
                        Post_form_mdl.records[0]['content_type'] = file_present ? "multimedia" : "text";
                        Post_form_mdl.records[0]['magic_id'] = that.uguid();
                        Post_form_mdl.records[0]['user_prof'] = that.user_prof;
                        Post_form_mdl.records[0]['created_at'] = that.timestamp();


                        if (file_present) {//we have a file to deal with
                            that.upld_spinner = true;

                            //check if the file size is within the allowed range 1 mb = 1,000,000
                            if(file_data.size <= 1000000 && file_data.type.search(/image.*/) >= 0){
                                /*var formdata = new FormData();
                                formdata.append("file_name",file[0].name);
                                formdata.append("post-pic",file[0]);*/

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
                                        that.post_socket.post(post_data, function () {
                                            //render the just created post with its picture as a base64 encoded data url
                                            var post_reader = new FileReader();
                                            post_reader.onloadend = function (e) {
                                                that.uploading = false;
                                                Post_form_mdl.records[0]['pic_url']= e.target.result;

                                                Post_mdl.display_mode = "append:top";
                                                Post_mdl.populate(Post_form_mdl.records[0]);
                                                Post_form_mdl.records[0]["post_text"]="";
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
                            /*alert("sending "+JSON.stringify(post_data));*/
                            that.post_socket.post(post_data);

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
            post_comment_load: function (e) {
                if(!$(this).parents(".usap-pod").data("commentLoad")) {
                    var that = e.data.that,
                    //obtain the magic id of the post
                        post_data = {
                            type: "post_comment_load",
                            magic_id: $(this).parents(".usap-pod").data("mgcid")
                        };

                    //send the post_comment_load request to the server
                    that.post_socket.post(post_data);
                    $(this).parents(".usap-pod").data("commentLoad",true);

                }
            },
            post_smiley: function (e) {
                /*alert("hi)*/
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
                that.smiley_box_process(post_smiley_box,that.post_socket,post_data,"post_smiley_del");

            },
            post_comment_post: function (e) {
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
                                that.post_socket.post(post_data);
                                field.parents(".usap-pod").data("commentLoad",true);

                            that.comment_render(mgcid,[post_comment_data],"post");
                        }



                        socket_data.post_comment_data = post_comment_data;
                        socket_data.type = "post_comment";
                            //send the post_comment to the server if the user is connected to it
                            that.post_socket.post(socket_data);

                        //send a notification to the user the post belongs to
                        if(post_comment_data.user_id != that.id){
                            that.create_user_noti({
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
            post_comment_smiley: function (e) {
                /*alert("hi")*/
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
                that.comment_smiley_box_process(smiley_box,that.post_socket,post_data,"post_comment_smiley_del");
            }
        });
    });
})(window.jQuery);