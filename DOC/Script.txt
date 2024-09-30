Drop table if exists UTILISATEUR;
Drop table if exists PLACE;
Drop table if exists RESERVATION;

CREATE TABLE UTILISATEUR(
   Num_Util SMALLINT,
   Nom_Util VARCHAR(30),
   Prenom_Util VARCHAR(50),
   Immatriculation VARCHAR(10),
   Statut Varchar(25),
   Num_Attente SMALLINT,
   Password_hach VARCHAR(30),
   PRIMARY KEY(Num_Util)
);

CREATE TABLE PLACE(
   Num_place SMALLINT,
   PRIMARY KEY(Num_place)
);

CREATE TABLE RESERVATION(
   Id_Reserv SMALLINT,
   Date_Debut DATE,
   Date_Fin DATE,
   PRIMARY KEY(Id_Reserv),
);
