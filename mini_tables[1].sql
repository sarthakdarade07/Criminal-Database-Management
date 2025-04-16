-- Create the database
CREATE DATABASE IF NOT EXISTS criminal;
USE criminal;

-- Create Criminal table
CREATE TABLE Criminal (
    criminal_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100),
    age DATE.
    crime_id varchar(10).
    FOREIGN KEY (crime_id) REFERENCES Crime_Record(crime_id)
);

-- Create Victim table
CREATE TABLE Victim (
    victim_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100),
    age DATE,
    contact VARCHAR(50)
);

-- Create Crime_Record table
CREATE TABLE Crime_Record (
    crime_id VARCHAR(10) PRIMARY KEY,
    criminal_id VARCHAR(10),
    crime_type VARCHAR(100),
    crime_date DATE,
    victim_id VARCHAR(10),
    FOREIGN KEY (criminal_id) REFERENCES Criminal(criminal_id),
    FOREIGN KEY (victim_id) REFERENCES Victim(victim_id)
);

-- Create Police table
CREATE TABLE Police (
    police_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100),
    police_rank VARCHAR(50)
);

-- Create Case_Details table
CREATE TABLE Case_Details (
    case_id VARCHAR(10) PRIMARY KEY,
    criminal_id VARCHAR(10),
    court_name VARCHAR(100),
    judge_name VARCHAR(100),
    case_status VARCHAR(50),
    police_id VARCHAR(10),
    FOREIGN KEY (criminal_id) REFERENCES Criminal(criminal_id),
    FOREIGN KEY (police_id) REFERENCES Police(police_id)
);

-- Create Court_Hearing table
CREATE TABLE Court_Hearing (
    hearing_id VARCHAR(10) PRIMARY KEY,
    case_id VARCHAR(10),
    hearing_date DATE,
    judge_name VARCHAR(100),
    FOREIGN KEY (case_id) REFERENCES Case_Details(case_id)
);

-- Create Evidence table
CREATE TABLE Evidence (
    evidence_id VARCHAR(10) PRIMARY KEY,
    case_id VARCHAR(10),
    type VARCHAR(100),
    description TEXT,
    location VARCHAR(255),
    FOREIGN KEY (case_id) REFERENCES Case_Details(case_id)
);

-- Create Jail table
CREATE TABLE Jail (
    jail_id VARCHAR(10) PRIMARY KEY,
    cell_number VARCHAR(20),
    criminal_id VARCHAR(10),
    FOREIGN KEY (criminal_id) REFERENCES Criminal(criminal_id)
);

-- Create Lawyer table
CREATE TABLE Lawyer (
    lawyer_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100),
    contact VARCHAR(50),
    case_id VARCHAR(10),
    FOREIGN KEY (case_id) REFERENCES Case_Details(case_id)
);

-- Create Visitors table
CREATE TABLE Visitors (
    visitor_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100),
    contact VARCHAR(50),
    visit_date DATE,
    criminal_id VARCHAR(10),
    FOREIGN KEY (criminal_id) REFERENCES Criminal(criminal_id)
);

-- Create Biometric table
CREATE TABLE Biometric (
    fingerprint_id VARCHAR(10) PRIMARY KEY,
    b_date DATE,
    criminal_id VARCHAR(10),
    FOREIGN KEY (criminal_id) REFERENCES Criminal(criminal_id) ON DELETE CASCADE
    image_path VARCHAR(255),
);

-- Create Login table
CREATE TABLE Login (
    id VARCHAR(50) PRIMARY KEY,
    password VARCHAR(255)
);

--create former ciminal table
create table former_criminals(
    criminal_id varchar (10),
    name varchar(100);
    age date;
    crime_id varchar (100)
);


--trigger for former criminals
delimiter #
create trigger former_cri  before delete
    on criminal
    for each row
    insert into former_criminals values(old.criminal_id,old.name,old.age,old.crime_id);
    end;
    #
    delimiter ;


    --procedure for entering crime record
    DELIMITER //

CREATE PROCEDURE AddCrimeRecord(
  IN p_crime_id VARCHAR(10),
  IN p_criminal_id VARCHAR(10),
  IN p_crime_type VARCHAR(100),
  IN p_crime_date DATE,
  IN p_victim_id VARCHAR(10)
)
BEGIN
  -- Check if referenced criminal and victim exist
  IF EXISTS (SELECT 1 FROM Criminal WHERE criminal_id = p_criminal_id)
     AND EXISTS (SELECT 1 FROM Victim WHERE victim_id = p_victim_id) THEN

    INSERT INTO Crime_Record (crime_id, criminal_id, crime_type, crime_date, victim_id)
    VALUES (p_crime_id, p_criminal_id, p_crime_type, p_crime_date, p_victim_id);

  ELSE
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Criminal ID or Victim ID does not exist.';
  END IF;
END //

DELIMITER ;