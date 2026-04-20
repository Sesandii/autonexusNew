
CREATE DATABASE db_name;
SHOW DATABASES;
USE db_name;
DROP DATABASE db_name;

-- =========================
-- TABLE COMMANDS
-- =========================
CREATE TABLE table_name (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    age INT
);

SHOW TABLES;
DESCRIBE table_name;
DROP TABLE table_name;

-- =========================
-- INSERT DATA
-- =========================
INSERT INTO table_name (name, age)
VALUES ('Sandi', 22);

-- =========================
-- SELECT DATA
-- =========================
SELECT * FROM table_name;
SELECT name, age FROM table_name;
SELECT * FROM table_name WHERE age > 18;
SELECT * FROM table_name WHERE name LIKE 'A%';

-- =========================
-- UPDATE DATA
-- =========================
UPDATE table_name
SET name = 'New Name'
WHERE id = 1;

-- =========================
-- DELETE DATA
-- =========================
DELETE FROM table_name
WHERE id = 1;

-- =========================
-- ALTER TABLE
-- =========================
ALTER TABLE table_name ADD email VARCHAR(100);
ALTER TABLE table_name MODIFY age INT;
ALTER TABLE table_name DROP COLUMN email;

-- =========================
-- SORT & LIMIT
-- =========================
SELECT * FROM table_name ORDER BY age ASC;
SELECT * FROM table_name ORDER BY age DESC;
SELECT * FROM table_name LIMIT 5;

-- =========================
-- CONDITIONS
-- =========================
SELECT * FROM table_name WHERE age = 20;
SELECT * FROM table_name WHERE age > 18;
SELECT * FROM table_name WHERE age BETWEEN 18 AND 30;
SELECT * FROM table_name WHERE name LIKE '%a%';

-- =========================
-- LOGICAL OPERATORS
-- =========================
SELECT * FROM table_name WHERE age > 18 AND age < 30;
SELECT * FROM table_name WHERE age = 20 OR age = 25;
SELECT * FROM table_name WHERE NOT age = 18;

-- =========================
-- JOINS
-- =========================
SELECT users.name, orders.amount
FROM users
INNER JOIN orders ON users.id = orders.user_id;

SELECT users.name, orders.amount
FROM users
LEFT JOIN orders ON users.id = orders.user_id;

SELECT users.name, orders.amount
FROM users
RIGHT JOIN orders ON users.id = orders.user_id;

-- =========================
-- AGGREGATE FUNCTIONS
-- =========================
SELECT COUNT(*) FROM table_name;
SELECT SUM(amount) FROM table_name;
SELECT AVG(age) FROM table_name;
SELECT MIN(age) FROM table_name;
SELECT MAX(age) FROM table_name;

-- =========================
-- GROUP BY
-- =========================
SELECT age, COUNT(*)
FROM table_name
GROUP BY age;

-- =========================
-- HAVING
-- =========================
SELECT age, COUNT(*)
FROM table_name
GROUP BY age
HAVING COUNT(*) > 1;

-- =========================
-- PRIMARY & FOREIGN KEY
-- =========================
CREATE TABLE orders (
    id INT PRIMARY KEY,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =========================
-- INDEX
-- =========================
CREATE INDEX idx_name
ON table_name(name);



//register fom
<label class="field with-icon">
        <span class="icon">📱</span>
        <input type="radio" name="sex" value="male" placeholder="Male"/>
        <input type="radio" name="sex" value="female" placeholder="Female"/>
      </label>