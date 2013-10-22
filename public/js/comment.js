
function ConfirmDeleteCommentHandler(id) {
	if (confirm("确认要删除吗？")) {
		Jsoncallback('/post/comment/delete', 
	    	function (json) {
		        if (typeof json.err !== 'undefined' && json.err != "") {
		            alert(json.err);
		            return false;
		        }
		        if (typeof json.success === 'undefined') {
		    		return false;
				}
			}, 
		    'POST', 
		    {'d': '', 'timestamp' : Date.parse(new Date()), 
			 'id': id }, 
		    'loading');
	    $('#comment_item_'+id).remove();
	}
	return false;
}
