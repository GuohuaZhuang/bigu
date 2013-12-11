/**
 * Util of json ajax.
 * author: firstboy
 * require: jquery
 */

function Jsoncallback(url, callback, method, data, loadid) {
	if (loadid) $('#'+loadid).fadeIn(200);
	$.ajax({
		type: method, 
		data: data, 
		scriptCharset: 'UTF-8', 
		dataType: 'json', 
		url: url, 
		success: function(json) {
			if (loadid) $('#'+loadid).fadeOut(200);
			if (callback != null) callback(json);
		}, 
		error: function(json, textStatus, errorThrown) {
			if (loadid) $('#'+loadid).fadeOut(200);
			// comment as not to alert when as net problems
			// if (callback != null) alert('[ERROR] -- JSON CALLBACK:\n'
			// 	+ '[ERROR:textStatus]: ' + textStatus 
			// 	+ '[ERROR:errorThrown]: ' + errorThrown 
			// 	+ '[ERROR:json.responseText]: ' + json.responseText);
		}
	});
}

function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + "=" + "(.+?)(&|$)").exec(location.search)||[,null])[1]
    );
}

function addslashes(string) {
    return string.replace(/\\/g, '\\\\').
        replace(/\u0008/g, '\\b').
        replace(/\t/g, '\\t').
        replace(/\n/g, '\\n').
        replace(/\f/g, '\\f').
        replace(/\r/g, '\\r').
        replace(/'/g, '\\\'').
        replace(/"/g, '\\"');
}
function trim(str){
	return str.replace(/(^\s*)|(\s*$)/g, "");
}
function ltrim(str){
	return str.replace(/(^\s*)/g,"");
}
function rtrim(str){
	return str.replace(/(\s*$)/g,"");
}