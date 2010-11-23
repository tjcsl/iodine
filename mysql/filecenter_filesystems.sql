DROP TABLE IF EXISTS filecenter_filesystems;
CREATE TABLE filecenter_filesystems (
	name VARCHAR(64) UNIQUE NOT NULL,
	code LONGTEXT NOT NULL
);
INSERT INTO filecenter_filesystems VALUES ("lan","$this->filesystem = new LANFilesystem($_SESSION['i2_username'], $I2_AUTH->get_user_password());$this->template_args['max_file_size'] = 10485760;");
INSERT INTO filecenter_filesystems VALUES ("portfolio","$this->filesystem = new PortfolioFilesystem($_SESSION['i2_username'], $I2_AUTH->get_user_password());$this->template_args['max_file_size'] = 10485760;");
INSERT INTO filecenter_filesystems VALUES ("csl","$this->filesystem = new CSLProxy($_SESSION['csl_username'], $_SESSION['csl_password']);if (!$this->filesystem->is_valid()) {$this->template = 'csl_login.tpl';$return=array('Filecenter','CSL Authentication');}$this->template_args['max_file_size'] = 20971520;");
INSERT INTO filecenter_filesystems VALUES ("main","$this->filesystem = new CSLProxy($_SESSION['i2_username'], $I2_AUTH->get_user_password(),$I2_AUTH->get_realm());$this->template_args['max_file_size'] = 20971520;");
INSERT INTO filecenter_filesystems VALUES ("bookmarks","$this->filesystem='bookmarks';return array('Filecenter','Filecenter bookmarks');");
INSERT INTO filecenter_filesystems VALUES ("local","$this->filesystem = new CIFS($_SESSION['i2_username'],$I2_AUTH->get_user_password(),$I2_USER->gradename.'/'.$I2_USER->username);$this->template_args['max_file_size'] = 10485760;");
