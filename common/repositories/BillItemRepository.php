<?php

namespace common\repositories;

/**
 * Bill Item Repository - All SQL queries for tbl_bill_item table
 * Columns: bill_item_id, bill_id, item_type, description, reference_id, quantity, unit_price, total_price, created_at
 */
class BillItemRepository extends BaseRepository
{
    /**
     * SQL: SELECT * FROM tbl_bill_item WHERE bill_id = :bill_id ORDER BY item_type
     */
    public function findByBill(int $billId): array
    {
        $sql = "SELECT * FROM tbl_bill_item WHERE bill_id = :bill_id ORDER BY item_type";
        return $this->queryAll($sql, [':bill_id' => $billId]);
    }

    /**
     * SQL: SELECT * FROM tbl_bill_item WHERE bill_item_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM tbl_bill_item WHERE bill_item_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_bill_item (bill_id, item_type, description, reference_id, quantity, unit_price, total_price) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_bill_item (bill_id, item_type, description, reference_id, quantity, unit_price, total_price) 
                VALUES (:bill_id, :item_type, :description, :reference_id, :quantity, :unit_price, :total_price)";
        
        return $this->insert($sql, [
            ':bill_id' => $data['bill_id'],
            ':item_type' => $data['item_type'],
            ':description' => $data['description'],
            ':reference_id' => $data['reference_id'] ?? null,
            ':quantity' => $data['quantity'],
            ':unit_price' => $data['unit_price'],
            ':total_price' => $data['total_price'],
        ]);
    }

    /**
     * SQL: UPDATE tbl_bill_item SET ... WHERE bill_item_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_bill_item 
                SET item_type = :item_type, description = :description, reference_id = :reference_id,
                    quantity = :quantity, unit_price = :unit_price, total_price = :total_price
                WHERE bill_item_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':item_type' => $data['item_type'],
            ':description' => $data['description'],
            ':reference_id' => $data['reference_id'] ?? null,
            ':quantity' => $data['quantity'],
            ':unit_price' => $data['unit_price'],
            ':total_price' => $data['total_price'],
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_bill_item WHERE bill_item_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_bill_item WHERE bill_item_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: DELETE FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = :item_type
     */
    public function deleteByBillAndType(int $billId, string $itemType): int
    {
        $sql = "DELETE FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = :item_type";
        return $this->execute($sql, [':bill_id' => $billId, ':item_type' => $itemType]);
    }

    /**
     * SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id
     */
    public function getTotalByBill(int $billId): float
    {
        $sql = "SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id";
        return (float) $this->queryScalar($sql, [':bill_id' => $billId]);
    }

    /**
     * SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = :item_type
     */
    public function getTotalByBillAndType(int $billId, string $itemType): float
    {
        $sql = "SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = :item_type";
        return (float) $this->queryScalar($sql, [':bill_id' => $billId, ':item_type' => $itemType]);
    }

    /**
     * SQL: SELECT * FROM tbl_bill_item ORDER BY created_at DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM tbl_bill_item ORDER BY created_at DESC";
        return $this->queryAll($sql);
    }
}