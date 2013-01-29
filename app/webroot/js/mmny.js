    $(document).ready(function(){
        $('#LocationType').change(function(){             
            var val = $("#LocationType").val();
			if(val == "Block Party or Street Fair")  {   
    	    	$('#LocationType').after('<tr colspan="2"><td><em>Block parties may only be hosted by qualified organizations; special permit rules apply.Before registering for a block party, click <a href="http://www.makemusicny.org/assets/resources/mmny-block-parties.pdf">here</a> to download full instructions.</em></td></tr>');
 			}
  		});

		$('#e2ma_signup input[type="text"]').val("Sign Up For Email Newsletter");
		$('#e2ma_signup input[type="text"]').focus(function() {
			$(this).val("");
			$("input#e2ma_signup_submit_button").show();
		});
		$("input#e2ma_signup_submit_button").hover(
			function() {$(this).css('text-decoration','underline'); },
			function() {$(this).css('text-decoration','none'); }
		);
		$("input#e2ma_signup_submit_button").focus(function() {
			$(this).css('text-decoration','underline');
		});

    });
