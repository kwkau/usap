(function($){
    $(function(){
        Controller.create("ussap.homeCntrl", {
            elements: {loginForm:"#login-form", regForm: "#reg-form", cat:"#cat"},
            events: {change:[{target:"cat",handler:"show_hide"}]},
           init : function(){
               this.form_validate(this.regForm);
               this.form_validate(this.loginForm);
           },
            show_hide: function(e){
                var that = e.data.that,cat_val = $(this).val();
                if(cat_val == "lecturer"){
                    $(this).parent().find("*[name='year_start']").val("0000");
                    $(this).parent().find("*[name='year_complete']").val("0000");
                    $(this).parent().find("*[name='index_number']").val("ab/cde/88/1234");
                    $(this).parent().find("*[name='token']").val("");
                    that.yrCmplt = false;
                    that.yrStart = false;
                    that.index = false;
                    that.token = true;
                }else{
                    $(this).parent().find("*[name='year_start']").val("");
                    $(this).parent().find("*[name='year_complete']").val("");
                    $(this).parent().find("*[name='index_number']").val("");
                    $(this).parent().find("*[name='token']").val("null");
                    that.yrCmplt = true;
                    that.yrStart = true;
                    that.index = true;
                    that.token = false;
                }
            }
        });
    });
})(window.jQuery);