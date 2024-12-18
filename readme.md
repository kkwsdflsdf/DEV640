# How To Run

### New mysql db

```
CREATE DATABASE dev640app;
USE dev640app;

CREATE TABLE members (
    user VARCHAR(16) NOT NULL,
    pass VARCHAR(16) NOT NULL,
    INDEX(user(6))
);

CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auth VARCHAR(16),
    recip VARCHAR(16),
    pm CHAR(1),
    time INT UNSIGNED,
    message VARCHAR(4096),
    INDEX(auth(6)),
    INDEX(recip(6))
);

CREATE TABLE friends (
    user VARCHAR(16),
    friend VARCHAR(16),
    INDEX(user(6)),
    INDEX(friend(6))
);

CREATE TABLE profiles (
    user VARCHAR(16),
    text VARCHAR(4096),
    INDEX(user(6))
);
```

### Enable GD

Open your `\Ampps\php\php.ini`
Find this:

```
;extension=gd
```

Delete the `;`, making it:

```
extension=gd
```

# update 1

Add following to database:

```
ALTER TABLE profiles ADD location VARCHAR(255);
ALTER TABLE profiles ADD interests VARCHAR(255);
```

# update 2 - like/dislike function

Add following to database:

```
ALTER TABLE messages ADD likes INT DEFAULT 0;
ALTER TABLE messages ADD dislikes INT DEFAULT 0;
```

### show profile image
```
ALTER TABLE profiles ADD COLUMN image VARCHAR(255);
```

### add chat option
```
CREATE TABLE chat (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    from VARCHAR(16),
    to VARCHAR(16),
    pm CHAR(1),
    time INT UNSIGNED,
    message TEXT
);
```
