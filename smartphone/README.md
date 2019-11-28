## Smartphone

## SQLite

Tutorial: <http://www.sqlitetutorial.net/>

Download and install: <http://www.sqlitetutorial.net/download-install-sqlite/>

SQLite Browser (GUI): <https://sqlitebrowser.org/>

### Create database

Inspired by: <https://alvinalexander.com/android/sqlite-create-table-insert-syntax-examples>

```sh
sqlite3 smartphone.db
.read create.sql
.quit
```

### View tables

```sh
.tables
```

### SQLite JDBC

Tutorial: <http://www.sqlitetutorial.net/sqlite-java/sqlite-jdbc-driver/>

## Gradle

Generic tutorial: <https://spring.io/guides/gs/gradle/>

Tutorial [SQLite JDBC driver with Gradle](https://stackoverflow.com/questions/50377264/using-sqlite-jdbc-driver-in-a-gradle-java-project).

### Setup notes

Generated Eclipse project using `gradle eclipse` command, while having `apply plugin: 'eclipse'` inside the `build.gradle` file. Tutorial: <http://www.thejavageek.com/2015/05/22/create-eclipse-project-with-gradle/>

Got `.gitignore` file from <https://www.gitignore.io/api/eclipse> and <https://www.gitignore.io/api/java%2Cgradle%2Cintellij>.

### Build

```sh
gradle build
```

### Run

```sh
gradle run
```

## Communication

Communication between client and smartphone is done with [Web sockets](https://github.com/TooTallNate/Java-WebSocket).
