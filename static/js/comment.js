var temp=null,comment_textarea=angela._id("comment"),respond=angela._id("respond"),comment_parent=angela._id("comment_parent"),reply_link=angela._id("cancel-comment-reply-link"),text=angela.text(reply_link),serialize=function(a){if(a&&"FORM"===a.nodeName){var b,c,d=[];for(b=a.elements.length-1;b>=0;b-=1)if(""!==a.elements[b].name)switch(a.elements[b].nodeName){case"INPUT":switch(a.elements[b].type){case"text":case"email":case"hidden":case"password":case"button":case"reset":case"submit":d.push(a.elements[b].name+"="+encodeURIComponent(a.elements[b].value));break;case"checkbox":case"radio":a.elements[b].checked&&d.push(a.elements[b].name+"="+encodeURIComponent(a.elements[b].value))}break;case"TEXTAREA":d.push(a.elements[b].name+"="+encodeURIComponent(a.elements[b].value));break;case"SELECT":switch(a.elements[b].type){case"select-one":d.push(a.elements[b].name+"="+encodeURIComponent(a.elements[b].value));break;case"select-multiple":for(c=a.elements[b].options.length-1;c>=0;c-=1)a.elements[b].options[c].selected&&d.push(a.elements[b].name+"="+encodeURIComponent(a.elements[b].options[c].value))}break;case"BUTTON":switch(a.elements[b].type){case"reset":case"submit":case"button":d.push(a.elements[b].name+"="+encodeURIComponent(a.elements[b].value))}}return d.join("&")}};if(comment_parent.value&&(comment_parent.value=0,angela.hide(reply_link)),reply_link.addEventListener("click",function(a){a.preventDefault(),angela.hide(this),angela.text(this,text),comment_parent.value=0,temp&&respond&&(temp.parentNode.insertBefore(respond,temp),temp.parentNode.removeChild(temp))},!1),!angela._id("commentlist")){var com_list=document.createElement("div");com_list.id="commentlist",com_list.innerHTML='<ul class="commentlist"></ul>',angela.after(com_list,angela._id("comments-count"))}var commentlist=angela._selector("ul.commentlist",com_list)[0];angela._id("comment-tips").innerHTML='<div id="angela-loading"></div><div id="angela-error"></div>';var loading=angela._id("angela-loading"),error=angela._id("angela-error"),loading_pic='<img src="'+Crystal.loading+'" />',error_pic='<img src="'+Crystal.error+'" />';addComment={moveForm:function(a,b){var c=angela._id(a),d=angela._selector("#"+a+" > .comment-body .comment-id")[0],d=angela.text(d);angela._id("wp-temp-form-div")||(temp=document.createElement("div"),temp.id="wp-temp-form-div",temp.style.display="none",respond.parentNode.insertBefore(temp,respond)),c?c.parentNode.insertBefore(respond,c.nextSibling):(temp=angela._id("wp-temp-form-div"),comment_parent.value="0",temp.parentNode.insertBefore(respond,temp),temp.parentNode.removeChild(temp)),document.body.scrollTop=respond.offsetTop-80,comment_parent.value=b,angela.text(reply_link,text+" "+d),angela.show(reply_link);try{comment_textarea.focus()}catch(e){}return!1}};var comment_form=angela._id("commentform"),t=null;comment_form.addEventListener("submit",function(a){return a.preventDefault(),new angela.ajax({url:Crystal.ajax,method:"POST",dataType:"html",send:"action=angela_ajax_comment&"+serialize(this),before:function(){loading.innerHTML=loading_pic+" 正在提交, 请稍候...",angela.show(loading),angela.hide(error),clearTimeout(t),t=null},success:function(){var a=document.createElement("div");a.className="new-comment",a.innerHTML=this.data,temp?respond.parentNode.insertBefore(a,respond):commentlist.appendChild(a),angela.hide(loading),reply_link.click(),angela._id("comment").value=""},error:function(){error.innerHTML=error_pic+" "+this.data,angela.hide(loading),angela.show(error),t=setTimeout(function(){angela.hide(error)},3e3)},timeoutCallback:function(){this.error()}}),!1},!1);