!function($){
    $(function () {
        Controller.create("usapProfileCntrl",{
            elements:usap_ele,
            events:{
                click:
                [
                    {target:"profile", selector:".edit", handler:"show_profile_edit_fld", type:"delegate"},
                    {target:"tab_nav", selector: ".close-item", handler:"tab_close", type:"delegate"},
                    {target:"mission_control", selector: "li", handler:"mission_click", type:"delegate"},
                    {target:"frnd_req_btn", handler:"friend_request"}
                ],
                keypress: {target:"profile", selector:".edit-field", handler:"profile_edit", type:"delegate"}
            },
            shared:general,
            init: function () {
                Tab_mdl.populate([
                    {id:"#profile-pane", name:"Profile",close:false}
                ]);
                this.start("user");
            },
            /*------------------------------------------
             * ignition for all websockets on this page
             *----------------------------------------*/
            socket_start: function () {
                //startup socket
                this.startup_socket = this.SharedResource.startUpSocket;
                this.startup_socket.start({
                    onopen: this.startup_open,
                    onmessage: this.startup_get,
                    onclose: this.startup_close,
                    scope:this
                });

                //profile socket
                this.profile_socket = this.DropZoneResource.profileSocket;
                this.profile_socket.start({
                    onopen:this.prof_open,
                    onmessage: this.prof_get,
                    onclose: this.prof_close,
                    scope:this
                });

                //user noti socket

            },
            prof_open: function (o) {
                this.profile_socket.post(this.id);

                /*---------------------------
                 * set profile socket events
                 *-------------------------*/
                this.profile_socket.bind("profile_load_ev",$.proxy(this.profile_load_handler,this));
            },
            prof_get: function (o) {
                /*alert(o.data);*/
                var packet = this.profile_socket.chck_data(o.data);
                if(packet && packet.packet_type == "profile_load"){
                    this.profile_socket.fire("profile_load_ev",packet);
                }
            },
            prof_close: function (o) {

            },
            profile_load_handler: function (packet) {
                this.spinner = false;
                Profile_mdl.populate(packet.payload);
            },
            show_profile_edit_fld: function (e) {
                var that = e.data.that;
                if($(this).hasClass("clicked")){
                    var text = $(this).data("spanText") ;
                    $(this).siblings("input").replaceWith(function (i) {
                        return $("<span>", {
                            text: $(this).val()||text, // getting input text
                            class: "text-capitalize"
                        })
                    });
                    $(this).siblings("span").data("prop",$(this).data("prop"));
                    $(this).removeClass("clicked");
                }else{
                    $(this).data("spanText",$(this).siblings("span").text());
                    $(this).data("prop",$(this).siblings("span").data("prop"));
                    $(this).siblings("span").replaceWith(function (i, text) {
                        return $("<input>", {
                            type: "text",//change the type
                            placeholder: text, //getting span text
                            name: $(this).data("prop"),
                            class: "text-capitalize edit-field"  //getting span id
                        })
                    });
                    $(this).addClass("clicked");
                }

            },
            profile_edit: function (e) {
                var that = e.data.that;
                    if(e.keyCode == 13){
                        if(!that.isEmpty(this)){
                            //obtain the entered text
                            var val = $(this).val(),
                            //obtain the name of the profile property to be changed
                                prop = $(this).attr("name"),
                                post_data = {
                                    type: "edit",
                                    field_name:prop,
                                    value: val
                                };

                            if(that.profile_socket.check()){
                                that.profile_socket.post(post_data);
                            }else{
                                alert("sorry, facing technical difficulties you cannot edit your profile at this time try again later")
                            }
                            //replace input with span
                            $(this).siblings("span").removeClass("clicked");
                            $(this).siblings("span").data("prop",prop);
                            $(this).replaceWith(function (i) {
                                return $("<span>", {
                                    text: $(this).val(), // getting input text
                                    class: "text-capitalize"
                                });
                            });


                        }
                    }
            },
            friend_request: function (e) {
                var that = e.data.that,
                /*
                 * obtain the id of the user we want to receive the request
                 * */
                    post_data = {
                        type:"friend-request",
                        request_data:{
                            target_id: $(this).data("uid"),
                            magic_id: that.uguid()
                        }
                    };

                that.startup_socket.post(post_data);
                if(that.startup_socket.check()){
                    that.create_user_noti({
                        target_id: post_data.request_data.target_id,
                        to_mgcid: post_data.request_data.magic_id,
                        to_type: "friend-request",
                        text: that.user_prof.full_name + that.friend_req_text,
                        created_at: that.timestamp()
                    });
                    $(this).replaceWith(function (i) {
                        return $("<button>",{
                            text:"Friends",
                            class:"btn btn-success"
                        })
                    })
                }

            }
        });
    })
}(window.jQuery);

