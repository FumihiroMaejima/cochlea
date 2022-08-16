CREATE DATABASE IF NOT EXISTS cochlea CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON cochlea.* TO 'root'@'%';

FLUSH PRIVILEGES;

-- slaveアカウント
CREATE USER IF NOT EXISTS 'repl'@'%' IDENTIFIED BY 'password';
GRANT REPLICATION SLAVE ON *.* TO 'repl'@'%';

FLUSH PRIVILEGES;

-- logs DB
CREATE DATABASE IF NOT EXISTS cochlea_logs CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
GRANT ALL PRIVILEGES ON cochlea_logs.* TO 'root'@'%';

FLUSH PRIVILEGES;

-- user DB1
CREATE DATABASE IF NOT EXISTS cochlea_user1 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
GRANT ALL PRIVILEGES ON cochlea_user1.* TO 'root'@'%';

FLUSH PRIVILEGES;

-- user DB2
CREATE DATABASE IF NOT EXISTS cochlea_user2 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
GRANT ALL PRIVILEGES ON cochlea_user2.* TO 'root'@'%';

FLUSH PRIVILEGES;

-- user DB3
CREATE DATABASE IF NOT EXISTS cochlea_user3 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
GRANT ALL PRIVILEGES ON cochlea_user3.* TO 'root'@'%';

FLUSH PRIVILEGES;

-- testing DB
CREATE DATABASE IF NOT EXISTS cochlea_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
GRANT ALL PRIVILEGES ON cochlea_testing.* TO 'root'@'%';

FLUSH PRIVILEGES;
