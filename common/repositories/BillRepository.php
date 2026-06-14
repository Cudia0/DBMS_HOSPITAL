<?php

namespace common\repositories;

/**
 * Bill Repository - All SQL queries for tbl_bill table
 * Columns: bill_id, appt_id, payment_status, payment_method, dr_fee, totalm_price, total_amount, bill_date, created_at, updated_at
 */
class BillRepository extends BaseRepository
{
    /**
     * SQL: SELECT b.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
     * FROM tbl_bill b JOIN tbl_appointment a ON b.appt_id = a.appt_id JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * ORDER BY b.bill_date DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT b.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.last_name AS doctor_lname
                FROM tbl_bill b 
                JOIN tbl_appointment a ON b.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                ORDER BY b.bill_date DESC";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT b.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, d.first_name AS doctor_fname, d.last_name AS doctor_lname, d.specialization
     * FROM tbl_bill b JOIN tbl_appointment a ON b.appt_id = a.appt_id JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id 
     * WHERE b.bill_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT b.*, a.appointment_date, p.first_name AS patient_fname, p.last_name AS patient_lname, 
                       d.first_name AS doctor_fname, d.last_name AS doctor_lname, d.specialization
                FROM tbl_bill b 
                JOIN tbl_appointment a ON b.appt_id = a.appt_id 
                JOIN tbl_patient p ON a.patient_id = p.patient_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE b.bill_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_bill (appt_id, payment_status, payment_method, dr_fee, totalm_price, total_amount, bill_date) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_bill (appt_id, payment_status, payment_method, dr_fee, totalm_price, total_amount, bill_date) 
                VALUES (:appt_id, :payment_status, :payment_method, :dr_fee, :totalm_price, :total_amount, :bill_date)";
        
        return $this->insert($sql, [
            ':appt_id' => $data['appt_id'] ?? null,
            ':payment_status' => $data['payment_status'] ?? 'pending',
            ':payment_method' => $data['payment_method'] ?? null,
            ':dr_fee' => $data['dr_fee'] ?? 0,
            ':totalm_price' => $data['totalm_price'] ?? 0,
            ':total_amount' => $data['total_amount'] ?? 0,
            ':bill_date' => $data['bill_date'],
        ]);
    }

    /**
     * SQL: UPDATE tbl_bill SET ... WHERE bill_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_bill 
                SET payment_status = :payment_status, payment_method = :payment_method,
                    dr_fee = :dr_fee, totalm_price = :totalm_price, total_amount = :total_amount
                WHERE bill_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':payment_status' => $data['payment_status'] ?? 'pending',
            ':payment_method' => $data['payment_method'] ?? null,
            ':dr_fee' => $data['dr_fee'] ?? 0,
            ':totalm_price' => $data['totalm_price'] ?? 0,
            ':total_amount' => $data['total_amount'] ?? 0,
        ]);
    }

    /**
     * SQL: UPDATE tbl_bill SET total_amount = :total_amount, dr_fee = :dr_fee, totalm_price = :totalm_price WHERE bill_id = :id
     */
    public function updateTotals(int $id, float $drFee, float $medTotal, float $grandTotal): int
    {
        $sql = "UPDATE tbl_bill SET dr_fee = :dr_fee, totalm_price = :totalm_price, total_amount = :total_amount WHERE bill_id = :id";
        return $this->execute($sql, [
            ':id' => $id, ':dr_fee' => $drFee, ':totalm_price' => $medTotal, ':total_amount' => $grandTotal,
        ]);
    }

    /**
     * SQL: UPDATE tbl_bill SET payment_status = :status, payment_method = :method WHERE bill_id = :id
     */
    public function markAsPaid(int $id, string $status, string $method): int
    {
        $sql = "UPDATE tbl_bill SET payment_status = :status, payment_method = :method WHERE bill_id = :id";
        return $this->execute($sql, [':id' => $id, ':status' => $status, ':method' => $method]);
    }

    /**
     * SQL: DELETE FROM tbl_bill WHERE bill_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_bill WHERE bill_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_bill WHERE appt_id = :appt_id
     */
    public function findByAppointment(int $apptId): array|false
    {
        $sql = "SELECT * FROM tbl_bill WHERE appt_id = :appt_id";
        return $this->queryOne($sql, [':appt_id' => $apptId]);
    }

    /**
     * SQL: SELECT b.* FROM tbl_bill b JOIN tbl_appointment a ON b.appt_id = a.appt_id WHERE a.patient_id = :patient_id ORDER BY b.bill_date DESC
     */
    public function findByPatient(int $patientId): array
    {
        $sql = "SELECT b.*, a.appointment_date, d.last_name AS doctor_lname
                FROM tbl_bill b 
                JOIN tbl_appointment a ON b.appt_id = a.appt_id 
                JOIN tbl_doctor d ON a.dr_id = d.dr_id 
                WHERE a.patient_id = :patient_id 
                ORDER BY b.bill_date DESC";
        return $this->queryAll($sql, [':patient_id' => $patientId]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_bill
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_bill";
        return (int) $this->queryScalar($sql);
    }

    /**
     * SQL: SELECT COALESCE(SUM(total_amount), 0) FROM tbl_bill WHERE payment_status = 'paid'
     */
    public function getTotalRevenue(): float
    {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) FROM tbl_bill WHERE payment_status = 'paid'";
        return (float) $this->queryScalar($sql);
    }

    /**
     * SQL: SELECT COALESCE(SUM(total_amount), 0) FROM tbl_bill WHERE payment_status = 'paid' AND bill_date >= :start_date
     */
    public function getMonthlyRevenue(string $startDate): float
    {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) FROM tbl_bill WHERE payment_status = 'paid' AND bill_date >= :start_date";
        return (float) $this->queryScalar($sql, [':start_date' => $startDate]);
    }
}