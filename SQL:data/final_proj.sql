

DROP SCHEMA IF EXISTS final_proj CASCADE;
--DROP TABLE IF EXISTS final_proj CASCADE; --list tables 

CREATE SCHEMA final_proj;
SET search_path = final_proj, public;


-- Table: final_proj.user_info
-- Columns:
--    username          - The username for the account, supplied during registration.
--    registration_date - The date the user registered. Set automatically.
--    description       - A user-supplied description.
CREATE TABLE final_proj.user_info (
        username                VARCHAR(30) PRIMARY KEY,
        registration_date       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        description             VARCHAR(500)
);


-- Table: final_proj.authentication
-- Columns:
--    username      - The username tied to the authentication info.
--    password_hash - The hash of the user's password + salt. Expected to be SHA1.
--    salt          - The salt to use. Expected to be a SHA1 hash of a random input.
CREATE TABLE final_proj.authentication (
        username        VARCHAR(30) PRIMARY KEY,
        password_hash   CHAR(40) NOT NULL,
        salt            CHAR(40) NOT NULL,
        FOREIGN KEY (username) REFERENCES final_proj.user_info(username)
);

-- Table: lab8.log
-- Columns:
--    log_id     - A unique ID for the log entry. Set by a sequence.
--    username   - The user whose action generated this log entry.
--    ip_address - The IP address of the user at the time the log was entered.
--    log_date   - The date of the log entry. Set automatically by a default value.
--    action     - What the user did to generate a log entry (i.e., "logged in").
CREATE TABLE final_proj.log (
        log_id          SERIAL PRIMARY KEY,
        username        VARCHAR(30) NOT NULL REFERENCES final_proj.user_info,
        ip_address      VARCHAR(15) NOT NULL,
        log_date        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        action          VARCHAR(50) NOT NULL
);

CREATE INDEX log_log_id_index ON final_proj.log (username);

-- Table: final_proj.movie
-- Columns:
--     title		- The title of the movie.
--     votes 		- The amount of votes users have given the movie
--     rank         - The current rank of the movie
CREATE TABLE final_proj.movie (
        rank			INTEGER     NOT NULL,
        rating			REAL        NOT NULL,
        title			VARCHAR(100) PRIMARY KEY,
        votes			VARCHAR(20) NOT NULL,
        director		VARCHAR(100) NOT NULL
);

-- Table: final_proj.user_favorites_movie
-- Columns:
--     like_or_dislike		-  A boolean value whether a certain user likes, or dislikes as movie, or null
CREATE TABLE final_proj.user_favorites_movie (
       title			VARCHAR(100) NOT NULL,
       username			VARCHAR(30) NOT NULL,
       like_or_dislike  BOOLEAN,
       FOREIGN KEY (title) REFERENCES final_proj.movie(title),
       FOREIGN KEY (username) REFERENCES final_proj.user_info(username)   			
);

CREATE TABLE final_proj.user_reviews_movie (
      title			VARCHAR(100) NOT NULL,
      username	    VARCHAR(30) NOT NULL,
      review        VARCHAR(5000000),
      FOREIGN KEY (title) REFERENCES final_proj.movie(title),
      FOREIGN KEY (username) REFERENCES final_proj.user_info(username)

);

