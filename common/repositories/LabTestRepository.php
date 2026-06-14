<?php

namespace common\repositories;

/**
 * Lab Test Repository - All SQL queries for tbl_lab_test table
 * Columns: test_id, appt_id, test_name, test_category, status, results, is_abnormal, ordered_date, results_date, notes, created_at, updated_at
 */
class LabTestRepository extends BaseRepository
{
    /**
     * SQL: SELECT lt.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
     * FROM tbl_lab_test lt JOIN tbl_appointment a ON lt.appt_id = a.appt_id JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * ORDER BY lt.ordered_date DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT lt.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
                FROM tbl_lab_test lt 
                JOIN tbl_appointment a ON lt.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                ORDER BY lt.ordered_date DESC";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT lt.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
     * FROM tbl_lab_test lt JOIN tbl_appointment a ON lt.appt_id = a.appt_id JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * WHERE lt.test_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT lt.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
                FROM tbl_lab_test lt 
                JOIN tbl_appointment a ON lt.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE lt.test_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_lab_test (appt_id, test_name, test_category, status, results, is_abnormal, ordered_date, results_date, notes) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_lab_test (appt_id, test_name, test_category, status, results, is_abnormal, ordered_date, results_date, notes) 
                VALUES (:appt_id, :test_name, :test_category, :status, :results, :is_abnormal, :ordered_date, :results_date, :notes)";
        
        return $this->insert($sql, [
            ':appt_id' => $data['appt_id'] ?? null,
            ':test_name' => $data['test_name'],
            ':test_category' => $data['test_category'] ?? null,
            ':status' => $data['status'] ?? 'ordered',
            ':results' => $data['results'] ?? null,
            ':is_abnormal' => $data['is_abnormal'] ?? 0,
            ':ordered_date' => $data['ordered_date'],
            ':results_date' => $data['results_date'] ?? null,
            ':notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_lab_test SET ... WHERE test_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_lab_test 
                SET test_name = :test_name, test_category = :test_category, status = :status,
                    results = :results, is_abnormal = :is_abnormal, results_date = :results_date, notes = :notes
                WHERE test_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':test_name' => $data['test_name'],
            ':test_category' => $data['test_category'] ?? null,
            ':status' => $data['status'] ?? 'ordered',
            ':results' => $data['results'] ?? null,
            ':is_abnormal' => $data['is_abnormal'] ?? 0,
            ':results_date' => $data['results_date'] ?? null,
            ':notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_lab_test WHERE test_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_lab_test WHERE test_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_lab_test WHERE appt_id = :appt_id ORDER BY ordered_date DESC
     */
    public function findByAppointment(int $apptId): array
    {
        $sql = "SELECT * FROM tbl_lab_test WHERE appt_id = :appt_id ORDER BY ordered_date DESC";
        return $this->queryAll($sql, [':appt_id' => $apptId]);
    }

    /**
     * SQL: SELECT * FROM tbl_lab_test WHERE appt_id = :appt_id AND status = :status
     */
    public function findByAppointmentAndStatus(int $apptId, string $status): array
    {
        $sql = "SELECT * FROM tbl_lab_test WHERE appt_id = :appt_id AND status = :status";
        return $this->queryAll($sql, [':appt_id' => $apptId, ':status' => $status]);
    }

    /**
     * SQL: SELECT lt.* FROM tbl_lab_test lt JOIN tbl_appointment a ON lt.appt_id = a.appt_id WHERE a.patient_id = :patient_id ORDER BY lt.ordered_date DESC
     */
    public function findByPatient(int $patientId): array
    {
        $sql = "SELECT lt.*, a.appointment_date, d.last_name AS doctor_lname
                FROM tbl_lab_test lt 
                JOIN tbl_appointment a ON lt.appt_id = a.appt_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE a.patient_id = :patient_id 
                ORDER BY lt.ordered_date DESC";
        return $this->queryAll($sql, [':patient_id' => $patientId]);
    }
}