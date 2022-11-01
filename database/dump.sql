CREATE TABLE User
(
    id       INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    email    VARCHAR(255)       NOT NULL,
    password VARCHAR(255)       NOT NULL
);

CREATE TABLE Post
(
    id          INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    title       VARCHAR(255)       NOT NULL,
    content     TEXT,
    publishedAt DATETIME,
    user_id     INT                NOT NULL
)
