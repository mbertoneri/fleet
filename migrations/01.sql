CREATE TABLE fleet
(
    id      varchar(255) NOT NULL,
    user_id varchar(255) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

CREATE table vehicle
(
    id           varchar(255) NOT NULL,
    plate_number varchar(50)  NOT NULL UNIQUE,
    type         varchar(50) NOT NULL,
    PRIMARY KEY (id)
);

CREATE table fleet_vehicle
(
    fleet_id   varchar(255) NOT NULL,
    vehicle_id varchar(255) NOT NULL,
    PRIMARY KEY (fleet_id,vehicle_id),
    FOREIGN KEY (fleet_id) REFERENCES fleet (id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) ON DELETE CASCADE
);