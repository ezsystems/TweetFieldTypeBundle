CREATE  TABLE eztweet (
  url VARCHAR(100) NOT NULL ,
  author_url VARCHAR(100) NULL ,
  contents TEXT NULL ,
  PRIMARY KEY (url) ,
  UNIQUE INDEX url_UNIQUE (url ASC)
)  ENGINE=InnoDB;
