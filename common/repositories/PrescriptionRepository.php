<?php

namespace common\repositories;

/**
 * Prescription Repository - All SQL queries for tbl_prescription table
 * Columns: prescription_id, appt_id, prescription_date, dosage_instructions, duration_days, notes, created_at, updated_at
 */
class PrescriptionRepository extends BaseRepository
{
    /**
     * SQL: SELECT pr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
     * FROM tbl_prescription pr JOIN tbl_appointment a ON pr.appt_id = a.appt_id JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * ORDER BY pr.prescription_date DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT pr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
                FROM tbl_prescription pr 
                JOIN tbl_appointment a ON pr.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                ORDER BY pr.prescription_date DESC";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT pr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.first_name AS doctor_fname, d.last_name AS doctor_lname
     * FROM tbl_prescription pr JOIN tbl_appointment a ON pr.appt_id = a.appt_id JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * WHERE pr.prescription_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT pr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, 
                       d.first_name AS doctor_fname, d.last_name AS doctor_lname
                FROM tbl_prescription pr 
                JOIN tbl_appointment a ON pr.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE pr.prescription_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_prescription (appt_id, dosage_instructions, duration_days, notes, prescription_date) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_prescription (appt_id, dosage_instructions, duration_days, notes, prescription_date) 
                VALUES (:appt_id, :dosage_instructions, :duration_days, :notes, :prescription_date)";
        
        return $this->insert($sql, [
            ':appt_id' => $data['appt_id'] ?? null,
            ':dosage_instructions' => $data['dosage_instructions'] ?? null,
            ':duration_days' => $data['duration_days'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':prescription_date' => $data['prescription_date'],
        ]);
    }

    /**
     * SQL: UPDATE tbl_prescription SET ... WHERE prescription_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_prescription 
                SET dosage_instructions = :dosage_instructions, duration_days = :duration_days, notes = :notes
                WHERE prescription_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':dosage_instructions' => $data['dosage_instructions'] ?? null,
            ':duration_days' => $data['duration_days'] ?? null,
            ':notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_prescription WHERE prescription_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_prescription WHERE prescription_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_prescription WHERE appt_id = :appt_id
     */
    public function findByAppointment(int $apptId): array|false
    {
        $sql = "SELECT * FROM tbl_prescription WHERE appt_id = :appt_id";
        return $this->queryOne($sql, [':appt_id' => $apptId]);
    }

    /**
     * SQL: SELECT pr.* FROM tbl_prescription pr JOIN tbl_appointment a ON pr.appt_id = a.appt_id WHERE a.patient_id = :patient_id ORDER BY pr.prescription_date DESC
     */
    public function findByPatient(int $patientId): array
    {
        $sql = "SELECT pr.*, a.appointment_date, d.last_name AS doctor_lname
                FROM tbl_prescription pr 
                JOIN tbl_appointment a ON pr.appt_id = a.appt_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE a.patient_id = :patient_id 
                ORDER BY pr.prescription_date DESC";
        return $this->queryAll($sql, [':patient_id' => $patientId]);
    }

    /**
     * SQL: SELECT pr.* FROM tbl_prescription pr JOIN tbl_appointment a ON pr.appt_id = a.appt_id WHERE a.dr_id = :doctor_id ORDER BY pr.prescription_date DESC
     */
    public function findByDoctor(int $doctorId): array
    {
        $sql = "SELECT pr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname
                FROM tbl_prescription pr 
                JOIN tbl_appointment a ON pr.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                WHERE a.dr_id = :doctor_id 
                ORDER BY pr.prescription_date DESC";
        return $this->queryAll($sql, [':doctor_id' => $doctorId]);
    }
}