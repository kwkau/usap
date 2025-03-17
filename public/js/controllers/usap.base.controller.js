/**
 * Base Controller for USAP
 * 
 * Provides common functionality for all controllers
 */
var UsapBaseController = {
    // Common properties
    user_prof: null,
    
    // Common methods
    isEmpty: function(element) {
        return element.val().trim() === "";
    },
    
    init: function() {
        // Common initialization
    },
    
    forum_comnt_smiley_engine: function(smileyBox, context, socket) {
        // Extract common functionality from both controllers
        // This avoids code duplication
    },
    
    // Common validation methods
    validateInput: function(input, rules) {
        // Generic validation logic
    }
};
