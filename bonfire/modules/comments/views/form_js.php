
		var anonymous = '<?php echo($anonymous); ?>';
		$('#submit_comment').click(function(e) {
			e.preventDefault();
			if ($('#comment_txt').val == '') {
				$('#comment_txt').prev('.control-group').addClass('error');
				$('#comment_txt').next('.help-inline').html('A comment is required.');
			} else {
				var post_comment = true;
				if (anonymous == 'true')
				{
					if ($('#anonymous_email').val() == '')
					{
						post_comment = false;
						$('#anonymous_email').prev('.control-group').addClass('error');
						$('#anonymous_email').next('.help-inline').html('An email address is required for anonymous commenting.');
					}
					else
					{
						var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
						if ( !emailReg.test( $('#anonymous_email').val() ) )
						{
							post_comment = false;
							$('#anonymous_email').prev('.control-group').addClass('error');
							$('#anonymous_email').next('.help-inline').html('The email address entered is not valid.');
						}
					}
				}
				if (post_comment)
				{
					// RESET FORM Styles
					$('#comment_txt').prev('.control-group').removeClass('error');
					$('#comment_txt').next('.help-inline').html('');

					// HIDE ELEMS
					$('div#comments').css('display','none');
					$('form#comment_form').css('display','none');
					$('#submit_comment').attr('disabled', 'disabled');
					$('div#waitload').css('display','block');

					// POST comment and get updated comment thread
					var dataObj = '{"thread_id":<?php echo($thread_id); ?>, "author_id": <?php echo($user_id); ?>, "comment_txt":"' +  escape($("#comment_txt").val()) +'"}';
					$.post("<?php echo site_url('/comments/ajax_add'); ?>", {'items':dataObj, '<?php echo $csrf_token_name; ?>': '<?php echo $csrf_hash; ?>'}, function(data, status)
					{
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
									statusMess = 'Your comment was added successfully.';
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
			}
		});