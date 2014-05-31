<?php
include_once dirname(__FILE__).'/db.php';

/**
 * Return the sql string to create the first adminuser (and some other important stuff)
 * @param <String> $username username of the adminuser
 * @param <String> $name the displayed name of the adminuser
 * @param <String> $email the email of the adminuser
 * @param <String> $password the password of the adminuser (plaintext)
 * @return string SQL Query to create the Adminuser
 */
function createAdminSql ($username, $name, $email, $password) {
	$db = new DB();
	$salt = $db->createSalt();
	$password = $password . $salt;
	return "
		INSERT INTO `user` (`username`, `email`, `prename`, `password`, 
			`salt`, `active`, `meta_creatorID`, `meta_createDate`, 
			`meta_changeUserID`, `meta_changeDate`) 
		   VALUES ('$username', '$email', '$name', SHA2('$password', 256), '$salt', '1', '1', NOW(), '1', NOW());
		
		INSERT INTO `group` (`name`, `active`, `meta_creator_id`, `meta_creatDate`) 
		   VALUES ('global-admin', '1', '1', NOW());
		
		INSERT INTO `setting` (`key`, `value`) VALUES ('global.admingroup', '1');
		INSERT INTO `setting` (`key`, `value`) VALUES ('global.loglevel', 'ERROR');
		INSERT INTO `setting` (`key`, `value`) VALUES ('global.name', 'Bugtracker');
		INSERT INTO `setting` (`key`, `value`) VALUES ('global.tracker', '');
		INSERT INTO `setting` (`key`, `value`) VALUES ('main.motd', '');
		
		INSERT INTO `log` (`message`, `date`, `level`, `user`) 
		   VALUES ('Platform Setup Successfull', NOW(), 'INFO', 'SETUP');
		
		INSERT INTO `status` (`name`, `color`, `isDone`, `active`) VALUES ('New', '#2e8fcd', '0', '1');
		INSERT INTO `status` (`name`, `color`, `isDone`, `active`) VALUES ('Progress', '#ffc000', '0', '1');
		INSERT INTO `status` (`name`, `color`, `isDone`, `active`) VALUES ('Fixed', '#2fbd47', '1', '1');
		INSERT INTO `status` (`name`, `color`, `isDone`, `active`) VALUES ('Not Fixed', '#a3a3a3', '1', '1');

		INSERT INTO `tasktype` (`name`) VALUES ('Bug');
		INSERT INTO `tasktype` (`name`) VALUES ('New Feature');
		INSERT INTO `tasktype` (`name`) VALUES ('Task');
		INSERT INTO `tasktype` (`name`) VALUES ('Improvement');
		INSERT INTO `tasktype` (`name`) VALUES ('Suggestion');
		
		INSERT INTO `user_group` (`user_id`, `group_id`) 
		   VALUES ('1', '1');

			";
}

// SQL Query to create the structure of the database (all tables)
$sqlToCreateDb = '
-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `email` VARCHAR(100) NULL,
  `prename` VARCHAR(60) NULL,
  `surname` VARCHAR(60) NULL,
  `password` VARCHAR(200) NULL,
  `salt` VARCHAR(30) NULL,
  `active` INT NULL,
  `meta_creatorID` INT NULL,
  `meta_createDate` DATE NULL,
  `meta_changeUserID` INT NULL,
  `meta_changeDate` DATE NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `status` ;

CREATE TABLE IF NOT EXISTS `status` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `color` VARCHAR(20) NULL,
  `isDone` INT NOT NULL,
  `active` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `group` ;

CREATE TABLE IF NOT EXISTS `group` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `active` INT NULL,
  `meta_creator_id` INT NULL,
  `meta_creatDate` DATE NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `project` ;

CREATE TABLE IF NOT EXISTS `project` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `key` VARCHAR(10) NOT NULL,
  `description` TEXT NULL,
  `group_admin` INT NOT NULL,
  `group_write` INT NOT NULL,
  `group_read` INT NOT NULL,
  `version` VARCHAR(45) NULL,
  `active` INT NULL,
  `meta_creatorID` INT NULL,
  `meta_createDate` DATE NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_project_group1_idx` (`group_admin` ASC),
  INDEX `fk_project_group2_idx` (`group_write` ASC),
  INDEX `fk_project_group3_idx` (`group_read` ASC),
  CONSTRAINT `fk_project_group1`
    FOREIGN KEY (`group_admin`)
    REFERENCES `group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_project_group2`
    FOREIGN KEY (`group_write`)
    REFERENCES `group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_project_group3`
    FOREIGN KEY (`group_read`)
    REFERENCES `group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tasktype`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tasktype` ;

