<?php global $plugin_settings; ?>
<div class="row">
	<div class="col-md-offset-3 col-md-6 col-xs-12">
		<div class="row mona-heremap-section">
			<div class="col-md-12 mona-heremap-section-header">
				<h4><?php echo __('API credentials'); ?></h4>
			</div>
			<form class="col-md-12 mona-heremap-section-content" method="POST" autocomplete="off">
				<div class="row">
					<div class="col-md-12">
						<div class="row form-group">
							<label class="col-md-2 form-label"><?php echo __('App ID'); ?></label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="input-api-key" name="api_key" value="<?php echo $plugin_settings['api_key']; ?>" required />
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="row form-group">
							<label class="col-md-2 form-label"><?php echo __('App Code'); ?></label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="input-api-secret" name="api_secret" value="<?php echo $plugin_settings['api_secret']; ?>" required />
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="row form-group">
							<label class="col-md-2 form-label"><?php echo __('SSL'); ?></label>
							<div class="col-md-10">
								<select class="form-control" id="input-api-ssl" name="is_ssl" required>
									<option value="0" <?php echo ($plugin_settings['is_ssl'] == 0) ? 'selected' : ''; ?>>Disabled</option>
									<option value="1" <?php echo ($plugin_settings['is_ssl'] == 1) ? 'selected' : ''; ?>>Enabled</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<input type="hidden" name="action" value="api" />
						<button class="btn btn-primary" type="submit"><?php echo __('Submit'); ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>