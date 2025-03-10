-- Table des utilisateurs

CREATE TABLE users (
id SERIAL PRIMARY KEY,
name VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
remember_token VARCHAR(100),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des places de parking

CREATE TABLE parking_spots (
id SERIAL PRIMARY KEY,
number VARCHAR(50) NOT NULL UNIQUE,
location VARCHAR(255) NOT NULL,
status VARCHAR(20) NOT NULL DEFAULT 'available',
description TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des réservations

CREATE TABLE reservations (
id SERIAL PRIMARY KEY,
user_id INTEGER NOT NULL,
parking_spot_id INTEGER NOT NULL,
start_time TIMESTAMP NOT NULL,
end_time TIMESTAMP,
duration INTEGER NOT NULL, -- en minutes
status VARCHAR(20) NOT NULL DEFAULT 'active',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (parking_spot_id) REFERENCES parking_spots(id) ON DELETE CASCADE
);

-- Table de la liste d'attente

CREATE TABLE waiting_list (
id SERIAL PRIMARY KEY,
user_id INTEGER NOT NULL,
parking_spot_id INTEGER NOT NULL,
position INTEGER NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (parking_spot_id) REFERENCES parking_spots(id) ON DELETE CASCADE,
UNIQUE (user_id, parking_spot_id) -- Un utilisateur ne peut être qu'une fois dans la liste d'attente pour une place spécifique
);

-- Table des notifications

CREATE TABLE notifications (
id SERIAL PRIMARY KEY,
user_id INTEGER NOT NULL,
type VARCHAR(50) NOT NULL,
message TEXT NOT NULL,
data JSONB,
read_at TIMESTAMP,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);