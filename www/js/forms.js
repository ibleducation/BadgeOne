/**
 * Login
 */
function logmedude(){

    $("#logform").submit(function( event ) {
        event.preventDefault();
        var is_email_reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/

        $("#errordiv").html("");
        $("#result" ).html("");


       if(!is_email_reg.test($("#emailreg").val())){

            $("#errordiv").show();
            $("#errordiv").html("A valid e-mail is required.");

        }else if(!$("#password1").val()){

            $("#errordiv").show();
            $("#errordiv").html("Password can't be empty.");


        }else{

            $("#errordiv").hide();
            var posting = $.post( "a_login.php", $("#logform").serialize(),function( data ) {

                if(parseInt(data) == 1){

                    $("#logform").hide();
                    $("#errordiv").hide();
                    $("#logindiv").show();
                    $("#logindiv").html("<img src='img/loading.gif' height='24' align='absbottom' >&#160; redirecting ...");

                    window.setTimeout(function(){
                        // Move to a new location or you can do something else
                        window.location.href = "./dashboard.php";
                    }, 2000);

                } else if(parseInt(data) == 2){
                    $("#errordiv").show();
                    $("#errordiv").html("Wrong email / password.");
                }

            } );
        }
    });
}

/**
 * Registration
 */
function regmedude(){

	$("#regform").submit(function( event ) {
	  event.preventDefault();
	  var is_email_reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/

	  $("#errordiv").html("");

	  if($("#password1").val() != $("#password2").val()){

		  $("#errordiv").show();
		  $("#errordiv").html("Passwords don't match.");

	  }else if(!is_email_reg.test($("#emailreg").val())){

	  	   $("#errordiv").show();
	  	   $("#errordiv").html("A valid e-mail is required.");

	  }else if(!$("#nameme").val()){

	  	   $("#errordiv").show();
	  	   $("#errordiv").html("Name is required.");


	  }else if(!$("#password1").val()){

	  	   $("#errordiv").show();
	  	   $("#errordiv").html("Password can't be empty.");


	  }else if( !$("#termcheck").prop( "checked" )){

	  	   $("#errordiv").show();
	  	   $("#errordiv").html(" You must accept terms and conditions.");


	  }else{

		$("#errordiv").hide();
		  var posting = $.post( "a_register.php", $("#regform").serialize(),function( data ) {

			   if(parseInt(data) == 1){

	                $("#regform").hide();
	                $("#msg1").hide();
	               	$("#errordiv").hide();
	               	$("#registerdiv").show();
			   	 	$("#registerdiv").html("Thanks for Registering!<br><br>You\'ve successfully created an account.<br> We've sent an account activation message to your email. To activate your account, click the link in the message.");

			   }else if(parseInt(data) == 2){

	               $("#errordiv").show();
				   $("#errordiv").html("Username already exists.");

			   }else if(parseInt(data) == 3){

	               $("#errordiv").show();
				   $("#errordiv").html("This email already exists.");

			   }else if(parseInt(data) == 11){
				   //autologin
	                $("#regform").hide();
	                $("#msg1").hide();
	               	$("#errordiv").hide();
	               	$("#registerdiv").show();
			   	 	$("#registerdiv").html("<img src='img/loading.gif' height='24' align='absbottom' >&#160; redirecting ...");

                    window.setTimeout(function(){
                        // Move to a new location or you can do something else
                        window.location.href = "./dashboard.php";
                    }, 2000);

			   }

		   } );
	 }
	});
}

/**
 * checkStrength
 */
function checkStrength(password)
	{
		//initial strength
		var strength = 0
		$('#pstrength').show()

		//if the password length is less than 6, return message.
		if (password.length < 6) {
			$('#pstrength').removeClass()
			$('#pstrength').addClass('short')
			$('#pstrength').html('Your Password is too short')
			return 'Too short'
		}

		//length is ok, lets continue.

		//if length is 8 characters or more, increase strength value
		if (password.length > 7) strength += 1

		//if password contains both lower and uppercase characters, increase strength value
		if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  strength += 1

		//if it has numbers and characters, increase strength value
		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  strength += 1

		//if it has one special character, increase strength value
		if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  strength += 1

		//if it has two special characters, increase strength value
		if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1

		//now we have calculated strength value, we can return messages

		//if value is less than 2
		if (strength < 2 )
		{
			$('#pstrength').removeClass()
			$('#pstrength').addClass('weak')
			$('#pstrength').html('Your Password is weak')
			return 'Weak'
		}
		else if (strength == 2 )
		{
			$('#pstrength').removeClass()
			$('#pstrength').addClass('good')
			$('#pstrength').html('Your Password is good')
			return 'Good'
		}
		else
		{
			$('#pstrength').removeClass()
			$('#pstrength').addClass('strong')
			$('#pstrength').html('Your Password is strong')
			return 'Strong'
		}
	}

/**
 * ResetAccount
 */
function resetmedude(){

    $("#resetform").submit(function( event ) {

    	event.preventDefault();

        $("#errordiv").html("");

        var is_email_reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/

        if(!is_email_reg.test($("#emailreg").val())){

            $("#errordiv").show();
            $("#errordiv").html("A valid e-mail is required.");

        } else {

            $("#errordiv").hide();

            var posting = $.post( "a_reset.php", $("#resetform").serialize(),function( data ) {

                if(parseInt(data) == 1){

                    $("#resetform").hide();
                    $("#msg1").hide();
                    $("#errordiv").hide();
                    $("#resetdiv").show();
                    $("#resetdiv").html("Email send");


                }

            } );

        }
    });
}

/**
 * Check Confirm
 * @param string
 * @returns {Boolean}
 */
function checkConfirm (txtConfirm) {
	if (confirm(txtConfirm)) {
		return true;
	} else {
		return false;
	}
}

function changepassdude(){

    $("#changepassform").submit(function( event ) {


        event.preventDefault();


        $("#errordiv").html("");


        var is_email_reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/

        if($("#password1").val() != $("#password2").val()){

            $("#errordiv").show();
            $("#errordiv").html("Passwords don't match.");

        }else if(!$("#password1").val()){

            $("#errordiv").show();
            $("#errordiv").html("Password can't be empty.");


        }else{

            $("#errordiv").hide();

            var posting = $.post( "a_changepass.php", $("#changepassform").serialize(),function( data ) {

                if(parseInt(data) == 1){

                    $("#changepassform").hide();
                    $("#msg1").hide();
                    $("#errordiv").hide();
                    $("#changediv").show();
                    $("#changediv").html("Password changed correctly");


                }else{

                    $("#changediv").show();
                    $("#changediv").html(data);
                }

            } );


        }


    });

}
