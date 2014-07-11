
	$('#reload_comments').click(function(e) {
		e.preventDefault();
		load_comments();
	});

	function load_comments() {
		var statusClass = '', statusMess = ''; 
		$.getJSON('<?php echo site_url('/comments/ajax_get/'.$thread_id); ?>', function(data, status){
			switch (status) 
			{
				case 'success':
					if (data.status.indexOf(":") != -1) 
					{
						var status = data.status.split(":");
						statusClass = 'alert-' + status[0];
						statusMess = status[1];
					}
					else 
					{
						statusClass = 'alert-success';
						statusMess = 'Comments loaded.';
						_draw_comments(data);
					}
					break;
				case 'timeout':
					statusClass = 'alert-error';
					statusMess = 'The server did not respond. please try submitting again.';
				case 'error':
					statusClass = 'alert-error';
					statusMess = 'Ann error occured processing your request. Error:' + data;
					break;
			}
			$('div#ajaxStatus').addClass(statusClass);
			$('div#ajaxStatus').html(statusMess);
			$('div#ajaxStatusBox').fadeIn("slow",function() { 
                            setTimeout( function() {
                                $('div#ajaxStatusBox').fadeOut("normal",function() { 
                                    $('div#ajaxStatusBox').hide(); 
                                });
                             }, 5000);
                         });

			// SHOW ELEMS, but not the form
			$('div#comments').css('display','block');
			$('div#waitload').css('display','none');
		}, 'json');	
	}
	function _draw_comments(data) {
		if (data.result.items.length > 0) {
			$('div#comments').empty();
			var commentStr = '',count = 1;
			$.each(data.result.items, function(i,item){
				commentStr += "<div class=\"well\">";
				commentStr += "<div data-toggle=\"collapse\" href=\"#comment"+count+"\">\n";
				commentStr += "<b>"+item.creator + " " + item.created + "</b>\n";
				commentStr += "</div>\n";
				commentStr += "<div class=\"well\" id=\"comment"+count+"\">\n";
				commentStr += "<p>" + item.comment + "</p>\n";
				commentStr += "</div>\n";
				commentStr += "</div>\n";
				count++;
			});
			$('div#comments').append(commentStr);
			return true;
		} else {
			return false;
		}
	}
	function fadeStatus() {
		
	}