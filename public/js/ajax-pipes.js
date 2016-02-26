// You can change these to see how pipe is affected
var haveStep1Fail = false;
var haveStep2Fail = false;

// Revealing module pattern for step 1 that ultimately calls an ajax command
var Step1 = (function () {
    var model = {};

    model.process = function() {
        log("starting step 1");
        
        var url = "http://fiddle.jshell.net/";
        if (haveStep1Fail) {
            url = "http://notvalid";
        }

        return $.ajax(url)
            .done(function () {
                log('step1 done');
            })
            .fail(function () {
                log('step1 failed');
            });
    };

    return model;
}());


// Revealing module pattern for step 2 that ultimately calls an ajax command
var Step2 = (function () {    
    var model = {};

    model.process = function() {
        log("starting step 2");

        var url = "http://fiddle.jshell.net/";
        if (haveStep2Fail) {
            url = "http://notvalid";
        }
        
        return $.ajax(url)
            .done(function () {
                log('step2 done');
            })
            .fail(function () {
                log('step2 failed');
            });
    };

    return model;
} () );

// this is just a simple logger so you don't have to use the console to see what is happening.
function log(message) {
    $("#output").append($("<div></div>").text(message));
};

log("start of process");

// the pipe method will chain calls together to allow for one step to rely on another.
Step1.process().pipe(Step2.process)
    .done(function () { log("the process completed successfully"); })
    .fail(function () { log("one of the steps failed"); })
    .always(function () { log("end of process"); });

// If you comment out the pipe lines and uncomment the $.when you can see that the when
// is similar but it will run each of the steps but then wait on their completion to begin.
//$.when(Step1.Get(), Step2.Get()).then(function () { console.log("done"); });


/*
 * what do i even seek to gain from using ajax pipes, why am i soo interested in this new technology
 * what advantages will obtain from using it, will there be any disadvantages? and even if i choose to adopt
 * it, how efficient will it make my applcations and where in my applications will, or may i say, should use this
 * method. a lot of questions for ajax pipes hmph. 
 * if ajax pipes are used to chain requests then how do chain reponses to an ajax request?
 * can it be done using the same ajax pipe method?
 * or cant?
 */