CREATE TABLE IF NOT EXISTS `tasktype` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `version`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `version` ;

CREATE TABLE IF NOT EXISTS `version` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `isReleased` INT NOT NULL,
  `doneDate` DATE NULL,
  `description` TEXT NULL,
  `project_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_version_project1_idx` (`project_id` ASC),
  CONSTRAINT `fk_version_project1`
    FOREIGN KEY (`project_id`)
    REFERENCES `project` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `component`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `component` ;

CREATE TABLE IF NOT EXISTS `component` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `project_id` INT NOT NULL,
  `active` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_component_project1_idx` (`project_id` ASC),
  CONSTRAINT `fk_component_project1`
    FOREIGN KEY (`project_id`)
    REFERENCES `project` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `task`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `task` ;

CREATE TABLE IF NOT EXISTS `task` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `summary` VARCHAR(150) NULL,
  `description` TEXT NULL,
  `status_id` INT NOT NULL,
  `project_id` INT NOT NULL,
  `creator_id` INT NOT NULL,
  `assignee_id` INT NULL,
  `createDate` DATE NULL,
  `tasktype_id` INT NOT NULL,
  `priority` INT NULL,
  `active` INT NULL,
  `version_id` INT NULL,
  `component_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_task_status_idx` (`status_id` ASC),
  INDEX `fk_task_project1_idx` (`project_id` ASC),
  INDEX `fk_task_user1_idx` (`creator_id` ASC),
  INDEX `fk_task_user2_idx` (`assignee_id` ASC),
  INDEX `fk_task_tasktype1_idx` (`tasktype_id` ASC),
  INDEX `fk_task_version1_idx` (`version_id` ASC),
  INDEX `fk_task_component1_idx` (`component_id` ASC),
  CONSTRAINT `fk_task_status`
    FOREIGN KEY (`status_id`)
    REFERENCES `status` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_project1`
    FOREIGN KEY (`project_id`)
    REFERENCES `project` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_user1`
    FOREIGN KEY (`creator_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_user2`
    FOREIGN KEY (`assignee_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_tasktype1`
    FOREIGN KEY (`tasktype_id`)
    REFERENCES `tasktype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_version1`
    FOREIGN KEY (`version_id`)
    REFERENCES `version` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_component1`
    FOREIGN KEY (`component_id`)
    REFERENCES `component` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_group` ;

CREATE TABLE IF NOT EXISTS `user_group` (
  `user_id` INT NOT NULL,
  `group_id` INT NOT NULL,
  INDEX `fk_user_group_user1_idx` (`user_id` ASC),
  INDEX `fk_user_group_group1_idx` (`group_id` ASC),
  PRIMARY KEY (`user_id`, `group_id`),
  CONSTRAINT `fk_user_group_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_group_group1`
    FOREIGN KEY (`group_id`)
    REFERENCES `group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `group_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `group_group` ;

CREATE TABLE IF NOT EXISTS `group_group` (
  `group_child` INT NOT NULL,
  `group_parent` INT NOT NULL,
  INDEX `fk_table1_group1_idx` (`group_child` ASC),
  INDEX `fk_table1_group2_idx` (`group_parent` ASC),
  PRIMARY KEY (`group_child`, `group_parent`),
  CONSTRAINT `fk_table1_group1`
    FOREIGN KEY (`group_child`)
    REFERENCES `group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_group2`
    FOREIGN KEY (`group_parent`)
    REFERENCES `group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `comment` ;

CREATE TABLE IF NOT EXISTS `comment` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `date` DATE NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_changelog_task1_idx` (`task_id` ASC),
  INDEX `fk_changelog_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_changelog_task1`
    FOREIGN KEY (`task_id`)
    REFERENCES `task` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_changelog_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `setting`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `setting` ;

CREATE TABLE IF NOT EXISTS `setting` (
  `key` VARCHAR(50) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`key`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `log` ;

CREATE TABLE IF NOT EXISTS `log` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `message` TEXT NOT NULL,
  `exception` TEXT NULL,
  `date` DATETIME NOT NULL,
  `level` VARCHAR(15) NOT NULL,
  `user` VARCHAR(100) NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB;   ';