<?php
/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

if ($user->mode == 1):
	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<div class="alert alert-info">
					Версия программы: WebUseOrg3 Lite v<?php echo $cfg->version; ?><br>
					Актуальные версии ПО: <a href="https://github.com/solodyagin/webuseorg3_lite/releases" target="_blank">github.com</a><br>
					Документация: <a href="http://www.грибовы.рф/?page_id=1202" target="_blank">здесь</a>
				</div>
				<form role="form" action="?content_page=config&config=save" method="post" name="form1" target="_self">
					<div class="form-group">
						<label for="form_sitename">Имя сайта</label>
						<input name="form_sitename" type="text" id="form_sitename" value="<?php echo $cfg->sitename; ?>" class="form-control" placeholder="Название сайта..."><br>
						<label for="form_sitename">URL сайта</label>
						<input name="urlsite" type="text" id="urlsite" value="<?php echo $cfg->urlsite; ?>" class="form-control" placeholder="http://где_мой_сайт" size="80"><br>
						<div class="row-fluid">
							<div class="col-xs-4 col-md-4 col-sm-4">
								<span class="help-block">Текущая тема</span>
								<input class="form-control" name="form_cfg_theme" type="text" id="form_cfg_theme" readonly="readonly" value="<?php echo $cfg->theme; ?>">
							</div>
							<div class="col-xs-8 col-md-8 col-sm-8">
								<span class="help-block">Выберите</span>
								<select class="form-control" name="form_cfg_theme_sl" id="form_cfg_theme_sl">
									<?php
									$arr_themes = GetArrayFilesInDir(WUO_ROOT . '/controller/client/themes');
									for ($i = 0; $i < count($arr_themes); $i++) {
										$sl = ($arr_themes[$i] == $cfg->theme) ? 'selected' : '';
										echo "<option value=\"$arr_themes[$i]\" $sl>$arr_themes[$i]</option>";
									}
									?>
								</select>
							</div>
						</div>
					</div>
					</hr>
					<div class="row-fluid">
						<div class="col-xs-4 col-md-4 col-sm-4">
							<span class="help-block">Сервер LDAP:</span>
							<input class="form-control" name="form_cfg_ldap" type="text" id="form_cfg_ldap" value="<?php echo $cfg->ldap; ?>">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form_cfg_ad" value="1" <?php
									if ($cfg->ad == "1") {
										echo "checked";
									}
									?>>Вход через Active Directory
								</label>
							</div>
						</div>
						<div class="col-xs-4 col-md-4 col-sm-4">
							<span class="help-block">Домен 1:</span>
							<input class="form-control" name="form_cfg_domain1" type="text" id="form_cfg_domain1" value="<?php echo $cfg->domain1; ?>">
						</div>
						<div class="col-xs-4 col-md-4 col-sm-4">
							<span class="help-block">Домен 2:</span>
							<input class="form-control" name="form_cfg_domain2" type="text" id="form_cfg_domain2" value="<?php echo $cfg->domain2; ?>">
						</div>
					</div>
					</hr>
					<div class="row-fluid">
						<div class="col-xs-4 col-md-4 col-sm-4">
							<span class="help-block">Сервер SMTP</span>
							<input class="form-control" name="form_smtphost" type="text" id="form_smtphost" value="<?php echo $cfg->smtphost; ?>">
							<span class="help-block">От кого почта:</span>
							<input class="form-control" name="form_emailadmin" type="text" id="form_emailadmin" value="<?php echo $cfg->emailadmin; ?>">
							<span class="help-block">Куда шлем ответы:</span>
							<input class="form-control" name="form_emailreplyto" type="text" id="form_emailreplyto" value="<?php echo $cfg->emailreplyto; ?>">
						</div>
						<div class="col-xs-4 col-md-4 col-sm-4">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form_smtpauth" id="form_smtpauth" value="1"  <?php
									if ($cfg->smtpauth == "1") {
										echo "checked";
									}
									?>>Требуется аутенфикация SMTP
								</label>
							</div>
							<span class="help-block">SMTP имя пользователя:</span>
							<input class="form-control" name="form_smtpusername" type="text" id="form_smtpusername" value="<?php echo $cfg->smtpusername; ?>">
							<span class="help-block">SMTP пароль пользователя:</span>
							<input class="form-control" name="form_smtppass" type="password" id="form_smtppass" value="<?php echo $cfg->smtppass; ?>">
						</div>
						<div class="col-xs-4 col-md-4 col-sm-4">
							<span class="help-block">SMTP порт:</span>
							<input class="form-control" name="form_smtpport" type="text" id="form_smtpport" value="<?php echo $cfg->smtpport; ?>">
							<div class="checkbox">
								<label>
									<input type=checkbox name="form_sendemail" id="form_sendemail" value="1" <?php
									if ($cfg->sendemail == "1") {
										echo "checked";
									}
									?>> Рассылать уведомления
								</label>
							</div>
						</div>
					</div>
					<div align="center">
						<input type="submit" name="Submit" class="btn btn-primary" value="Сохранить изменения">
					</div>
				</form>
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="alert alert-error">
		У вас нет доступа в данный раздел!
	</div>
<?php endif;
