/**
 * Common forum utilities for USAP application
 */
var UsapForumUtils = {
    /**
     * Common smiley engine for forum comments
     * @param {Object} smileyBox - The element containing the smiley
     * @param {Object} context - The controller context
     * @param {Object} socket - The socket to use for communication
     */
    handleSmiley: function(smileyBox, context, socket) {
        // Extract common functionality from forum_comnt_smiley_engine
        // Previously duplicated in multiple controllers
    },
    
    /**
     * Load forum comments
     * @param {Object} element - The clicked element
     * @param {Object} context - The controller context
     * @param {Object} socket - The socket to use for communication
     * @param {String} magicIdSelector - The selector to find magic ID
     */
    loadComments: function(element, context, socket, magicIdSelector) {
        if(!element.parents(".usap-pod").data("commentLoad")) {
            var post_data = {
                type: "forum_comment_load",
                magic_id: element.parents(".usap-pod").data(magicIdSelector || "mgcid")
            };
            
            socket.post(post_data);
            element.parents(".usap-pod").data("commentLoad", true);
        }
    }
};
