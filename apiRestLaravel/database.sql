CREATE DATABASE IF NOT EXISTS api_rest_blog;
USE api_rest_blog;
CREATE TABLE Usuario(
		id INT(255) auto_increment NOT NULL
		,nombre VARCHAR(100) NOT NULL
		,apellido VARCHAR(100) NOT NULL
		,img_usuario VARCHAR(200) 
		,rol INT(2)
		,correo VARCHAR(100) NOT NULL
		,contrasena VARCHAR(100) NOT NULL
		,descripcion TEXT
		,fec_creacion DATETIME DEFAULT NULL 
		,ultima_act DATETIME DEFAULT NULL 
		,remember_token VARCHAR(255)
		,estado ENUM('ACTI','INAC')

		,CONSTRAINT pk_usuario PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE Categoria(
		id INT(255) auto_increment NOT NULL
		, nom_cat VARCHAR(50)
		,fec_creacion DATETIME DEFAULT NULL 
		,ultima_act DATETIME DEFAULT NULL
		,estado ENUM('ACTI','INAC')

		,CONSTRAINT pk_categoria PRIMARY KEY(id)

)ENGINE=InnoDb;

CREATE TABLE Publicacion(
		id INT(255) auto_increment NOT NULL
		,id_usuario INT(255) NOT NULL
		,id_categoria INT(255) NOT NULL
		,nombre_pub VARCHAR(50) NOT NULL
		,contenido TEXT NOT NULL
		,img_usuario VARCHAR(200) 
		,fec_creacion DATETIME DEFAULT NULL 
		,ultima_act DATETIME DEFAULT NULL
		,estado ENUM('ACTI','INAC')

		,CONSTRAINT pk_categoria PRIMARY KEY(id)	
		,CONSTRAINT fk_usuario_publicacion FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
		,CONSTRAINT fk_categoria_publicacion FOREIGN KEY (id_categoria) REFERENCES Categoria(id)
)ENGINE=InnoDb;

CREATE TABLE rol(
		 id INT(255) auto_increment NOT NULL
		,descripcion VARCHAR(255) NOT NULL
		,estado ENUM('ACTI','INAC')

		,CONSTRAINT pk_rol PRIMARY KEY(id)	
		,CONSTRAINT fk_usuario_rol FOREIGN KEY (id) REFERENCES users(rol)
)ENGINE=InnoDb;
