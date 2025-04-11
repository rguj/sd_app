<?php
require_once('_autoload.php');

if(!is_null($_SESSION['uid'])) {
	$_SESSION['msgs'][] = ['success', 'You have already logged in!'];
	redirect('home.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
	<?php require_once('_head_asset.php'); ?>
	
    <style>
        .error-feedback { color: red; font-size: 0.9em; }
		.lbl-required:after { content: " *"; color: red; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">

	<div class="row">
		<div class="col-6 offset-3">
    <div class="card">
        <div class="card-header text-center">
            <h3 id="txtTitle"></h3>
        </div>
        <div class="card-body">
            <form id="authForm">
                <div class="mb-3 regGroup" style="">
                    <label for="name" class="form-label">Name<span class="lbl-required"></span></label>
                    <input type="text" class="form-control" id="name" name="name">
                    <div class="error-feedback" id="nameFeedback"></div>
                </div>
                <div class="mb-3 regGroup">
                    <label for="email" class="form-label">Email<span class="lbl-required"></span></label>
                    <input type="email" class="form-control" id="email" name="email">
                    <div class="error-feedback" id="emailFeedback"></div>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username<span class="lbl-required"></span></label>
                    <input type="text" class="form-control" id="username" name="username">
                    <div class="error-feedback" id="usernameFeedback"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password<span class="lbl-required"></span></label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="error-feedback mt-1" id="passwordFeedback"></div>
                </div>
                <input type="hidden" id="action" name="action" value="signin">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <div class="mt-3 text-center">
                <button class="btn btn-link" id="toggleAction">Switch to Sign Up</button>
            </div>
        </div>
    </div>
	
	</div>
	</div>
	
	
</div>


<script>
$(function(){
    const regGroup = $('.regGroup');
    const toggleBtn = $('#toggleAction');
    const actionInput = $('#action');

    // Toggle sign in/sign up
    toggleBtn.click(function() {
        if (actionInput.val() === 'signin') {
			$('#txtTitle').html('Register');
            actionInput.val('signup');
            regGroup.show();
            toggleBtn.text('Switch to Sign In');
			$('button[type=submit]').html('Register');
			if($('#password').val() != '') {
				$('#password').trigger('input');
			}
        } else {
			$('#txtTitle').html('Sign In');
            actionInput.val('signin');
            regGroup.hide();
            toggleBtn.text('Switch to Sign Up');
			$('button[type=submit]').html('Login');
        }
        clearFeedback();
    });
	

    // On input validation (for real-time UX)
    $('#password').on('input', function() {
		if($('#action').val() != 'signup') return;
		const spanBadgeE = '<span class="border border-secondary text-danger rounded p-1 me-2 mt-1 py-0" style="border-color: #dddddd !important; display:inline-block;">';
		const spanBadgeS = '<span class="border border-secondary text-success rounded p-1 me-2 mt-1 py-0" style="border-color: #dddddd !important; display:inline-block;">';
        const val = $(this).val();
        let feedback = '';
		
        if (val.length < 6) feedback += spanBadgeE; else feedback += spanBadgeS;
		feedback += 'Has at least 6 characters</span>';
		
        if (!/[a-z]/.test(val)) feedback += spanBadgeE; else feedback += spanBadgeS;
		feedback += 'Has at least 1 lowercase letter</span>';
		
        if (!/[A-Z]/.test(val)) feedback += spanBadgeE; else feedback += spanBadgeS;
		feedback += 'Has at least 1 uppercase letter</span>';
		
        if (!/[0-9]/.test(val)) feedback += spanBadgeE; else feedback += spanBadgeS;
		feedback += 'Has at least 1 digit</span>';
		
        if (!/[!@#$%^&*]/.test(val)) feedback += spanBadgeE; else feedback += spanBadgeS;
		feedback += 'Has at least 1 special character</span>';
		
        $('#passwordFeedback').html(feedback);
    });

    function clearFeedback() {
        $('.error-feedback').html('');
    }

    // Handle form submit via AJAX
    $('#authForm').submit(function(e) {
        e.preventDefault();
        clearFeedback();
        const formData = $(this).serialize();
		$('input,button').prop('disabled', true);
		
		if($('#action').val() === 'signin') {
			$('button[type=submit]').html('Submitting...');
		} else if($('#action').val() === 'signup') {
			$('button[type=submit]').html('Submitting...');
		}

        $.post('login_.php', formData, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'error') {
                if (res.errors) {
                    for (let field in res.errors) {
                        $(`#${field}Feedback`).text(res.errors[field]);
                    }
                } else if (res.message) {
                    toastr.error(res.message);
                }
            } else {
                toastr.success(res.message);
				
				if($('#action').val() === 'signup') {
					$('#name, #email, #username, #password').val('');
					$('#action').val('signup');
					$('#toggleAction').trigger('click');
				}
				
                if (res.user) {
                    //alert(`Welcome, ${res.user.name}! Email: ${res.user.email}`);
					$('#username, #password').val('');
					window.location.replace('./home.php');
                }
            }
			$('input,button').prop('disabled', false);
			
			if($('#action').val() === 'signin') {
				$('button[type=submit]').html('Login');
			} else if($('#action').val() === 'signup') {
				$('button[type=submit]').html('Register');
			}
			
        }).always(function(){
			
		});
    });
	
	actionInput.val('signup');
	toggleBtn.trigger('click');
});

</script>

<?php require_once('_footer.php'); ?>

</body>
</html>
