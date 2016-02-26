SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `ussap` DEFAULT CHARACTER SET latin1 ;
USE `ussap` ;

-- -----------------------------------------------------
-- Table `ussap`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`user` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_email` TEXT NULL DEFAULT NULL ,
  `password` CHAR(128) NULL DEFAULT NULL ,
  `salt` CHAR(128) NULL DEFAULT NULL ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `user_status` BINARY(1) NULL DEFAULT '1' ,
  `token` CHAR(128) NULL DEFAULT NULL ,
  `series_identifier` CHAR(128) NOT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`bookmarks`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`bookmarks` (
  `bookmark_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `user_id` INT(11) NOT NULL ,
  `bookmark_type` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`bookmark_id`) ,
  INDEX `fk_Bookmarks_Users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_Bookmarks_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`friends`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`friends` (
  `friend_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`friend_id`) ,
  INDEX `fk_Friends_Users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_Friends_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`chat`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`chat` (
  `chat_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `friend_id` INT(11) NOT NULL ,
  PRIMARY KEY (`chat_id`) ,
  INDEX `fk_Conversation_Friends1_idx` (`friend_id` ASC) ,
  CONSTRAINT `fk_Conversation_Friends1`
    FOREIGN KEY (`friend_id` )
    REFERENCES `ussap`.`friends` (`friend_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`notification`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`notification` (
  `notification_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `notification_type` VARCHAR(45) NOT NULL ,
  `created_at` TIMESTAMP NULL DEFAULT NULL ,
  `perp` INT(11) NOT NULL ,
  `perp_object_type` VARCHAR(100) NULL DEFAULT NULL ,
  `notification_text` VARCHAR(180) NULL DEFAULT NULL ,
  `perp_object_id` INT(11) NULL DEFAULT NULL ,
  `status` BINARY(1) NOT NULL DEFAULT '0' ,
  `magic_id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`notification_id`) ,
  INDEX `perp_index` (`perp` ASC) ,
  INDEX `magic_id_index` (`magic_id` ASC) ,
  CONSTRAINT `notification_ibfk_1`
    FOREIGN KEY (`perp` )
    REFERENCES `ussap`.`user` (`user_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 27
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`chat_notification`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`chat_notification` (
  `chat_notification_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `target_id` INT(11) NOT NULL ,
  `chat_id` INT(11) NOT NULL ,
  `notification_id` INT(11) NOT NULL ,
  `target_object_type` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`chat_notification_id`) ,
  INDEX `notification_id_index` (`notification_id` ASC) ,
  INDEX `chat_id_index` (`chat_id` ASC) ,
  CONSTRAINT `chat_notification_ibfk_1`
    FOREIGN KEY (`notification_id` )
    REFERENCES `ussap`.`notification` (`notification_id` ),
  CONSTRAINT `chat_notification_ibfk_2`
    FOREIGN KEY (`chat_id` )
    REFERENCES `ussap`.`chat` (`chat_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`conversation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`conversation` (
  `conversation_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `message` TEXT NOT NULL ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `chat_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `status` BINARY(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`conversation_id`) ,
  INDEX `fk_Conversation_reply_Conversation1_idx` (`chat_id` ASC) ,
  INDEX `fk_Conversation_reply_Users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_Conversation_reply_Conversation1`
    FOREIGN KEY (`chat_id` )
    REFERENCES `ussap`.`chat` (`chat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Conversation_reply_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`department`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`department` (
  `department_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `department_name` VARCHAR(45) NOT NULL ,
  `department_type` VARCHAR(45) NOT NULL ,
  `department_head` VARCHAR(45) NOT NULL ,
  `department_info` LONGTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`department_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`forum`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`forum` (
  `forum_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(155) NOT NULL ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `forum_type` VARCHAR(45) NOT NULL ,
  `magic_id` VARCHAR(45) NOT NULL ,
  `flag` INT(11) NULL DEFAULT '0' ,
  PRIMARY KEY (`forum_id`) ,
  UNIQUE INDEX `unique_magic_id` (`magic_id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`department_forum`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`department_forum` (
  `department_forum_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `forum_topic` TEXT NOT NULL ,
  `forum_id` INT(11) NOT NULL ,
  `department_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`department_forum_id`) ,
  INDEX `fk_department_forum_forum1_idx` (`forum_id` ASC) ,
  INDEX `fk_department_forum_department1_idx` (`department_id` ASC) ,
  INDEX `user_id_index` (`user_id` ASC) ,
  CONSTRAINT `department_forum_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` ),
  CONSTRAINT `fk_department_forum_department1`
    FOREIGN KEY (`department_id` )
    REFERENCES `ussap`.`department` (`department_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_forum_forum1`
    FOREIGN KEY (`forum_id` )
    REFERENCES `ussap`.`forum` (`forum_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`department_notification`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`department_notification` (
  `department_notification_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `notification_id` INT(11) NOT NULL ,
  `department_id` INT(11) NOT NULL ,
  `target_object_magic_id` VARCHAR(45) NULL DEFAULT NULL ,
  `target_object_type` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`department_notification_id`) ,
  INDEX `fk_department_notification_Notification1_idx` (`notification_id` ASC) ,
  INDEX `fk_department_notification_Department1_idx` (`department_id` ASC) ,
  CONSTRAINT `fk_department_notification_Department1`
    FOREIGN KEY (`department_id` )
    REFERENCES `ussap`.`department` (`department_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_notification_Notification1`
    FOREIGN KEY (`notification_id` )
    REFERENCES `ussap`.`notification` (`notification_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`posts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`posts` (
  `post_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `flag` INT(11) NULL DEFAULT '0' ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `pic_url` LONGTEXT NULL DEFAULT NULL ,
  `content_type` VARCHAR(20) NULL DEFAULT NULL ,
  `post_text` LONGTEXT NULL DEFAULT NULL ,
  `post_type` VARCHAR(45) NOT NULL ,
  `magic_id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`post_id`) ,
  UNIQUE INDEX `unique_magic_id` (`magic_id` ASC) ,
  INDEX `magic_id_index` (`magic_id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 70
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`department_post`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`department_post` (
  `department_post_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `post_id` INT(11) NOT NULL ,
  `department_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`department_post_id`) ,
  INDEX `post_id_index` (`post_id` ASC) ,
  INDEX `department_id_index` (`department_id` ASC) ,
  INDEX `user_id_index` (`user_id` ASC) ,
  CONSTRAINT `department_post_ibfk_1`
    FOREIGN KEY (`post_id` )
    REFERENCES `ussap`.`posts` (`post_id` ),
  CONSTRAINT `department_post_ibfk_2`
    FOREIGN KEY (`department_id` )
    REFERENCES `ussap`.`department` (`department_id` ),
  CONSTRAINT `department_post_ibfk_3`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`uploads`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`uploads` (
  `upload_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `upload_url` TEXT NULL DEFAULT NULL ,
  `created_at` DATETIME NOT NULL ,
  `size` INT(11) NOT NULL ,
  `file_type` VARCHAR(120) NOT NULL ,
  `file_name` TEXT NOT NULL ,
  `magic_id` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`upload_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`department_upload`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`department_upload` (
  `department_upload_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `upload_id` INT(11) NOT NULL ,
  `department_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`department_upload_id`) ,
  INDEX `fk_department_upload_Uploads1_idx` (`upload_id` ASC) ,
  INDEX `fk_department_upload_department1_idx` (`department_id` ASC) ,
  CONSTRAINT `fk_department_upload_department1`
    FOREIGN KEY (`department_id` )
    REFERENCES `ussap`.`department` (`department_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_upload_Uploads1`
    FOREIGN KEY (`upload_id` )
    REFERENCES `ussap`.`uploads` (`upload_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`forum_bookmark`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`forum_bookmark` (
  `forum_bookmark_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `forum_magic_id` VARCHAR(50) NULL DEFAULT NULL ,
  `bookmark_id` INT(11) NOT NULL ,
  PRIMARY KEY (`forum_bookmark_id`) ,
  INDEX `fk_forum_bookmark_Forum1_idx` (`forum_magic_id` ASC) ,
  INDEX `fk_forum_bookmark_bookmarks1_idx` (`bookmark_id` ASC) ,
  CONSTRAINT `fk_forum_bookmark_bookmarks1`
    FOREIGN KEY (`bookmark_id` )
    REFERENCES `ussap`.`bookmarks` (`bookmark_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`forum_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`forum_comments` (
  `forum_comment_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `content` TEXT NOT NULL ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `forum_id` INT(11) NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `magic_id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`forum_comment_id`) ,
  INDEX `fk_Forum_comments_Users1_idx` (`user_id` ASC) ,
  INDEX `forum_id_index` (`forum_id` ASC) ,
  CONSTRAINT `fk_Forum_comments_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `forum_comments_ibfk_1`
    FOREIGN KEY (`forum_id` )
    REFERENCES `ussap`.`forum` (`forum_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`forum_comment_smiley`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`forum_comment_smiley` (
  `forum_comment_smiley_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `forum_comment_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`forum_comment_smiley_id`) ,
  INDEX `fk_Forum_comment_smiley_Forum_comments1_idx` (`forum_comment_id` ASC) ,
  INDEX `fk_Forum_comment_smiley_user1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_Forum_comment_smiley_Forum_comments1`
    FOREIGN KEY (`forum_comment_id` )
    REFERENCES `ussap`.`forum_comments` (`forum_comment_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Forum_comment_smiley_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`forum_privacy`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`forum_privacy` (
  `forum_privacy_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `target` VARCHAR(45) NOT NULL ,
  `forum_id` INT(11) NOT NULL ,
  `target_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`forum_privacy_id`) ,
  INDEX `fk_Forum_privacy_Forum1_idx` (`forum_id` ASC) ,
  CONSTRAINT `fk_Forum_privacy_Forum1`
    FOREIGN KEY (`forum_id` )
    REFERENCES `ussap`.`forum` (`forum_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`friend_list`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`friend_list` (
  `friend_list_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_friend_id` INT(11) NULL DEFAULT NULL ,
  `friend_id1` INT(11) NOT NULL ,
  PRIMARY KEY (`friend_list_id`) ,
  INDEX `fk_Friend_list_Friends1_idx` (`user_friend_id` ASC) ,
  INDEX `fk_Friend_list_Friends2_idx` (`friend_id1` ASC) ,
  CONSTRAINT `friend_list_ibfk_1`
    FOREIGN KEY (`user_friend_id` )
    REFERENCES `ussap`.`friends` (`friend_id` ),
  CONSTRAINT `friend_list_ibfk_2`
    FOREIGN KEY (`friend_id1` )
    REFERENCES `ussap`.`friends` (`friend_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`friend_request`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`friend_request` (
  `friend_request_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `target_id` INT(11) NOT NULL ,
  `perp_id` INT(11) NOT NULL ,
  `magic_id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`friend_request_id`) ,
  UNIQUE INDEX `unique_magic_id` (`magic_id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`groups` (
  `group_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `group_name` VARCHAR(45) NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `admin` INT(11) NOT NULL ,
  `group_description` LONGTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`group_id`) ,
  INDEX `fk_Groups_Users1_idx` (`admin` ASC) ,
  CONSTRAINT `fk_Groups_Users1`
    FOREIGN KEY (`admin` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_members`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_members` (
  `group_member_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `group_member_status` BINARY(1) NULL DEFAULT '0' ,
  `group_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`group_member_id`) ,
  INDEX `fk_Group_members_Groups1_idx` (`group_id` ASC) ,
  INDEX `fk_Group_members_Users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_Group_members_Groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `ussap`.`groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_members_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_chat`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_chat` (
  `group_chat_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `chat_id` INT(11) NOT NULL ,
  `group_id` INT(11) NOT NULL ,
  `group_member_id` INT(11) NOT NULL ,
  PRIMARY KEY (`group_chat_id`) ,
  INDEX `fk_group_chat_chat1_idx` (`chat_id` ASC) ,
  INDEX `fk_group_chat_groups1_idx` (`group_id` ASC) ,
  INDEX `fk_group_chat_group_members1_idx` (`group_member_id` ASC) ,
  CONSTRAINT `fk_group_chat_chat1`
    FOREIGN KEY (`chat_id` )
    REFERENCES `ussap`.`chat` (`chat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_chat_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `ussap`.`groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_chat_group_members1`
    FOREIGN KEY (`group_member_id` )
    REFERENCES `ussap`.`group_members` (`group_member_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_conversation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_conversation` (
  `group_conversation_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `message` TEXT NOT NULL ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `group_chat_id` INT(11) NOT NULL ,
  `group_member_id` INT(11) NOT NULL ,
  PRIMARY KEY (`group_conversation_id`) ,
  INDEX `fk_group_conversation_group_chat1_idx` (`group_chat_id` ASC) ,
  INDEX `fk_group_conversation_group_members1_idx` (`group_member_id` ASC) ,
  CONSTRAINT `fk_group_conversation_group_chat1`
    FOREIGN KEY (`group_chat_id` )
    REFERENCES `ussap`.`group_chat` (`group_chat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_conversation_group_members1`
    FOREIGN KEY (`group_member_id` )
    REFERENCES `ussap`.`group_members` (`group_member_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_forum`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_forum` (
  `group_forum_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `forum_topic` VARCHAR(250) NOT NULL ,
  `forum_id` INT(11) NOT NULL ,
  `group_id` INT(11) NOT NULL ,
  `group_member_id` INT(11) NOT NULL ,
  PRIMARY KEY (`group_forum_id`) ,
  INDEX `fk_group_forum_forum1_idx` (`forum_id` ASC) ,
  INDEX `fk_group_forum_groups1_idx` (`group_id` ASC) ,
  INDEX `fk_group_forum_group_members1_idx` (`group_member_id` ASC) ,
  CONSTRAINT `fk_group_forum_forum1`
    FOREIGN KEY (`forum_id` )
    REFERENCES `ussap`.`forum` (`forum_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_forum_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `ussap`.`groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_forum_group_members1`
    FOREIGN KEY (`group_member_id` )
    REFERENCES `ussap`.`group_members` (`group_member_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_notification`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_notification` (
  `group_notification_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `notification_id` INT(11) NOT NULL ,
  `group_id` INT(11) NOT NULL ,
  `target_object_magic_id` VARCHAR(45) NULL DEFAULT NULL ,
  `target_object_type` VARCHAR(100) NULL DEFAULT NULL ,
  PRIMARY KEY (`group_notification_id`) ,
  INDEX `fk_group_notification_Notification1_idx` (`notification_id` ASC) ,
  INDEX `fk_group_notification_Groups1_idx` (`group_id` ASC) ,
  CONSTRAINT `fk_group_notification_Groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `ussap`.`groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_notification_Notification1`
    FOREIGN KEY (`notification_id` )
    REFERENCES `ussap`.`notification` (`notification_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_post`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_post` (
  `group_id` INT(11) NOT NULL ,
  `group_member_id` INT(11) NOT NULL ,
  `post_id` INT(11) NOT NULL ,
  `group_post_id` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`group_post_id`) ,
  INDEX `fk_group_messages_Groups1_idx` (`group_id` ASC) ,
  INDEX `fk_group_post_Group_members1_idx` (`group_member_id` ASC) ,
  INDEX `post_id_index` (`post_id` ASC) ,
  CONSTRAINT `fk_group_messages_Groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `ussap`.`groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_post_Group_members1`
    FOREIGN KEY (`group_member_id` )
    REFERENCES `ussap`.`group_members` (`group_member_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `group_post_ibfk_1`
    FOREIGN KEY (`post_id` )
    REFERENCES `ussap`.`posts` (`post_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_privacy`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_privacy` (
  `group_privacy_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `group_id` INT(11) NOT NULL ,
  `type` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`group_privacy_id`) ,
  INDEX `group_id` (`group_id` ASC) ,
  CONSTRAINT `group_privacy_ibfk_1`
    FOREIGN KEY (`group_id` )
    REFERENCES `ussap`.`groups` (`group_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`group_upload`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`group_upload` (
  `group_upload_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `group_id` INT(11) NOT NULL ,
  `upload_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`group_upload_id`) ,
  INDEX `fk_group_upload_groups1_idx` (`group_id` ASC) ,
  INDEX `upload_id_index` (`upload_id` ASC) ,
  INDEX `user_id_index` (`user_id` ASC) ,
  CONSTRAINT `fk_group_upload_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `ussap`.`groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `group_upload_ibfk_1`
    FOREIGN KEY (`upload_id` )
    REFERENCES `ussap`.`uploads` (`upload_id` ),
  CONSTRAINT `group_upload_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`login_attempts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`login_attempts` (
  `login_attempts_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `login_attempt_time` VARCHAR(30) NULL DEFAULT NULL ,
  PRIMARY KEY (`login_attempts_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`post_bookmark`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`post_bookmark` (
  `post_bookmark_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `bookmark_id` INT(11) NOT NULL ,
  `post_magic_id` VARCHAR(50) NULL DEFAULT NULL ,
  PRIMARY KEY (`post_bookmark_id`) ,
  INDEX `fk_post_bookmark_bookmarks1_idx` (`bookmark_id` ASC) ,
  INDEX `fk_post_bookmark_posts1_idx` (`post_magic_id` ASC) ,
  CONSTRAINT `fk_post_bookmark_bookmarks1`
    FOREIGN KEY (`bookmark_id` )
    REFERENCES `ussap`.`bookmarks` (`bookmark_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`post_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`post_comments` (
  `post_comment_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `content` LONGTEXT NOT NULL ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `post_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `magic_id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`post_comment_id`) ,
  UNIQUE INDEX `unique_magic_id` (`magic_id` ASC) ,
  INDEX `fk_Post_comments_Posts1_idx` (`post_id` ASC) ,
  INDEX `fk_Post_comments_Users1_idx` (`user_id` ASC) ,
  INDEX `magic_id_index` (`magic_id` ASC) ,
  CONSTRAINT `fk_Post_comments_Posts1`
    FOREIGN KEY (`post_id` )
    REFERENCES `ussap`.`posts` (`post_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Post_comments_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`post_comment_smiley`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`post_comment_smiley` (
  `post_comment_smiley_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `post_comment_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`post_comment_smiley_id`) ,
  INDEX `post_comment_id_idx` (`post_comment_id` ASC) ,
  INDEX `fk_Post_comment_smiley_user1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_Post_comment_smiley_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `post_comment_id`
    FOREIGN KEY (`post_comment_id` )
    REFERENCES `ussap`.`post_comments` (`post_comment_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`post_privacy`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`post_privacy` (
  `post_privacy_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `target` VARCHAR(45) NOT NULL ,
  `post_id` INT(11) NOT NULL ,
  `target_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`post_privacy_id`) ,
  INDEX `post_id_index` (`post_id` ASC) ,
  CONSTRAINT `post_privacy_ibfk_1`
    FOREIGN KEY (`post_id` )
    REFERENCES `ussap`.`posts` (`post_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 41
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`post_smiley`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`post_smiley` (
  `post_smiley_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `post_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`post_smiley_id`) ,
  INDEX `fk_Post_smiley_Posts1_idx` (`post_id` ASC) ,
  INDEX `fk_Post_smiley_user1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_Post_smiley_Posts1`
    FOREIGN KEY (`post_id` )
    REFERENCES `ussap`.`posts` (`post_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Post_smiley_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 24
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`profile`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`profile` (
  `profile_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `date_of_birth` DATE NULL DEFAULT NULL ,
  `email_address` VARCHAR(45) NOT NULL ,
  `gender` CHAR(1) NOT NULL ,
  `phonenumber` VARCHAR(20) NULL DEFAULT NULL ,
  `year_start` YEAR NULL DEFAULT NULL ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `profile_pic` TEXT NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `department_id` INT(11) NOT NULL ,
  `category` VARCHAR(45) NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `index_number` VARCHAR(30) NULL DEFAULT NULL ,
  `about_me` TEXT NULL DEFAULT NULL ,
  `year_complete` YEAR NULL DEFAULT NULL ,
  `profile_pic_thumb` TEXT NULL DEFAULT NULL ,
  `programme` VARCHAR(250) NULL DEFAULT NULL ,
  `hobbies` TEXT NULL DEFAULT NULL ,
  `black_list` BINARY(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`profile_id`) ,
  INDEX `fk_Profile_Users1_idx` (`user_id` ASC) ,
  INDEX `fk_Profile_Department1_idx` (`department_id` ASC) ,
  CONSTRAINT `fk_Profile_Department1`
    FOREIGN KEY (`department_id` )
    REFERENCES `ussap`.`department` (`department_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Profile_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 19
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`privacy`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`privacy` (
  `privacy_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `privacy_type` VARCHAR(45) NOT NULL ,
  `profile_id` INT(11) NOT NULL ,
  PRIMARY KEY (`privacy_id`) ,
  INDEX `fk_Privacy_Profile1_idx` (`profile_id` ASC) ,
  CONSTRAINT `fk_Privacy_Profile1`
    FOREIGN KEY (`profile_id` )
    REFERENCES `ussap`.`profile` (`profile_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`token`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`token` (
  `token_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `token_type` VARCHAR(100) NOT NULL ,
  `token` CHAR(128) NOT NULL ,
  `token_salt` CHAR(128) NOT NULL ,
  `token_hash` CHAR(128) NOT NULL ,
  PRIMARY KEY (`token_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`usap_admin`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`usap_admin` (
  `admin_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(50) NOT NULL ,
  `password` CHAR(128) NOT NULL ,
  `salt` CHAR(128) NOT NULL ,
  `first_name` VARCHAR(50) NOT NULL ,
  `last_name` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`admin_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`user_forum`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`user_forum` (
  `user_forum_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `forum_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `forum_topic` TEXT NOT NULL ,
  PRIMARY KEY (`user_forum_id`) ,
  INDEX `fk_user_forum_forum1_idx` (`forum_id` ASC) ,
  INDEX `fk_user_forum_user1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_user_forum_forum1`
    FOREIGN KEY (`forum_id` )
    REFERENCES `ussap`.`forum` (`forum_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_forum_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`user_notification`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`user_notification` (
  `user_notification_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `notification_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `target_object_magic_id` VARCHAR(45) NULL DEFAULT NULL ,
  `target_object_type` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`user_notification_id`) ,
  INDEX `fk_user_notification_Notification1_idx` (`notification_id` ASC) ,
  INDEX `fk_user_notification_Users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_user_notification_Notification1`
    FOREIGN KEY (`notification_id` )
    REFERENCES `ussap`.`notification` (`notification_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_notification_Users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 23
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`user_post`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`user_post` (
  `post_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `user_post_id` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`user_post_id`) ,
  INDEX `fk_user_post_posts1_idx` (`post_id` ASC) ,
  INDEX `fk_user_post_user1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_user_post_posts1`
    FOREIGN KEY (`post_id` )
    REFERENCES `ussap`.`posts` (`post_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_post_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 41
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ussap`.`user_uploads`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ussap`.`user_uploads` (
  `user_uploads_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `upload_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_uploads_id`) ,
  INDEX `fk_user_uploads_Uploads1_idx` (`upload_id` ASC) ,
  INDEX `fk_user_uploads_user1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_user_uploads_Uploads1`
    FOREIGN KEY (`upload_id` )
    REFERENCES `ussap`.`uploads` (`upload_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_uploads_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `user_uploads_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ussap`.`user` (`user_id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = latin1;

USE `ussap` ;

-- -----------------------------------------------------
-- procedure chck_frnd
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `chck_frnd`(IN user INT, IN indi INT)
BEGIN
    SELECT lst.friend_list_id FROM ussap.friend_list AS lst WHERE lst.user_friend_id = user and lst.friend_id1 = indi;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure delete_forum_comment_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_forum_comment_smiley`(IN userId INT, IN magicId VARCHAR(45))
BEGIN
      SET @commentId = (SELECT ussap.forum_comments.forum_comment_id FROM ussap.forum_comments WHERE ussap.forum_comments.magic_id = magicId);
      DELETE smly.* FROM ussap.forum_comment_smiley AS smly WHERE smly.forum_comment_id = @commentId AND smly.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure delete_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_noti`(IN notiMagicId VARCHAR(45), IN notiType VARCHAR(50))
BEGIN 
    SET @notiId = (SELECT noti.notification_id FROM ussap.notification AS noti WHERE noti.magic_id = notiMagicId);
    IF notiType = 'user' THEN 
      DELETE usr_noti.* FROM ussap.user_notification as usr_noti WHERE usr_noti.notification_id = @notiId;
    ELSEIF notiType = 'department' THEN 
      DELETE dep_noti.* FROM ussap.department_notification AS dep_noti WHERE dep_noti.notification_id = @notiId;
    ELSEIF notiType = 'group' THEN 
      DELETE grp_noti.* FROM ussap.group_notification AS grp_noti WHERE grp_noti.notification_id = @notiId;
    END IF;
    
    DELETE noti.* FROM ussap.notification as noti WHERE noti.notification_id = @notiId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure delete_post_comment_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_post_comment_smiley`(IN userId INT, IN magicId VARCHAR(45))
BEGIN
      SET @commentId = (SELECT ussap.post_comments.post_comment_id FROM ussap.post_comments WHERE ussap.post_comments.magic_id = magicId);
      DELETE smly.* FROM ussap.post_comment_smiley AS smly WHERE smly.post_comment_id = @commentId AND smly.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure delete_post_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_post_smiley`(IN userId INT, IN magicId VARCHAR(45))
BEGIN
      SET @postId = (SELECT ussap.posts.post_id FROM ussap.posts WHERE ussap.posts.magic_id = magicId);
      DELETE smly.* FROM ussap.post_smiley AS smly WHERE smly.post_id = @postId AND smly.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure delete_user
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_user`(IN userId INT)
BEGIN 
    UPDATE ussap.profile as usr set usr.black_list = 1 where usr.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_all_dep
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_all_dep`()
BEGIN
    SELECT * FROM ussap.department;
    END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_all_user_forums
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_all_user_forums`(IN userId INT)
BEGIN
    SET @frnd_id = (SELECT frnd.friend_id FROM ussap.friends as frnd WHERE frnd.user_id = userId);

    SELECT * FROM (SELECT * FROM 
                    (SELECT forums.forum_id,forums.magic_id,forums.forum_type,forums.created_at,forums.category,forum_priv.target,forums.flag,forums.forum_topic,forums.user_id FROM 
                      (SELECT forum.forum_id,forum.magic_id,forum.created_at,forum.forum_type,forum.category,forum.flag,usr_forum.forum_topic,usr_forum.user_id FROM ussap.forum as forum
                        JOIN ussap.user_forum AS usr_forum ON (forum.forum_id = usr_forum.forum_id)
                          WHERE usr_forum.user_id = userId) AS forums JOIN ussap.forum_privacy AS forum_priv ON (forums.forum_id = forum_priv.forum_id)) AS usr_forum
                   union
                   SELECT * from
                     /*friend forums*/
                     (SELECT frnd_forum.forum_id,frnd_forum.magic_id,frnd_forum.forum_type,frnd_forum.created_at,frnd_forum.category,frnd_forum.target,frnd_forum.flag,usr.forum_topic,usr.user_id FROM
                       (SELECT frnds_forum.forum_id,frnds_forum.forum_type,frnds_forum.target,frnds_forum.category,frnds_forum.flag,frnds_forum.created_at,frnds_forum.magic_id,frnds_forum.target_id,lst.friend_id1,lst.user_friend_id FROM
                         (SELECT frm.forum_id,frm.category,frm.created_at,frm.magic_id,frm.flag,frm.forum_type,forum_priv.target_id,forum_priv.target FROM ussap.forum AS frm
                           JOIN ussap.forum_privacy as forum_priv on(frm.forum_id = forum_priv.forum_id) WHERE forum_priv.target = 'friend') as frnds_forum
                         JOIN ussap.friend_list as lst ON (lst.user_friend_id = frnds_forum.target_id) WHERE lst.friend_id1 = @frnd_id) as frnd_forum
                       JOIN ussap.user_forum as usr ON (frnd_forum.forum_id = usr.forum_id)) as frnd_forum
                   union
                   SELECT * FROM
                      /*general forum*/
                     (SELECT gen_forum.forum_id,gen_forum.magic_id,gen_forum.forum_type,gen_forum.created_at,gen_forum.category,gen_forum.target,gen_forum.flag,usr.forum_topic,usr.user_id FROM
                       (SELECT forum.forum_id,forum.category,forum.flag,forum.magic_id,forum.created_at,forum.forum_type,forum_priv.target FROM ussap.forum as forum
                         JOIN ussap.forum_privacy as forum_priv ON (forum.forum_id = forum_priv.forum_id) WHERE forum_priv.target = 'general') AS gen_forum
                       JOIN ussap.user_forum AS usr ON (gen_forum.forum_id = usr.forum_id) WHERE usr.user_id <> userId ORDER BY gen_forum.created_at DESC) as gen_forum) as totalForum ORDER BY totalForum.created_at DESC;
    

  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_all_user_posts
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_all_user_posts`(IN userId INT)
BEGIN
    SET @frnd_id = (SELECT frnd.friend_id FROM ussap.friends as frnd WHERE frnd.user_id = userId);
    SELECT * FROM (SELECT * FROM
                  /*user posts*/
                  (SELECT posts.post_id,posts.magic_id,posts.content_type,posts.created_at,posts.flag,posts.pic_url,posts.post_text,posts.post_type,post_priv.target,posts.user_id FROM
                    (SELECT post.post_id,post.magic_id,post.post_type,post.content_type,post.created_at,post.flag,post.pic_url,post.post_text,usr_post.user_id
                     FROM ussap.posts AS post JOIN ussap.user_post AS usr_post ON (post.post_id = usr_post.post_id)
                     WHERE usr_post.user_id = userId) AS posts JOIN ussap.post_privacy AS post_priv ON(posts.post_id = post_priv.post_id)) AS usr_post
                   union
                   SELECT * from
                    /*friend posts*/
                     (SELECT frnd_post.post_id,frnd_post.magic_id,frnd_post.content_type,frnd_post.created_at,frnd_post.flag,frnd_post.pic_url,frnd_post.post_text,frnd_post.post_type,frnd_post.target,usr.user_id FROM
                       (SELECT * FROM
                         (SELECT post.post_id,post.magic_id,post.post_text,post.content_type,post.created_at,post.flag,post.pic_url,post.post_type,post_priv.target_id,post_priv.target FROM ussap.posts AS post
                           JOIN ussap.post_privacy as post_priv on(post.post_id = post_priv.post_id) WHERE post_priv.target = 'friend') as frnds_post
                         JOIN ussap.friend_list as lst ON (lst.user_friend_id = frnds_post.target_id) WHERE lst.friend_id1 = @frnd_id) as frnd_post
                       JOIN ussap.user_post as usr ON (frnd_post.post_id = usr.post_id)) as frnd_post
                   union
                   SELECT * FROM
                    /*general posts*/
                     (SELECT gen_post.post_id,gen_post.magic_id,gen_post.content_type,gen_post.created_at,gen_post.flag,gen_post.pic_url,gen_post.post_text,gen_post.post_type,gen_post.target,usr.user_id FROM
                       (SELECT pst.post_id,pst.magic_id,pst.post_text,pst.content_type,pst.created_at,pst.flag,pst.pic_url,pst.post_type,post_priv.target FROM ussap.posts as pst
                         JOIN ussap.post_privacy as post_priv ON (pst.post_id = post_priv.post_id) WHERE post_priv.target = 'general') AS gen_post
                       JOIN ussap.user_post AS usr ON (gen_post.post_id = usr.post_id) WHERE usr.user_id <> userId) as gen_post) as totalPosts ORDER BY totalPosts.created_at DESC LIMIT 8;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_byname_dep
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_byname_dep`(IN depName VARCHAR(150))
BEGIN
    SELECT * FROM ussap.department WHERE ussap.department.department_name = depName;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_chat_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_chat_noti`(IN userId INT)
BEGIN
    SELECT noti.notification_id,noti.created_at,noti.notification_text,noti.notification_type,noti.perp_object_id,noti.perp,noti.perp_object_type,cht_noti.target_id,cht_noti.chat_id,cht_noti.target_object_type FROM notification as noti 
      JOIN ussap.chat_notification AS cht_noti ON (noti.notification_id = cht_noti.notification_id)
        WHERE cht_noti.target_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_count_department_forums
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_count_department_forums`(IN grpId INT)
BEGIN 
    SELECT grp_frm.forum_id FROM ussap.group_forum as grp_frm WHERE grp_frm.group_id = grpId;
    END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_dep
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_dep`(IN depId INT)
BEGIN
    SELECT * FROM ussap.department WHERE ussap.department.department_id = depId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_dep_uploads
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_dep_uploads`(IN depId INT)
BEGIN
    SELECT * FROM ussap.uploads as upld JOIN ussap.department_upload AS dep_upld ON (upld.upload_id = dep_upld.upload_id) 
      WHERE dep_upld.department_id = depId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_department_forums
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_department_forums`(IN depId INT)
BEGIN
    SELECT forum.forum_id,forum.forum_type,forum.magic_id,forum.category,forum.created_at,forum.flag,forum.forum_type,dep_forum.user_id,dep_forum.department_id,dep_forum.forum_topic FROM ussap.forum AS forum 
      JOIN ussap.department_forum AS dep_forum ON (forum.forum_id = dep_forum.forum_id)
        WHERE dep_forum.department_id = depId ORDER BY forum.created_at DESC;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_department_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_department_noti`(IN depId INT)
BEGIN
    SELECT noti.notification_id,noti.magic_id,noti.created_at,noti.notification_text,noti.notification_type,noti.perp_object_id,noti.perp,noti.perp_object_type,dep_noti.department_id,dep_noti.target_object_id,dep_noti.target_object_type FROM ussap.notification as noti 
      JOIN ussap.department_notification as dep_noti ON (noti.notification_id = dep_noti.notification_id)
      WHERE dep_noti.department_id = depId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_department_posts
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_department_posts`(IN depId INT)
BEGIN 
   
    SELECT post.magic_id,post.post_id,post.content_type,post.created_at,post.flag,post.pic_url,post.post_text,post.post_type,dep_post.user_id,dep_post.department_id 
      FROM ussap.posts as post JOIN ussap.department_post AS dep_post ON (post.post_id = dep_post.post_id)
        WHERE dep_post.department_id = depId ORDER BY post.created_at DESC;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_department_uploads
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_department_uploads`(IN depid INT)
BEGIN 
      SELECT * from ussap.uploads AS upld JOIN ussap.department_upload AS dep_up ON upld.upload_id = dep_up.upload_id
        WHERE dep_up.department_id = depid;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_flagged_forums
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_flagged_forums`(IN lim INT)
BEGIN

    SELECT * FROM
      (SELECT * FROM
        (SELECT frm.forum_id,frm.magic_id,frm.forum_type,frm.category,frm.created_at,frm.flag,usr_frm.user_id,usr_frm.forum_topic FROM ussap.forum as frm JOIN ussap.user_forum AS usr_frm
            ON frm.forum_id = usr_frm.forum_id
        WHERE frm.forum_type = 'user' AND frm.flag >= lim) AS usr_frms
       UNION
       SELECT * FROM
         (SELECT frm.forum_id,frm.magic_id,frm.forum_type,frm.category,frm.created_at,frm.flag,dep_frm.user_id,dep_frm.forum_topic FROM ussap.forum as frm JOIN ussap.department_forum AS dep_frm ON frm.forum_id = dep_frm.forum_id
         WHERE frm.forum_type = 'department' AND frm.flag >= lim) AS dep_frms
       UNION
       SELECT * FROM
         (SELECT frm.forum_id,frm.magic_id,frm.forum_type,frm.category,frm.created_at,frm.flag,mem.user_id,frm.forum_topic from
           (SELECT frm.forum_id,frm.magic_id,frm.forum_type,frm.category,frm.created_at,frm.flag,grp_frm.forum_topic,grp_frm.group_member_id FROM ussap.forum AS frm JOIN ussap.group_forum AS grp_frm ON frm.forum_id = grp_frm.forum_id
           WHERE frm.forum_type = 'group' AND frm.flag >= lim) as frm JOIN ussap.group_members AS mem ON frm.group_member_id = mem.group_member_id)AS grp_frms
      ) AS all_forums ORDER BY all_forums.created_at DESC;

  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_flagged_posts
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_flagged_posts`(IN lim INT)
BEGIN 
  
  SELECT * FROM 
    (SELECT * FROM 
      (SELECT pst.post_id,pst.magic_id,pst.post_type,pst.content_type,pst.post_text,pst.pic_url,pst.flag,pst.created_at,usr_post.user_id FROM ussap.posts as pst JOIN ussap.user_post as usr_post
        ON pst.post_id = usr_post.post_id
          WHERE pst.post_type = 'user' and pst.flag >= lim) AS usr_psts
        UNION
       SELECT * FROM 
       (SELECT pst.post_id,pst.magic_id,pst.post_type,pst.content_type,pst.post_text,pst.pic_url,pst.flag,pst.created_at,dep_pst.user_id FROM ussap.posts AS pst JOIN ussap.department_post as dep_pst 
         ON pst.post_id = dep_pst.post_id
          WHERE pst.post_type = 'department' and pst.flag >= lim) AS dep_psts
        UNION
       SELECT * FROM 
         (SELECT pst.post_id,pst.magic_id,pst.post_type,pst.content_type,pst.post_text,pst.pic_url,pst.flag,pst.created_at,mem.user_id FROM
         (SELECT pst.post_id,pst.magic_id,pst.post_type,pst.content_type,pst.post_text,pst.pic_url,pst.flag,pst.created_at,grp_pst.group_member_id FROM ussap.posts as pst JOIN ussap.group_post as grp_pst on pst.post_id = grp_pst.post_id
         WHERE pst.post_type = 'group' and pst.flag >= lim)as pst JOIN ussap.group_members as mem
           ON pst.group_member_id = mem.group_member_id) AS grp_psts
    )AS all_posts ORDER BY all_posts.created_at DESC;
  
    
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_forum
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_forum`(IN forumMagicId VARCHAR(45))
BEGIN
    /*
      obtain information about the type of forum that we are coming to deal with
    */
    SET @forumType = (SELECT frm.forum_type FROM ussap.forum as frm WHERE frm.magic_id = forumMagicId);
  
    IF @forumType = 'user' THEN 
      SELECT * FROM ussap.forum AS frm JOIN ussap.user_forum AS usr_frm ON (frm.forum_id = usr_frm.forum_id)
        WHERE frm.magic_id = forumMagicId;
    ELSEIF @forumType = 'department' THEN
      SELECT * FROM ussap.forum AS frm JOIN ussap.department_forum AS dep_frm ON (frm.forum_id = dep_frm.forum_id)
      WHERE frm.magic_id = forumMagicId;
    ELSEIF @forumType = 'group' THEN
      SELECT * FROM ussap.forum AS frm JOIN ussap.group_forum AS grp_frm ON (frm.forum_id = grp_frm.forum_id)
      WHERE frm.magic_id = forumMagicId;
    END IF;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_forum_bkmk
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_forum_bkmk`(IN userId INT)
BEGIN
    SELECT bkmk.bookmark_id,bkmk.user_id,bkmk.bookmark_type,bkmk.created_at,forum_bkmk.forum_magic_id FROM ussap.bookmarks AS bkmk 
      JOIN ussap.forum_bookmark AS forum_bkmk ON (bkmk.bookmark_id = forum_bkmk.bookmark_id)
        WHERE bkmk.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_forum_comment_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_forum_comment_smiley`(IN commentId INT)
BEGIN 
    SELECT * FROM ussap.forum_comment_smiley where ussap.forum_comment_smiley.forum_comment_id = commentId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_forum_comments
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_forum_comments`(IN forumId INT,IN magicId VARCHAR(45), IN type BINARY)
BEGIN 
  
    IF type = 1 THEN 
      SET @forumId = (SELECT frm.forum_id FROM ussap.forum as frm WHERE frm.magic_id = magicId);
      SELECT * FROM ussap.forum_comments WHERE ussap.forum_comments.forum_id = @forumId ORDER BY ussap.forum_comments.created_at ASC;
    ELSE
      SELECT * FROM ussap.forum_comments WHERE ussap.forum_comments.forum_id = forumId ORDER BY ussap.forum_comments.created_at ASC;
    END IF;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_forum_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_forum_smiley`(IN magicId VARCHAR(45))
BEGIN
    SET @commentId = (SELECT commnt.forum_comment_id FROM ussap.forum_comments commnt WHERE commnt.magic_id = magicId);
    SELECT * FROM ussap.forum_comment_smiley WHERE ussap.forum_comment_smiley.forum_comment_id = @commentId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_friend_forums
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_friend_forums`(IN friendId INT)
BEGIN
    SELECT frnd_forum.forum_id,frnd_forum.forum_type,frnd_forum.magic_id,frnd_forum.created_at,frnd_forum.cartegory,usr.forum_topic,usr.user_id FROM
      (SELECT * FROM
        (SELECT frm.forum_id,frm.cartegory,frm.created_at,frm.magic_id,frm.forum_type,forum_priv.target_id FROM ussap.forum AS frm
          JOIN ussap.forum_privacy as forum_priv on(frm.forum_id = forum_priv.forum_id) WHERE forum_priv.target = 'friend') as frnds_forum
      JOIN ussap.friend_list as lst ON (lst.user_friend_id = frnds_forum.target_id) WHERE lst.friend_id1 = friendId) as frnd_forum
    JOIN ussap.user_forum as usr ON (frnd_forum.forum_id = usr.forum_id);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_friend_list
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_friend_list`(IN userId INT)
BEGIN
    SELECT frnds.user_id, usr_frnds.friend_id1 FROM ussap.friends AS frnds JOIN 
      (SELECT frnd_lst.friend_id1 from ussap.friends as frnd
        JOIN ussap.friend_list AS frnd_lst ON (frnd.friend_id = frnd_lst.user_friend_id) WHERE frnd.user_id = userId) as usr_frnds ON (frnds.friend_id = usr_frnds.friend_id1);
    END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_friend_posts
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_friend_posts`(IN friendId INT)
BEGIN 
    SELECT frnd_post.post_id,frnd_post.content_type,frnd_post.created_at,frnd_post.flag,frnd_post.pic_url,frnd_post.post_text,frnd_post.post_type,frnd_post.target,usr.user_id FROM 
      (SELECT * FROM 
        (SELECT post.post_id,post.post_text,post.content_type,post.created_at,post.flag,post.pic_url,post.post_type,post_priv.target_id,post_priv.target FROM ussap.posts AS post 
        JOIN ussap.post_privacy as post_priv on(post.post_id = post_priv.post_id) WHERE ussap.post_privacy.target = 'friend') as frnds_post
      JOIN ussap.friend_list as lst ON (lst.user_friend_id = frnds_post.target_id) WHERE lst.friend_id1 = friendId) as frnd_post 
    JOIN ussap.user_post as usr ON (frnd_post.post_id = usr.post_id);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_general_forums
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_general_forums`(IN userId INT)
BEGIN
    SELECT gen_forum.forum_id,gen_forum.magic_id,gen_forum.forum_type,gen_forum.created_at,gen_forum.cartegory,usr.forum_topic,usr.user_id FROM
      (SELECT forum.forum_id,forum.cartegory,forum.magic_id,forum.created_at,forum.forum_type FROM ussap.forum as forum 
        JOIN ussap.forum_privacy as forum_priv ON (forum.forum_id = forum_priv.forum_id) WHERE forum_priv.target = 'general') AS gen_forum
    JOIN ussap.user_forum AS usr ON (gen_forum.forum_id = usr.forum_id) WHERE usr.user_id <> userId ORDER BY gen_forum.created_at DESC;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_general_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_general_post`(IN userId INT)
BEGIN 
    SELECT gen_post.post_id,gen_post.post_type,gen_post.pic_url,gen_post.flag,gen_post.content_type,gen_post.created_at,gen_post.post_text,gen_post.target,usr.user_id FROM 
      (SELECT pst.post_id,pst.post_text,pst.content_type,pst.created_at,pst.flag,pst.pic_url,pst.post_type,post_priv.target FROM ussap.posts as pst 
      JOIN ussap.post_privacy as post_priv ON (pst.post_id = post_priv.post_id) WHERE post_priv.target = 'general') AS gen_post 
    JOIN ussap.user_post AS usr ON (gen_post.post_id = usr.post_id) WHERE usr.user_id <> userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_group
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_group`(IN name VARCHAR(50))
BEGIN 
    select grp.group_name,grp.group_id,grp.admin,grp.created_at,grp.group_description,grp_priv.type from ussap.groups as grp JOIN ussap.group_privacy as grp_priv on (grp.group_id = grp_priv.group_id)
      WHERE grp.group_name = name;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_group_forums
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_group_forums`(IN groupId INT)
BEGIN 
    SELECT forum.forum_id,forum.created_at,forum.category,forum.forum_type,forum.magic_id,forum.forum_topic,forum.flag, grp_mem.user_id FROM 
      (SELECT forum.forum_id,forum.created_at,forum.flag,forum.category,forum.forum_type,forum.magic_id,grp_forum.forum_topic,grp_forum.group_member_id FROM ussap.forum as forum 
      JOIN ussap.group_forum as grp_forum ON (forum.forum_id = grp_forum.forum_id) 
        WHERE grp_forum.group_id = groupId) forum JOIN ussap.group_members AS grp_mem ON (forum.group_member_id = grp_mem.group_member_id) ORDER BY forum.created_at DESC LIMIT 8;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_group_members
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_group_members`(IN grpId INT)
BEGIN 
    SELECT * FROM ussap.group_members as grp_mem 
      where grp_mem.group_id = grpId;
    END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_group_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_group_noti`(IN groupId INT)
BEGIN
    SELECT noti.notification_id,noti.magic_id,noti.created_at,noti.notification_text,noti.notification_type,noti.perp_object_id,noti.perp,noti.perp_object_type,grp_noti.group_id,grp_noti.target_object_id,grp_noti.target_object_type FROM ussap.notification as noti 
      JOIN ussap.group_notification AS grp_noti ON (noti.notification_id = grp_noti.notification_id)
        WHERE grp_noti.group_id = groupId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_group_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_group_post`(IN groupId INT)
BEGIN
  SELECT grp_posts.post_id,grp_posts.magic_id,grp_posts.content_type,grp_posts.created_at,grp_posts.flag,grp_posts.pic_url,grp_posts.post_text,grp_posts.post_type,grp_mem.user_id FROM
    (SELECT psts.post_id,psts.magic_id,psts.content_type,psts.created_at,psts.flag,psts.pic_url,psts.post_text,psts.post_type,grp_pst.group_member_id FROM ussap.posts as psts
      JOIN ussap.group_post as grp_pst ON (psts.post_id = grp_pst.post_id)
    WHERE grp_pst.group_id = groupId) AS grp_posts
    JOIN ussap.group_members as grp_mem ON (grp_posts.group_member_id = grp_mem.group_member_id) ORDER BY grp_posts.created_at DESC LIMIT 8;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_group_upload
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_group_upload`(IN groupId INT)
BEGIN
    SELECT * FROM ussap.group_upload as grp_upload 
      JOIN ussap.groups AS  grps ON (grp_upload.group_id = grps.group_id)
        WHERE grp_upload.group_id= groupId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_groups
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_groups`(IN userId INT)
BEGIN
  SELECT usr_grp.group_id,usr_grp.group_name,usr_grp.created_at,usr_grp.admin,grp_priv.type,usr_grp.group_description from 
    (SELECT grp.group_id,grp.admin,grp.created_at,grp.group_name, grp.group_description FROM ussap.groups as grp 
      JOIN ussap.group_members AS grp_mem ON (grp.group_id = grp_mem.group_id)
        WHERE grp_mem.user_id = userId) AS usr_grp JOIN ussap.group_privacy as grp_priv ON (usr_grp.group_id = grp_priv.group_id);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_grp_uploads
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_grp_uploads`(IN grpId INT)
BEGIN
    SELECT * FROM ussap.uploads as upld JOIN ussap.group_upload AS grp_upld ON (upld.upload_id = grp_upld.upload_id) 
      WHERE grp_upld.group_id = grpId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_post`(IN postMagicId VARCHAR(45))
BEGIN 
    /*
      obtain information about the type of the post we are coming to work with
    */
    SET @postType = (SELECT pst.post_type FROM ussap.posts as pst WHERE pst.magic_id = postMagicId);
    
    IF @postType = 'user' THEN 
      SELECT * FROM ussap.posts AS pst JOIN ussap.user_post AS usr_post ON (pst.post_id = usr_post.post_id)
        WHERE pst.magic_id = postMagicId;
      
    ELSEIF @postType = 'department' THEN 
      SELECT * FROM ussap.posts AS pst JOIN ussap.department_post AS dep_post ON (pst.post_id = dep_post.post_id)
        WHERE pst.magic_id = postMagicId;
      
    ELSEIF @postType = 'group' THEN 
      SELECT * FROM ussap.posts AS pst JOIN ussap.group_post AS grp_post ON (pst.post_id = grp_post.post_id)
        WHERE pst.magic_id = postMagicId;
      
    END IF;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_post_bkmk
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_post_bkmk`(IN userId INT)
BEGIN
    SELECT bkmk.bookmark_id,bkmk.user_id,bkmk.bookmark_type,bkmk.created_at,post_bkmk.post_magic_id FROM ussap.bookmarks AS bkmk 
      JOIN ussap.post_bookmark AS post_bkmk ON (bkmk.bookmark_id = post_bkmk.bookmark_id)
        WHERE bkmk.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_post_comment_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_post_comment_smiley`(IN commentMagicId VARCHAR(45))
BEGIN 
    SET @commentId = (SELECT com.post_comment_id FROM ussap.post_comments as com WHERE com.magic_id = commentMagicId);
    SELECT * FROM ussap.post_comment_smiley where ussap.post_comment_smiley.post_comment_id = @commentId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_post_comments
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_post_comments`(IN postId INT, IN postMagicId VARCHAR(45), IN type BINARY)
BEGIN 
    
    IF type = 1 THEN/*use the magic id of the post*/
      SET @postId = (SELECT psts.post_id FROM ussap.posts as psts WHERE psts.magic_id = postMagicId);
      SELECT * FROM ussap.post_comments as pc WHERE pc.post_id = @postId ORDER BY pc.created_at ASC;
    ELSE/*use the post id of the post*/
      SELECT * FROM ussap.post_comments as pc WHERE pc.post_id = postId ORDER BY pc.created_at ASC;
    END IF;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_post_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_post_smiley`(IN postMagicId VARCHAR(45))
BEGIN
    SET @postId = (SELECT pst.post_id FROM ussap.posts AS pst WHERE pst.magic_id = postMagicId);
    SELECT * FROM ussap.post_smiley WHERE ussap.post_smiley.post_id = @postId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_profile
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_profile`(IN userId INT)
BEGIN 
    SELECT * FROM ussap.profile WHERE ussap.profile.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_token
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_token`(IN tokenType VARCHAR(100))
BEGIN
    SELECT * FROM ussap.token AS tkn WHERE tkn.token_type = tokenType;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_user
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_user`(IN userId INT)
BEGIN 
    SELECT * from ussap.user WHERE ussap.user.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_user_forum
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_user_forum`(IN userId INT)
BEGIN
    SELECT forums.forum_id,forums.magic_id,forums.forum_type,forums.created_at,forums.category,forums.target,usr_forum.forum_topic,usr_forum.user_id FROM 
      (SELECT forum.forum_id,forum.magic_id,forum.forum_type,forum.created_at,forum.category,forum_priv.target FROM ussap.forum as forum 
        JOIN ussap.forum_privacy AS forum_priv ON (forum.forum_id = forum_priv.forum_id)) AS forums 
    JOIN ussap.user_forum AS usr_forum ON (forums.forum_id = usr_forum.forum_id) WHERE usr_forum.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_user_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_user_noti`(IN userId INT)
BEGIN
    SELECT noti.notification_id,noti.magic_id,noti.created_at,noti.notification_text,noti.notification_type,noti.perp_object_id,noti.perp,noti.perp_object_type,usr_noti.user_id,usr_noti.target_object_magic_id,usr_noti.target_object_type FROM ussap.notification as noti 
      JOIN ussap.user_notification as usr_noti ON (noti.notification_id = usr_noti.notification_id)
      WHERE usr_noti.user_id = userId ORDER BY noti.created_at DESC LIMIT 8;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_user_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_user_post`(IN userId INT)
BEGIN 
  SELECT posts.post_id,posts.post_type,posts.content_type,posts.created_at,posts.flag,posts.pic_url,posts.post_text,posts.user_id,post_priv.target FROM 
    (SELECT post.post_id,post.post_type,post.content_type,post.created_at,post.flag,post.pic_url,post.post_text,usr_post.user_id
      FROM ussap.posts AS post JOIN ussap.user_post AS usr_post ON (post.post_id = usr_post.post_id) 
        WHERE usr_post.user_id = userId) AS posts JOIN ussap.post_privacy AS post_priv ON(posts.post_id = post_priv.post_id);

  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_user_token
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_user_token`(IN email TEXT)
BEGIN
    SELECT ussap.user.token, ussap.user.series_identifier,ussap.user.password,ussap.user.user_id FROM ussap.user WHERE ussap.user.user_email = email;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetch_user_uploads
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_user_uploads`(IN userId INT)
BEGIN 
    SELECT * FROM ussap.uploads as upld JOIN ussap.user_uploads AS usr_upld ON (upld.upload_id = usr_upld.upload_id) 
      WHERE usr_upld.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure forum_comment_count
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `forum_comment_count`(IN forumId INT)
BEGIN 
    SELECT com.forum_id FROM ussap.forum_comments as com WHERE com.forum_id = forumId;
    END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure forum_flag
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `forum_flag`(IN magcId VARCHAR(45))
BEGIN
    SET @flag_num = (SELECT frm.flag FROM ussap.forum as frm WHERE frm.magic_id = magcId);
    set @flag_add = @flag_num +1;
    
    UPDATE ussap.forum as frm SET frm.flag = @flag_add WHERE frm.magic_id = magcId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_chat_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_chat_noti`(IN perpId INT, IN targetId INT, IN chatId INT, IN notiText VARCHAR(250),IN targetObjectId INT, IN targetObjectType VARCHAR(45),IN notiType VARCHAR(45),IN perpObjectType VARCHAR(45), IN perpObjectId INT, IN createdAt TIMESTAMP)
BEGIN
    INSERT INTO ussap.notification (notification_type, created_at, perp, perp_object_type, notification_text) 
      VALUES (notiType,createdAt,perpId,perpObjectType,notiText);
    
    SET @noti_id = (SELECT ussap.notification.notification_id FROM ussap.notification WHERE ussap.notification.created_at = createdAt);
    
    INSERT INTO ussap.chat_notification (target_id, target_object_type, chat_id, notification_id) 
      VALUES (targetObjectId,targetObjectType,chatId,@noti_id);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_department_forum
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_department_forum`(IN userId INT, IN forumCat TEXT, IN forumTopic TEXT, IN createdAt TIMESTAMP, IN magicId VARCHAR(45), IN depId INT)
BEGIN
    INSERT INTO ussap.forum (category, created_at, forum_type,magic_id) VALUES (forumCat,createdAt,'department',magicId);

    set @forum_id =(SELECT ussap.forum.forum_id FROM ussap.forum WHERE ussap.forum.magic_id = magicId);

    INSERT INTO ussap.department_forum (forum_topic, forum_id, department_id, user_id) VALUES (forumTopic,@forum_id,depId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_department_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_department_noti`(IN perpId INT, IN depId INT, IN notiText VARCHAR(250), IN targetObjectMagicId VARCHAR(45), IN targetObjectType VARCHAR(45), IN createdAt TIMESTAMP, IN magicId VARCHAR(45))
BEGIN
    INSERT INTO ussap.notification (notification_type, created_at, perp, notification_text,magic_id) 
      VALUES ('department',createdAt,perpId,notiText,magicId);
    
    SET @noti_id = (SELECT ussap.notification.notification_id FROM ussap.notification WHERE ussap.notification.magic_id = magicId);
    
    INSERT INTO ussap.department_notification (notification_id, department_id, target_object_magic_id, target_object_type) 
      VALUES (@noti_id,depId,targetObjectMagicId,targetObjectType);
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_department_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_department_post`(IN userId INT, IN postText TEXT, IN picUrl TEXT, IN contentType VARCHAR(45), IN createdAt TIMESTAMP, IN depId INT,IN magicId VARCHAR(45))
BEGIN 
    INSERT INTO ussap.posts (created_at, pic_url, content_type, post_text, post_type,magic_id) VALUES (createdAt,picUrl,contentType,postText,'department',magicId);
    
    set @post_id =(SELECT ussap.posts.post_id FROM ussap.posts WHERE ussap.posts.magic_id = magicId);
    
    INSERT INTO ussap.department_post (post_id, department_id, user_id) VALUES (@post_id,depId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_department_upload
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_department_upload`(IN depId INT, IN userId INT, IN f_size INT, IN f_name TEXT, IN f_type VARCHAR(120),IN createdAt DATETIME, IN url TEXT, IN magcid VARCHAR(45))
BEGIN
    INSERT INTO ussap.uploads (upload_url, created_at, size, file_type, file_name,magic_id) VALUES (url,createdAt,f_size,f_type,f_name,magcid);
      SET @upldId = (SELECT upld.upload_id FROM ussap.uploads AS upld WHERE upld.magic_id = magcid);

    INSERT INTO ussap.department_upload (upload_id, department_id,user_id) VALUES (@upldId,depId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_forum_bkmk
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_forum_bkmk`(IN userId INT,IN bookMarkType VARCHAR(45),IN forumMagicId VARCHAR(50),IN createdAt TIMESTAMP)
BEGIN
    INSERT INTO ussap.bookmarks (created_at, user_id, bookmark_type) VALUES (createdAt,userId,bookMarkType);
    
    SET @bkmk_id = (SELECT ussap.bookmarks.bookmark_id FROM ussap.bookmarks WHERE ussap.bookmarks.created_at = createdAt);

    INSERT INTO ussap.forum_bookmark (bookmark_id, forum_magic_id) VALUES (@bkmk_id,forumMagicId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_forum_comments
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_forum_comments`(IN forumMagicId VARCHAR(45), IN userId INT, IN commentText TEXT, IN createdAt TIMESTAMP, IN magicId VARCHAR(45))
BEGIN
    SET @forumId = (SELECT frm.forum_id FROM ussap.forum AS frm WHERE frm.magic_id = forumMagicId);
    INSERT INTO ussap.forum_comments (content, created_at, forum_id, user_id,magic_id) VALUES (commentText,createdAt,@forumId,userId,magicId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_forum_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_forum_smiley`(IN userId INT, IN commentMagicId VARCHAR(45))
BEGIN
    SET @commentId = (SELECT commnt.forum_comment_id FROM ussap.forum_comments AS commnt WHERE commnt.magic_id = commentMagicId);
    INSERT INTO ussap.forum_comment_smiley (forum_comment_id, user_id) VALUES (@commentId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_friend
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_friend`(IN userId INT)
BEGIN
    INSERT INTO ussap.friends (user_id) VALUES (userId);
    SELECT ussap.friends.friend_id FROM ussap.friends WHERE ussap.friends.user_id = userId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_friend_forum
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_friend_forum`(IN userId INT, IN forumCat TEXT, IN forumTopic TEXT, IN magicId VARCHAR(45),IN createdAt TIMESTAMP)
BEGIN
    INSERT INTO ussap.forum (category, created_at, forum_type,magic_id) VALUES (forumCat,createdAt,'user',magicId);

    set @forum_id = (SELECT ussap.forum.forum_id FROM ussap.forum WHERE ussap.forum.created_at = createdAt);
    SET @frnd_id = (SELECT ussap.friends.friend_id FROM ussap.friends WHERE ussap.friends.user_id = userId);

    INSERT INTO ussap.forum_privacy (target, forum_id,target_id) VALUES ('friend',@forum_id,@frnd_id);

    INSERT INTO ussap.user_forum (forum_id, user_id, forum_topic) VALUES (@forum_id,userId,forumTopic);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_friend_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_friend_post`(IN userId INT, IN postText TEXT, IN picUrl TEXT, IN contentType VARCHAR(45), IN createdAt TIMESTAMP, IN magicId VARCHAR(45))
BEGIN 
    INSERT INTO ussap.posts (created_at, pic_url, content_type, post_text, post_type,magic_id) VALUES (createdAt,picUrl,contentType,postText,'user',magicId);
    
    set @post_id =(SELECT ussap.posts.post_id FROM ussap.posts WHERE ussap.posts.magic_id = magicId);
    SET @frnd_id = (SELECT ussap.friends.friend_id from ussap.friends WHERE ussap.friends.user_id = userId);
    
    INSERT INTO ussap.post_privacy (target, post_id,target_id) VALUES ('friend',@post_id,@frnd_id);
    
    INSERT INTO ussap.user_post (post_id, user_id) VALUES (@post_id,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_friend_request
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_friend_request`(IN targetId INT, IN perpId INT, IN magicId VARCHAR(45))
BEGIN 
    INSERT INTO ussap.friend_request(target_id, perp_id,magic_id) VALUES (targetId,perpId,magicId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_friends
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_friends`(IN friend1 INT, IN friend2 INT)
BEGIN 
    INSERT INTO ussap.friend_list(user_friend_id, friend_id1) VALUES (friend1,friend2);
    INSERT INTO ussap.friend_list(user_friend_id, friend_id1) VALUES (friend2,friend1);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_general_forum
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_general_forum`(IN userId INT, IN forumCat TEXT, IN forumTopic TEXT, IN magicId VARCHAR(45),IN createdAt TIMESTAMP)
BEGIN
    INSERT INTO ussap.forum (category, created_at, forum_type,magic_id) VALUES (forumCat,createdAt,'user',magicId);

    set @forum_id = (SELECT ussap.forum.forum_id FROM ussap.forum WHERE ussap.forum.created_at = createdAt);

    INSERT INTO ussap.forum_privacy (target, forum_id) VALUES ('general',@forum_id);

    INSERT INTO ussap.user_forum (forum_id, user_id, forum_topic) VALUES (@forum_id,userId,forumTopic);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_general_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_general_post`(IN userId INT, IN postText TEXT, IN picUrl TEXT, IN contentType VARCHAR(45), IN createdAt TIMESTAMP, IN magicId VARCHAR(45))
BEGIN 
    INSERT INTO ussap.posts (created_at, pic_url, content_type, post_text, post_type,magic_id) VALUES (createdAt,picUrl,contentType,postText,'user',magicId);
    
    SET @post_id =(SELECT ussap.posts.post_id FROM ussap.posts WHERE ussap.posts.magic_id = magicId);
    
    INSERT INTO ussap.post_privacy (target, post_id) VALUES ('general',@post_id);
    
    INSERT INTO ussap.user_post (post_id, user_id) VALUES (@post_id,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_group
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_group`(IN adminId INT, IN groupName VARCHAR(120),IN createdAt TIMESTAMP,IN privType VARCHAR(10))
BEGIN
    INSERT INTO ussap.groups (group_name, created_at, admin) VALUES (groupName,createdAt,adminId);
    
    SET @grp_id = (SELECT grp.group_id FROM ussap.groups as grp WHERE grp.created_at = createdAt and grp.group_name = groupName);

    INSERT INTO ussap.group_privacy (group_id, type) VALUES (@grp_id,privType);
  
  /*
   insert the admin as the first group member
  */
    INSERT INTO ussap.group_members(group_id, user_id,group_member_status) VALUES (@grp_id,adminId,0);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_group_forum
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_group_forum`(IN userId INT, IN forumCat TEXT, IN forumTopic TEXT,  IN createdAt TIMESTAMP, IN groupId INT, IN magicId VARCHAR(45))
BEGIN
    INSERT INTO ussap.forum (category, created_at, forum_type, magic_id) VALUES (forumCat,createdAt,'group',magicId);

    set @forum_id =(SELECT ussap.forum.forum_id FROM ussap.forum WHERE ussap.forum.created_at = createdAt);
    set @member_id = (SELECT members.group_member_id FROM ussap.group_members AS members
                          WHERE members.user_id = userId AND members.group_id = groupId);

    INSERT INTO ussap.group_forum(forum_topic, forum_id, group_id, group_member_id) VALUES (forumTopic,@forum_id,groupId,@member_id);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_group_member
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_group_member`(IN userId INT, IN groupId INT)
BEGIN
    INSERT INTO ussap.group_members (group_id, user_id) VALUES (groupId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_group_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_group_noti`(IN perpId INT, IN groupId INT, IN notiText VARCHAR(250), IN targetObjectMagicId VARCHAR(45), IN targetObjectType VARCHAR(45), IN createdAt TIMESTAMP, IN magicId VARCHAR(45))
BEGIN
    INSERT INTO ussap.notification (notification_type, created_at, perp, notification_text,magic_id) 
      VALUES ('group',createdAt,perpId,notiText,magicId);

    SET @noti_id = (SELECT ussap.notification.notification_id FROM ussap.notification WHERE ussap.notification.magic_id = magicId);
    
    INSERT INTO ussap.group_notification (notification_id, group_id, target_object_magic_id, target_object_type) 
      VALUES (@noti_id,groupId,targetObjectMagicId,targetObjectType);
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_group_post
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_group_post`(IN userId INT, IN groupId INT, IN postText TEXT, IN picUrl TEXT, IN contentType VARCHAR(20), IN createdAt TIMESTAMP,IN magicId VARCHAR(45))
BEGIN
    SET @member_id = (SELECT ussap.group_members.group_member_id FROM ussap.group_members WHERE ussap.group_members.group_id = groupId AND ussap.group_members.user_id = userId);
      /*insert our posts table*/
    INSERT INTO ussap.posts (created_at, pic_url, content_type, post_text, post_type, magic_id) VALUES(createdAt,picUrl,contentType,postText,'group',magicId);
    
    /*obtain the post id*/
    SET @post_id = (SELECT ussap.posts.post_id FROM ussap.posts WHERE ussap.posts.magic_id = magicId);
    
    /*insert into group post table*/
    INSERT INTO ussap.group_post (group_id, group_member_id, post_id) VALUES (groupId,@member_id,@post_id);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_group_upload
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_group_upload`(IN grpId INT, IN userId INT, IN f_size INT, IN f_name TEXT, IN f_type VARCHAR(120),IN createdAt DATETIME, IN url TEXT, in magcid VARCHAR(45))
BEGIN
    INSERT INTO ussap.uploads (upload_url, created_at, size, file_type, file_name,magic_id) VALUES (url,createdAt,f_size,f_type,f_name,magcid);
      SET @upldId = (SELECT upld.upload_id FROM ussap.uploads AS upld WHERE upld.magic_id = magcid);

    INSERT INTO ussap.group_upload (upload_id, group_id,user_id) VALUES (@upldId,grpId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_groups
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_groups`(in groupName varchar(45), in createdAt timestamp, in adminId int , IN groupPrivacyType VARCHAR(25))
BEGIN

    IF (groupPrivacyType = 'private')THEN
      insert into ussap.groups(group_name, created_at, admin)
      values(groupName, createdAt,adminId);

      SET @groupId = (SELECT ussap.groups.group_id FROM ussap.groups WHERE ussap.groups.created_at = createdAt);

      INSERT  INTO ussap.group_privacy(type, group_id)
      VALUES ( groupPrivacyType, @groupId);

    ELSE
      insert into ussap.groups(group_name, created_at,admin)
      values(groupName, createdAt,adminId);

      SET @groupId = (SELECT ussap.groups.group_id FROM ussap.groups WHERE ussap.groups.created_at = createdAt);

      INSERT  INTO ussap.group_privacy(type, group_id)
      VALUES ( groupPrivacyType, @groupId);
    END IF ;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_post_bkmk
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_post_bkmk`(IN userId INT,IN bookMarkType VARCHAR(45),IN postMagicId VARCHAR(50),IN createdAt TIMESTAMP)
BEGIN
    INSERT INTO ussap.bookmarks (created_at, user_id, bookmark_type) VALUES (createdAt,userId,bookMarkType);
    
    SET @bkmk_id = (SELECT ussap.bookmarks.bookmark_id FROM ussap.bookmarks WHERE ussap.bookmarks.created_at = createdAt);

    INSERT INTO ussap.post_bookmark (bookmark_id, post_magic_id) VALUES (@bkmk_id,postMagicId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_post_comment
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_post_comment`(IN postMagicId VARCHAR(45), IN userId INT, IN commentText TEXT, IN createdAt TIMESTAMP, IN magicId VARCHAR(45))
BEGIN
    SET @postId = (SELECT pst.post_id FROM ussap.posts AS pst WHERE pst.magic_id = postMagicId);
    INSERT INTO ussap.post_comments (content, created_at, post_id, user_id,magic_id) VALUES (commentText,createdAt,@postId,userId,magicId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_post_comment_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_post_comment_smiley`(IN userId INT, IN commentMagicId VARCHAR(45))
BEGIN
    SET @commentId = (SELECT com.post_comment_id FROM ussap.post_comments com WHERE com.magic_id = commentMagicId);
    INSERT INTO ussap.post_comment_smiley (post_comment_id, user_id) VALUES (@commentId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_post_comments
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_post_comments`(IN postId INT, IN userId INT, IN commentText TEXT, IN createdAt TIMESTAMP)
BEGIN
    INSERT INTO ussap.post_comments (content, created_at, post_id, user_id) VALUES (commentText,createdAt,postId,userId);
    SELECT ussap.post_comments.post_comment_id FROM ussap.post_comments WHERE ussap.post_comments.created_at = createdAt;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_post_smiley
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_post_smiley`(IN userId INT, IN postMagicId VARCHAR(45))
BEGIN
    SET @postId = (SELECT pst.post_id FROM ussap.posts as pst WHERE pst.magic_id = postMagicId);
    INSERT INTO ussap.post_smiley (post_id, user_id) VALUES (@postId,userId);
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_profile
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_profile`(IN userId INT, in emailAddress varchar(45),in userGender char(1),in userDepartment varchar(45), in userCategory varchar(20), in firstName varchar(45), in lastName varchar(45),IN indexNumber VARCHAR(30))
BEGIN
  
  SET @dep_id =(select ussap.department.department_id FROM ussap.department WHERE ussap.department.department_name = userDepartment);
  
    IF (userCategory= 'student')THEN
      insert into ussap.profile(email_address,gender,department_id,category,first_name,last_name,user_id,index_number)
        values(emailAddress,userGender,@dep_id,userCategory,firstName,lastName,userId,indexNumber);
    ELSEIF
      (userCategory= 'lecturer')THEN
      insert into ussap.profile(email_address,gender,department_id,category,first_name,last_name,user_id)
        values(emailAddress,userGender,@dep_id,userCategory,firstName,lastName,userId);
    ELSEIF
      (userCategory= 'alumni')THEN
      insert into ussap.profile(email_address,gender,department_id,category,first_name,last_name,user_id,index_number)
        values(emailAddress,userGender,@dep_id,userCategory,firstName,lastName,userId,indexNumber);
    END IF;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_token
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_token`(IN tokenType VARCHAR(100), IN token CHAR(128), IN tokenSalt CHAR(128), IN tokenHash CHAR(128))
BEGIN
    INSERT INTO ussap.token(token_type, token, token_salt, token_hash) VALUES (tokenType,token,tokenSalt,tokenHash);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_user
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_user`(in userEmail varchar(100), in userPassword char(128), in userSalt CHAR(128), in createdAt TIMESTAMP,IN seriesIdentifier CHAR(128))
BEGIN
    insert into ussap.user(user_email,password,salt,created_at,series_identifier) values (userEmail,userPassword,userSalt,createdAt,seriesIdentifier);
    SELECT ussap.user.user_id from ussap.user where ussap.user.created_at = createdAt and ussap.user.salt = userSalt;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_user_noti
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_user_noti`(IN perpId INT, IN targetId INT ,IN notiText VARCHAR(180), IN targetObjectMagicId VARCHAR(45), IN targetObjectType VARCHAR(45), IN createdAt TIMESTAMP, IN magicId VARCHAR(45))
BEGIN
    INSERT INTO ussap.notification (notification_type, created_at, perp, notification_text,magic_id) 
      VALUES ('user',createdAt,perpId,notiText,magicId);

    SET @noti_id = (SELECT ussap.notification.notification_id FROM ussap.notification WHERE ussap.notification.magic_id = magicId);
    
    INSERT INTO ussap.user_notification (notification_id, user_id, target_object_magic_id, target_object_type) 
      VALUES (@noti_id,targetId,targetObjectMagicId,targetObjectType);
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure insert_user_upload
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_user_upload`(IN userId INT, IN f_size INT, IN f_name TEXT, IN f_type VARCHAR(120),IN createdAt DATETIME, IN url TEXT, IN magcid VARCHAR(45))
BEGIN
    INSERT INTO ussap.uploads (upload_url, created_at, size, file_type, file_name, magic_id) VALUES (url,createdAt,f_size,f_type,f_name,magcid);
      SET @upldId = (SELECT upld.upload_id FROM ussap.uploads AS upld WHERE upld.created_at = createdAt);

    INSERT INTO ussap.user_uploads (upload_id, user_id) VALUES (@upldId,userId);
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure is_group_member
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `is_group_member`(IN userId INT, IN grpId INT)
BEGIN
    SELECT grp_mem.group_member_id FROM ussap.group_members AS grp_mem WHERE grp_mem.user_id = userId AND grp_mem.group_id = grpId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure post_comment_count
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `post_comment_count`(IN postId INT)
BEGIN 
    SELECT com.post_id FROM ussap.post_comments as com WHERE com.post_id = postId;
    END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure post_flag
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `post_flag`(IN magcId VARCHAR(45))
BEGIN 
    SET @flag_num = (SELECT pst.flag FROM ussap.posts as pst WHERE pst.magic_id = magcId);
    set @flag_add = @flag_num +1;
    
    UPDATE ussap.posts as pst SET pst.flag = @flag_add where pst.magic_id = magcId;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure post_smiley_count
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `post_smiley_count`(IN postId INT)
BEGIN 
    SELECT smly.post_id FROM ussap.post_smiley as smly WHERE smly.post_id = postId;
    END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure search_forum
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_forum`(IN queryText TEXT)
BEGIN
    SELECT ussap.forum.magic_id FROM ussap.forum WHERE ussap.forum.category LIKE queryText
    ORDER BY ussap.forum.category ='DESC' ;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure search_groups
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_groups`(IN queryText TEXT)
BEGIN
    SELECT ussap.groups.group_name FROM ussap.groups WHERE ussap.groups.group_name LIKE queryText
    ORDER BY ussap.groups.group_name ASC;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure user_verify
-- -----------------------------------------------------

DELIMITER $$
USE `ussap`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_verify`(in userEmail varchar(45))
BEGIN
    set @usr_count =(select count(ussap.user.user_id) from ussap.user where ussap.user.user_email= userEmail);
    if (@usr_count = 1) then
      select ussap.user.user_id,ussap.user.password, ussap.user.salt from ussap.user where ussap.user.user_email= userEmail;
    elseif (@usr_count = 0) then
      select error_status = 10;

    elseif (@usr_count > 1) then
      select error_status = 11;
    end if;
  END$$

DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
