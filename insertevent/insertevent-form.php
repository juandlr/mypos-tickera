<div class='insertevent-form'>
	<form action="" method="post" enctype="multipart/form-data">
		<div class='row'>
			<div class='col-md-6'>
				<p>
					<input placeholder='Event Title' required id='event-title' class="form-control input-lg" name='event_title' type='text' />
				</p>
				<p>
					<input required id='event-name' placeholder='Your Name' class="form-control input-lg" name='event_name' type='text' />
				</p>
				<p>
					<input required id='event-email' placeholder='Your Email' class="form-control input-lg" name='event_email' type='email' />
				</p>

				<p>
					<textarea name='event_body' placeholder='Event Details' class="form-control input-lg" rows="7" cols="6"></textarea>
				</p>
				<p>
					<input id='event-address' placeholder='Address' class="form-control input-lg" name='event_address' type='text' />
				</p>
				<p>
					<input id='event-phone' placeholder='Phone' class="form-control input-lg" name='event_phone' type='text' />
				</p>
			</div>
			<div class='col-sm-6'>
				<p>
					<input name='start_date' placeholder='Start Date' class="form-control input-lg" id='start-date' type="date" />
				</p>
				<p>
					<input name='end_date' placeholder='End Date' class="form-control input-lg" id='end-date' type="date" />
				
				
				<p>
					<input name='start_time' placeholder='Start Time' class="form-control input-lg" id='start-time' type="time" />
				</p>
				<p>
					<input name='end_time' placeholder='End Time' class="form-control input-lg" id='end-time' type="time" />
				</p>
				<p>
					<input type='text' placeholder='Event Tags' class="form-control input-lg" name='event_tags' />
				</p>
				<p>
					<input type='text' placeholder='Event Video' class="form-control input-lg" name='event_video' />
				</p>
				<p>
					<label>Image</label> <input type='file' name='event_image' id='event_image' />
				</p>
				<p>
					<label for="message_human">Human Verification: <span>*</span> <br>
					<input type="text" style="width: 60px;" name="message_human"> + 3 = 5
					</label>
				</p>
			</div>
		</div>
		<?php wp_nonce_field('insertevent-nonce', 'insertevent-nonce', false); ?>
		<input value='Submit Event' class='btn btn-primary btn-lg' type='submit' name='event_submitted' />
		<?php global $success_message, $error_message; ?>
		<?php if (isset($success_message)) : ?>
		<div style='margin-top: 20px' class="alert alert-success"><?php print $success_message; ?></div>
		<?php endif; ?>
		<?php if (isset($error_message)) : ?>
	    <div style='margin-top: 20px' class="alert alert-danger"><?php print $error_message; ?></div>
	    <?php endif; ?>
	</form>
</div>