<?php

if (!isset($TEMPLATE) ){
	$TITLE = 'Global Design Values - Signup Page';
	$HEAD = '
		<style>
			#frmSignup ul {margin:0;pading:0;list-style:none;}
			#frmSignup li {margin:8px 0;padding:0;}
			#frmSignup label {
				display:block;
				color:#333333;
				font-weight:bold;
			}
			#frmSignup .help {
				display:block;
				font-weight:normal;
				font-weight:bold;
			}
			#frmSignup .help {
				display:block;
				font-weight:normal;
				font-size:. 8em;
				color:#999999;
			}
			#frmSignup label.disclaimer {
				font-weight:normal;
			}
		</style>
	';
		include 'template.inc.php';
}
?>

<div class="row">
	<div class="two-of-three colomn">
		<h2>Sign Up</h2>
		<form method="post" action="application.php" id="frmSignup">
			<ul>
				<li>
					<label for="email">
						Email Address
						<span class="help">
							Used to contact you if something drastic changes. No spam.
						</span>
						<input style="width:219px" type="text" name="email" id="email"/>
					</label>
				</li>
				<li>
					<label for="subit" class="disclaer">
						By clicking the button below and/or otherwise accessing this
						application I am agreeing to all the <a href="#terms">Terms and
						Condidions</a> that appply.
					</label>
					<br/>
					<button type="submit" name="signup" id="signup">Sign Up!</button>
				</li>
			</ul>
		</form>
	</div>
	<div class="three-of-three colomn" id="terms">
		<h2>Terms and Conditions</h2>
		<?php include 'inc/terms.inc.php'; ?>
	</div>
</div>
