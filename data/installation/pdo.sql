CREATE TABLE oauth_client (
  client_id     VARCHAR(80)  NOT NULL,
  client_secret VARCHAR(80)  NULL,
  name          VARCHAR(256) NOT NULL,
  redirect_uri  TEXT         NULL,
  scopes        TINYTEXT     NULL,
  PRIMARY KEY (client_id)
) ENGINE = InnoDB;

CREATE TABLE oauth_access_token (
  id          VARCHAR(80) NOT NULL,
  client_id   VARCHAR(80) NOT NULL,
  user_id     VARCHAR(64) NOT NULL,
  expire_date DATETIME    NOT NULL,
  scopes      TINYTEXT    NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB;

CREATE TABLE oauth_authorization_code (
  id           VARCHAR(80)  NOT NULL,
  client_id    VARCHAR(80)  NOT NULL,
  user_id      VARCHAR(64)  NOT NULL,
  expire_date  DATETIME     NOT NULL,
  redirect_uri VARCHAR(256) NULL,
  scopes       TINYTEXT     NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB;

CREATE TABLE oauth_refresh_token (
  id              VARCHAR(80) NOT NULL,
  access_token_id VARCHAR(80) NOT NULL,
  expire_date     DATETIME    NOT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB;

ALTER TABLE oauth_access_token
  ADD CONSTRAINT access_token_client FOREIGN KEY (client_id) REFERENCES oauth_client (client_id) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE oauth_authorization_code
  ADD CONSTRAINT authorization_code_client FOREIGN KEY (client_id) REFERENCES oauth_client (client_id) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE oauth_refresh_token
  ADD CONSTRAINT refresh_token_access_token FOREIGN KEY (access_token_id) REFERENCES oauth_access_token (id) ON DELETE CASCADE ON UPDATE NO ACTION;
