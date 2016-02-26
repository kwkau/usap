Controller.create("adminLogin",{
    elements:{reg_form:"#reg-form",log_form: "#login-form"},
    events:{},
    init: function(){
        this.form_validate(this.reg_form);//registration form validation
        this.form_validate(this.log_form);//login form validation
    }
});