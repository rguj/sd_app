<?php
require_once('_autoload.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    
	<?php require_once('_head_asset.php'); ?>
	
    <style>
        .error-feedback { color: red; font-size: 0.9em; }
		.lbl-required:after { content: " *"; color: red; }
		
		#strength-bar {
		  transition: width 0.3s, background-color 0.3s;
		}
		#strength-label {
		  margin-top: 2px;
		  font-size: 0.7rem;
		}
		
		.progress {
			height: 5px;
		}
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">

	<div class="row mb-4">
		<div class="col-4 offset-4 text-center">
			<a class="btn btn-link text-primary" href="./" style="font-size:20px; font-weight:600; text-decoration: none;">MyApp</a>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-6 col-md-6 offset-lg-3 offset-md-3">
    <div class="card">
        <div class="card-header text-center">
            <h3 id="txtTitle"></h3>
        </div>
        <div class="card-body">
            <form id="authForm">
                <div class="mb-3 regGroup" style="">
                    <label for="name" class="form-label mb-0">Name<span class="lbl-required"></span></label>
                    <div class="input-group">
					    <span class="input-group-text"><i class="bi bi-person"></i></span>
						<input type="text" class="form-control" name="name" id="name" placeholder="Enter name" autofocus />
					</div>
                    <div class="error-feedback" id="nameFeedback"></div>
                </div>
                <div class="mb-3 regGroup">
                    <label for="email" class="form-label mb-0">Email<span class="lbl-required"></span></label>
                    <div class="input-group">
					    <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
						<input type="email" class="form-control" name="email" id="email" placeholder="Enter email" autofocus />
					</div>
                    <div class="error-feedback" id="emailFeedback"></div>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label mb-0">Username<span class="lbl-required"></span></label>
                    <div class="input-group">
					    <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
						<input type="text" class="form-control" name="username" id="username" placeholder="Enter username" autofocus />
					</div>
                    <div class="error-feedback" id="usernameFeedback"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label mb-0">Password<span class="lbl-required"></span></label>
                    <div class="input-group">
					    <span class="input-group-text"><i class="bi bi-lock"></i></span>
						<input type="password" class="form-control" name="password" id="password" placeholder="Enter password" autofocus />
						<span class="input-group-text" id="toggle-password" style="cursor: pointer;">
							<i class="bi bi-eye" id="toggle-icon"></i>
					    </span>
					</div>
					<div class="d-none" id="div-password-strength">
						<div id="strength-label" class="text-muted">Password Required</div>
						<div class="progress mt-1">
							<div id="strength-bar" class="progress-bar" role="progressbar"></div>
						</div>
						<div class="error-feedback mt-1" id="passwordFeedback"></div>
					</div>
					
					
                </div>
                <input type="hidden" id="action" name="action" value="signin">
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <div class="mt-3 text-center">
                <button class="btn btn-link" id="toggleAction" style="text-decoration:none;">Switch to Sign Up</button>
            </div>
        </div>
    </div>
	
	</div>
	</div>
	
	
</div>


<script>


const regGroup = $('.regGroup');
const toggleBtn = $('#toggleAction');
const actionInput = $('#action');


const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('strength-bar');
const strengthLabel = document.getElementById('strength-label');

