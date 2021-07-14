<?php require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
?>
 <div class="container">
    <div class="row">
       <div class="col-md-6 col-md-offset-3">
            <div class="login-banner text-center">
                <h1><img src="../img/logo.png" alt=""></h1>
            </div>
            <div class="portlet portlet-blue">
                <div class="portlet-heading login-heading">
                    <div class="portlet-title">
                        <h4><strong><?php  echo $dictionary->GetValue("change_password");?> </strong>
                        </h4>
                        <h5><?php echo $dictionary->GetValue("change_password_message");?></h5>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="portlet-body ">
					<form id="form1" action="code/user.change.password.code.php" method="post">
						<?php
							if(isset($LOGIN_MESSAGE) && isset($LOGIN_STATUS)) {
								print "<div class='alert-danger '>".$dictionary->GetValue($LOGIN_MESSAGE)."</div><br>";
								unset($LOGIN_MESSAGE, $USER_STATUS);
							}

                            echo "<strong><i class='icon-warning-sign'></i> ".$dictionary->GetValue("password_rules")."<br/><br/>";
				        ?>
           		        <div class="clearfix"></div>
                        <fieldset>
                            <div class="form-group">
                                <label for="old_password"><?php print $dictionary->GetValue("old_password"); ?></label>
                                <input type="password" id="old_password" name="old_password" value="" placeholder="<?php print $dictionary->GetValue("old_password"); ?>" class="form-control"/>
                                <input type="hidden" id="name" name="name" value="<?php echo $USERNAME; ?>"  />
                            </div> <!-- /password -->
        					<div class="form-group">
                                <label for="password"><?php print $dictionary->GetValue("Password"); ?></label>
                                <input type="password" id="password" name="password" value="" placeholder="<?php print $dictionary->GetValue("Password"); ?>" class="form-control"/>
                            </div> <!-- /password -->
                            <div class="form-group">
                                <label for="confirm_password"><?php print $dictionary->GetValue("Confirm_Password"); ?></label>
                                <input type="password" id="confirm_password" name="confirm_password" value="" placeholder="<?php print $dictionary->GetValue("Confirm_Password"); ?>" class="form-control"/>
                            </div> <!-- /password -->
                            <div class="login-actions">
                                 <input type="submit" value="<?php print $dictionary->GetValue("change_password"); ?>" class="btn btn-lg btn-blue btn-block btn-success">
                            </div> <!-- .actions -->
                        </fieldset>
		            </form>
	            </div>
            </div>
        </div>
    </div>
</div>
