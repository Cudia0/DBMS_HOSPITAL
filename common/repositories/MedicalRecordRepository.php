<?php

namespace common\repositories;

/**
 * Medical Record Repository - All SQL queries for tbl_medical_record table
 * Columns: record_id, appt_id, diagnosis, treatment_plan, vital_signs, notes, record_date, created_at, updated_at
 */
class MedicalRecordRepository extends BaseRepository
{
    /**
     * SQL: SELECT mr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
     * FROM tbl_medical_record mr 
     * JOIN tbl_appointment a ON mr.appt_id = a.appt_id 
     * JOIN tbl_patient p ON a.patient_id = p.patient_id 
     * JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * ORDER BY mr.record_date DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT mr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
                FROM tbl_medical_record mr 
                JOIN tbl_appointment a ON mr.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                ORDER BY mr.record_date DESC";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT mr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.first_name AS doctor_fname, d.last_name AS doctor_lname
     * FROM tbl_medical_record mr JOIN tbl_appointment a ON mr.appt_id = a.appt_id JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * WHERE mr.record_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT mr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, 
                       d.first_name AS doctor_fname, d.last_name AS doctor_lname, d.specialization
                FROM tbl_medical_record mr 
                JOIN tbl_appointment a ON mr.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE mr.record_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_medical_record (appt_id, diagnosis, treatment_plan, vital_signs, notes, record_date) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_medical_record (appt_id, diagnosis, treatment_plan, vital_signs, notes, record_date) 
                VALUES (:appt_id, :diagnosis, :treatment_plan, :vital_signs, :notes, :record_date)";
        
        return $this->insert($sql, [
            ':appt_id' => $data['appt_id'],
            ':diagnosis' => $data['diagnosis'] ?? null,
            ':treatment_plan' => $data['treatment_plan'] ?? null,
            ':vital_signs' => $data['vital_signs'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':record_date' => $data['record_date'],
        ]);
    }

    /**
     * SQL: UPDATE tbl_medical_record SET ... WHERE record_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_medical_record 
                SET diagnosis = :diagnosis, treatment_plan = :treatment_plan, vital_signs = :vital_signs, notes = :notes
                WHERE record_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':diagnosis' => $data['diagnosis'] ?? null,
            ':treatment_plan' => $data['treatment_plan'] ?? null,
            ':vital_signs' => $data['vital_signs'] ?? null,
            ':notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_medical_record WHERE record_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_medical_record WHERE record_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_medical_record WHERE appt_id = :appt_id
     */
    public function findByAppointment(int $apptId): array|false
    {
        $sql = "SELECT * FROM tbl_medical_record WHERE appt_id = :appt_id";
        return $this->queryOne($sql, [':appt_id' => $apptId]);
    }

    /**
     * SQL: SELECT mr.* FROM tbl_medical_record mr JOIN tbl_appointment a ON mr.appt_id = a.appt_id WHERE a.patient_id = :patient_id ORDER BY mr.record_date DESC
     */
    public function findByPatient(int $patientId): array
    {
        $sql = "SELECT mr.*, a.appointment_date, d.last_name AS doctor_lname
                FROM tbl_medical_record mr 
                JOIN tbl_appointment a ON mr.appt_id = a.appt_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE a.patient_id = :patient_id 
                ORDER BY mr.record_date DESC";
        return $this->queryAll($sql, [':patient_id' => $patientId]);
    }

    /**
     * SQL: SELECT mr.* FROM tbl_medical_record mr JOIN tbl_appointment a ON mr.appt_id = a.appt_id WHERE a.dr_id = :doctor_id ORDER BY mr.record_date DESC
     */
    public function findByDoctor(int $doctorId): array
    {
        $sql = "SELECT mr.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname
                FROM tbl_medical_record mr 
                JOIN tbl_appointment a ON mr.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                WHERE a.dr_id = :doctor_id 
                ORDER BY mr.record_date DESC";
        return $this->queryAll($sql, [':doctor_id' => $doctorId]);
    }
}