function updateStrengthBar(score) {
  const widths = ['100%', '20%', '40%', '60%', '80%', '100%'];
  const colors = ['bg-danger', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-success'];
  const labels = ['Password Required', 'Very Weak', 'Weak', 'Moderate', 'Strong', 'Very Strong'];

  strengthBar.style.width = widths[score];
  strengthBar.className = `progress-bar ${colors[score]}`;
  strengthLabel.textContent = labels[score];
}



function clearFeedback() {
	$('.error-feedback').html('');
}


$(function(){

    // Toggle sign in/sign up
    toggleBtn.click(function() {
        if (actionInput.val() === 'signin') {
			$('#txtTitle').html('Sign Up');
            actionInput.val('signup');
            regGroup.show();
            toggleBtn.text('Switch to Sign In');
			$('button[type=submit]').html('Sign Up');
			if($('#password').val() != '') {
				$('#password').trigger('input');
			}
			$('#div-password-strength').removeClass('d-none');
        } else {
			$('#txtTitle').html('Sign In');
            actionInput.val('signin');
            regGroup.hide();
            toggleBtn.text('Switch to Sign Up');
			$('button[type=submit]').html('Sign In');
			$('#div-password-strength').addClass('d-none');
        }
        clearFeedback();
    });
	
	$(toggleBtn).on('click', function() {
		if(actionInput.val() == 'signup') {
			$('#password').trigger('input');
		}
	});
	

    // On input validation (for real-time UX)
    $('#password').on('input', function() {
		if($('#action').val() != 'signup') return;
		const spanBadgeE = '<span class="border border-secondary text-danger rounded p-1 me-2 mt-1 py-0" style="border-color: #dddddd !important; display:inline-block;">';
		const spanBadgeS = '<span class="border border-secondary text-success rounded p-1 me-2 mt-1 py-0" style="border-color: #dddddd !important; display:inline-block;">';
        const val = $(this).val();
        let feedback = '';
		let score = 0;
		
        if (val.length < 6) { feedback += spanBadgeE; } else { feedback += spanBadgeS; score++; }
		feedback += 'Has at least 6 characters</span>';
		
        if (!/[a-z]/.test(val)) { feedback += spanBadgeE; } else { feedback += spanBadgeS; score++; }
		feedback += 'Has at least 1 lowercase letter</span>';
		
        if (!/[A-Z]/.test(val)) { feedback += spanBadgeE; } else { feedback += spanBadgeS; score++; }
		feedback += 'Has at least 1 uppercase letter</span>';
		
        if (!/[0-9]/.test(val)) { feedback += spanBadgeE; } else { feedback += spanBadgeS; score++; }
		feedback += 'Has at least 1 digit</span>';
		
        if (!/[!@#$%^&*]/.test(val)) { feedback += spanBadgeE; } else { feedback += spanBadgeS; score++; }
		feedback += 'Has at least 1 special character</span>';
		
		updateStrengthBar(score);
		
        $('#passwordFeedback').html(feedback);
    });

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

        $.post('<?= PAGE_CURRENT_FILENAME ?>_.php', formData, function(response, e, j) {
			log(response)
			log(e)
			log(j)
            const res = JSON.parse(response);
			
            if (res.status === 'error') {
                if (res.errors) {
					toastr.error('Validation failed. Please resolve the fields.');
                    for (let field in res.errors) {
                        $(`#${field}Feedback`).text(res.errors[field]);
						let input = $('#'+field);
                        input.addClass('is-invalid');
                        input.next('.error-feedback').text(res.errors[field]);
                    }
                } else if (res.message) {
                    toastr.error(res.message);
                }
				
				if($('#action').val() === 'signin') {
					$('button[type=submit]').html('Sign Up');
				} else if($('#action').val() === 'signup') {
					$('button[type=submit]').html('Sign In');
				}
				$('input,button').prop('disabled', false);
				return;
            } else {
                toastr.success(res.message);
				
				if($('#action').val() === 'signup') {
					$('#name, #email, #username, #password').val('');
					$('#action').val('signup');
					$('#toggleAction').trigger('click');
				}
				
                if (res.user) {
					$('#username, #password').val('');
					window.location.replace('./home.php');
                }
            }
			$('input,button').prop('disabled', false);
			
			if($('#action').val() === 'signin') {
				$('button[type=submit]').html('Sign In');
			} else if($('#action').val() === 'signup') {
				$('button[type=submit]').html('Sign Up');
			}
			
        }).always(function(){
			
		});
    });
	
	actionInput.val('signup');
	toggleBtn.trigger('click');
	
	
	$('#toggle-password').on('click', () => {
		const isPassword = $('#password').attr('type') == 'password';
		
		$('#password').attr('type', isPassword ? 'text' : 'password');
		if($('#password').attr('type') == 'password') {
			$('#toggle-icon').removeClass('bi-eye-slash i-eye').addClass('bi-eye-slash');
		} else {
			$('#toggle-icon').removeClass('bi-eye-slash bi-eye').addClass('bi-eye');
		}
		
    });
	
	function clearFormErrors() {
        $('#studentForm input').removeClass('is-invalid');
        $('#authForm .error-feedback').text('');
    }

    $('#authForm input').on('input', function () {
		if($('#action').val() === 'signup') {
			$(this).removeClass('is-invalid');
			if($(this).attr('id') !== 'password') {
				$(this).next('.error-feedback').text('');
			}
		}
    });
	
	
	
	
});

</script>

<?php require_once('_footer.php'); ?>

</body>
</html>
