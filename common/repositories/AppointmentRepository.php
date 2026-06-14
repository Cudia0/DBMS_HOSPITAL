<?php

namespace common\repositories;

/**
 * Appointment Repository - All SQL queries for tbl_appointment table
 * Columns: appt_id, dr_id, patient_id, recep_id, symptoms_list, appointment_date, appointment_time, status, created_at, updated_at
 */
class AppointmentRepository extends BaseRepository
{
    /**
     * SQL: SELECT a.*, p.first_name AS patient_fname, p.last_name AS patient_lname, d.first_name AS doctor_fname, d.last_name AS doctor_lname 
     * FROM tbl_appointment a 
     * LEFT JOIN tbl_patient p ON a.patient_id = p.patient_id 
     * LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * ORDER BY a.appointment_date DESC, a.appointment_time DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT a.*, p.first_name AS patient_fname, p.last_name AS patient_lname, 
                       d.first_name AS doctor_fname, d.last_name AS doctor_lname
                FROM tbl_appointment a 
                LEFT JOIN tbl_patient p ON a.patient_id = p.patient_id 
                LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT a.*, p.first_name AS patient_fname, p.last_name AS patient_lname, d.first_name AS doctor_fname, d.last_name AS doctor_lname 
     * FROM tbl_appointment a LEFT JOIN tbl_patient p ON a.patient_id = p.patient_id LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * WHERE a.appt_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT a.*, p.first_name AS patient_fname, p.last_name AS patient_lname, 
                       d.first_name AS doctor_fname, d.last_name AS doctor_lname
                FROM tbl_appointment a 
                LEFT JOIN tbl_patient p ON a.patient_id = p.patient_id 
                LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE a.appt_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_appointment (dr_id, patient_id, recep_id, symptoms_list, appointment_date, appointment_time, status) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_appointment (dr_id, patient_id, recep_id, symptoms_list, appointment_date, appointment_time, status) 
                VALUES (:dr_id, :patient_id, :recep_id, :symptoms_list, :appointment_date, :appointment_time, :status)";
        
        return $this->insert($sql, [
            ':dr_id' => $data['dr_id'] ?? null,
            ':patient_id' => $data['patient_id'] ?? null,
            ':recep_id' => $data['recep_id'] ?? null,
            ':symptoms_list' => $data['symptoms_list'] ?? null,
            ':appointment_date' => $data['appointment_date'] ?? null,
            ':appointment_time' => $data['appointment_time'] ?? null,
            ':status' => $data['status'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_appointment SET ... WHERE appt_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_appointment 
                SET dr_id = :dr_id, patient_id = :patient_id, recep_id = :recep_id,
                    symptoms_list = :symptoms_list, appointment_date = :appointment_date,
                    appointment_time = :appointment_time, status = :status
                WHERE appt_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':dr_id' => $data['dr_id'] ?? null,
            ':patient_id' => $data['patient_id'] ?? null,
            ':recep_id' => $data['recep_id'] ?? null,
            ':symptoms_list' => $data['symptoms_list'] ?? null,
            ':appointment_date' => $data['appointment_date'] ?? null,
            ':appointment_time' => $data['appointment_time'] ?? null,
            ':status' => $data['status'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_appointment SET status = :status, recep_id = :recep_id, appointment_date = :date, appointment_time = :time WHERE appt_id = :id
     */
    public function accept(int $id, string $status, string $date, string $time, ?int $recepId): int
    {
        $sql = "UPDATE tbl_appointment SET status = :status, recep_id = :recep_id, appointment_date = :date, appointment_time = :time WHERE appt_id = :id";
        return $this->execute($sql, [
            ':id' => $id, ':status' => $status, ':date' => $date, ':time' => $time, ':recep_id' => $recepId,
        ]);
    }

    /**
     * SQL: UPDATE tbl_appointment SET status = :status WHERE appt_id = :id
     */
    public function updateStatus(int $id, string $status): int
    {
        $sql = "UPDATE tbl_appointment SET status = :status WHERE appt_id = :id";
        return $this->execute($sql, [':id' => $id, ':status' => $status]);
    }

    /**
     * SQL: DELETE FROM tbl_appointment WHERE appt_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_appointment WHERE appt_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_appointment WHERE patient_id = :patient_id ORDER BY appointment_date DESC
     */
    public function findByPatient(int $patientId): array
    {
        $sql = "SELECT a.*, d.first_name AS doctor_fname, d.last_name AS doctor_lname, d.specialization
                FROM tbl_appointment a 
                LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE a.patient_id = :patient_id 
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        return $this->queryAll($sql, [':patient_id' => $patientId]);
    }

    /**
     * SQL: SELECT * FROM tbl_appointment WHERE dr_id = :dr_id ORDER BY appointment_date DESC
     */
    public function findByDoctor(int $doctorId): array
    {
        $sql = "SELECT a.*, p.first_name AS patient_fname, p.last_name AS patient_lname
                FROM tbl_appointment a 
                LEFT JOIN tbl_patient p ON a.patient_id = p.patient_id 
                WHERE a.dr_id = :dr_id 
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        return $this->queryAll($sql, [':dr_id' => $doctorId]);
    }

    /**
     * SQL: SELECT * FROM tbl_appointment WHERE appointment_date = :date AND status IS NOT NULL ORDER BY appointment_time
     */
    public function findToday(string $date): array
    {
        $sql = "SELECT a.*, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
                FROM tbl_appointment a 
                LEFT JOIN tbl_patient p ON a.patient_id = p.patient_id 
                LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE a.appointment_date = :date AND a.status IS NOT NULL 
                ORDER BY a.appointment_time";
        return $this->queryAll($sql, [':date' => $date]);
    }

    /**
     * SQL: SELECT * FROM tbl_appointment WHERE (status IS NULL OR status = '') ORDER BY created_at DESC
     */
    public function findPending(): array
    {
        $sql = "SELECT a.*, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
                FROM tbl_appointment a 
                LEFT JOIN tbl_patient p ON a.patient_id = p.patient_id 
                LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE (a.status IS NULL OR a.status = '') 
                ORDER BY a.created_at DESC";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_appointment
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_appointment";
        return (int) $this->queryScalar($sql);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_appointment WHERE status = :status
     */
    public function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_appointment WHERE status = :status";
        return (int) $this->queryScalar($sql, [':status' => $status]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_appointment WHERE appointment_date = :date AND status IS NOT NULL
     */
    public function countToday(string $date): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_appointment WHERE appointment_date = :date AND status IS NOT NULL";
        return (int) $this->queryScalar($sql, [':date' => $date]);
    }
}