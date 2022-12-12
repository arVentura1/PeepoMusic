CREATE DATABASE IF NOT EXISTS peepomusic;
USE peepomusic;
-- 
DROP TABLE IF EXISTS playlist_songs, playlists, usuario_followers, usuario_likes, canciones, usuarios, roles;
-- roles
CREATE TABLE roles (
	id int NOT NULL,
	nombreRol varchar(30) NOT NULL
);
-- tabla usuarios
CREATE TABLE usuarios (
	id int NOT NULL,
	nickName varchar(30) NOT NULL,
	clave varchar(60) NOT NULL,
	rol int DEFAULT 2,
	mail varchar(60) NOT NULL,
	phoneNum varchar(30) NOT NULL,
	biografia varchar(350) NOT NULL,
	fotoPfp varchar(150) NOT NULL DEFAULT 'assets/img/fotos/defaultPfp.png',
	fechaRegistro date DEFAULT CURRENT_DATE,
	numeroSeguidores int NOT NULL DEFAULT 0
	numeroSeguidos int NOT NULL DEFAULT 0
);
-- tabla usuario_likes
CREATE TABLE usuario_likes (
	id int NOT NULL,
	idUsu int NOT NULL,
	idCancion int NOT NULL
);
-- tabla usuario_followers
CREATE TABLE usuario_followers (
	id int NOT NULL,
	idUsuSeguidor int NOT NULL,
	idUsuSeguido int NOT NULL
);
-- tabla canciones
CREATE TABLE canciones (
	id int NOT NULL,
	ubicacion varchar(150) NOT NULL,
	titulo varchar(60) NOT NULL,
	idUsu int NOT NULL,
	fotoPortada varchar(150) NOT NULL DEFAULT 'assets/img/fotos/defaultSongCover.png',
	fechaSubida date DEFAULT CURRENT_DATE,
	numeroLikesCancion int NOT NULL DEFAULT 0
);
-- tabla playlists
CREATE TABLE playlists (
	id int NOT NULL,
	titulo varchar(60) NOT NULL,
	idUsu int NOT NULL,
	fotoPlaylist varchar(150) NOT NULL DEFAULT 'assets/img/fotos/defaultPlaylistCover.png',
	fechaCreacion date DEFAULT CURRENT_DATE,
	numeroLikesPlaylist int NOT NULL DEFAULT 0
);
-- tabla playlist_songs
CREATE TABLE playlist_songs (
	id int NOT NULL,
	idPlaylist int NOT NULL,
	idCancion int NOT NULL
);
-- tabla playlist_likes
CREATE TABLE playlist_likes (
	id int NOT NULL,
	idUsuario int NOT NULL,
	idPlaylist int NOT NULL
);
-- PK
ALTER TABLE roles ADD PRIMARY KEY (id);
ALTER TABLE roles MODIFY id int NOT NULL AUTO_INCREMENT;

ALTER TABLE usuarios ADD PRIMARY KEY (id);
ALTER TABLE usuarios MODIFY id int NOT NULL AUTO_INCREMENT;

ALTER TABLE usuario_likes ADD PRIMARY KEY (id);
ALTER TABLE usuario_likes MODIFY id int NOT NULL AUTO_INCREMENT;

ALTER TABLE usuario_followers ADD PRIMARY KEY (id);
ALTER TABLE usuario_followers MODIFY id int NOT NULL AUTO_INCREMENT;

ALTER TABLE canciones ADD PRIMARY KEY (id);
ALTER TABLE canciones MODIFY id int NOT NULL AUTO_INCREMENT;

ALTER TABLE playlists ADD PRIMARY KEY (id);
ALTER TABLE playlists MODIFY id int NOT NULL AUTO_INCREMENT;

ALTER TABLE playlist_songs ADD PRIMARY KEY (id);
ALTER TABLE playlist_songs MODIFY id int NOT NULL AUTO_INCREMENT;

ALTER TABLE playlist_likes ADD PRIMARY KEY (id);
ALTER TABLE playlist_likes MODIFY id int NOT NULL AUTO_INCREMENT;

-- FK
-- fk del id usuario
ALTER TABLE usuarios 
		ADD CONSTRAINT rolUsuario FOREIGN KEY (rol) 
		REFERENCES roles (id) ON DELETE CASCADE;
		
ALTER TABLE usuario_likes 
		ADD CONSTRAINT idUsuarioLike FOREIGN KEY (idUsu)
		REFERENCES usuarios (id) ON DELETE CASCADE;
		
ALTER TABLE usuario_likes 
		ADD CONSTRAINT idCancionLiked FOREIGN KEY (idCancion)
		REFERENCES canciones (id) ON DELETE CASCADE;
		
ALTER TABLE usuario_followers 
		ADD CONSTRAINT idUsuarioSeguido FOREIGN KEY (idUsuSeguido)
		REFERENCES usuarios (id) ON DELETE CASCADE;
		
ALTER TABLE usuario_followers 
		ADD CONSTRAINT idUsuSeguidor FOREIGN KEY (idUsuSeguidor)
		REFERENCES usuarios (id) ON DELETE CASCADE;

ALTER TABLE canciones
		ADD CONSTRAINT idUsuarioCancion FOREIGN KEY (idUsu)
		REFERENCES usuarios (id) ON DELETE CASCADE;

ALTER TABLE playlists
		ADD CONSTRAINT idUsuarioPlaylist FOREIGN KEY (idUsu)
		REFERENCES usuarios (id) ON DELETE CASCADE;

-- fk del id playlist en playlist_songs
ALTER TABLE playlist_songs 
		ADD CONSTRAINT idPlaylist_songs FOREIGN KEY (idPlaylist)
		REFERENCES playlists (id) ON DELETE CASCADE;

-- fk del id cancion en playlist_songs
ALTER TABLE playlist_songs 
		ADD CONSTRAINT idUsu_songs FOREIGN KEY (idCancion)
		REFERENCES canciones (id) ON DELETE CASCADE;

-- fk del id playlist en playlist_likes
ALTER TABLE playlist_likes
		ADD CONSTRAINT idUsuario_likes FOREIGN KEY (idUsuario)
		REFERENCES usuarios (id) ON DELETE CASCADE;

-- fk del id cancion en playlist_likes
ALTER TABLE playlist_likes
		ADD CONSTRAINT idPlaylist_likes FOREIGN KEY (idPlaylist)
		REFERENCES playlists (id) ON DELETE CASCADE;


INSERT INTO roles VALUES (1,'administrador');
INSERT INTO roles VALUES (2,'usuario');

COMMIT;