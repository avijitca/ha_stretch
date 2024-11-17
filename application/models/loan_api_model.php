<?php

/**
 * Loan_api_model
 *
 * This model handles all database interactions related to loans in the application. It includes
 * methods for creating, retrieving, updating, and deleting loan records. The data is managed
 * through MySQL queries using CodeIgniter's Query Builder class for ease and security.
 *
 * PHP version 7.4.33
 *
 * @category  Models
 * @package   CodeIgniter
 * @author    Avijit Chakravarty
 * @version   1.0
 * @created   2024-11-10
 * @updated   2024-11-11
 * 
 * Usage:
 * This model is typically loaded in controllers using:
 * $this->load->model('loan_api_model');
 * 
 * Methods Included:
 * 1. add_loan($data)              - Insert a new loan record.
 * 2. get_loan_by_id($id)          - Retrieve a specific loan by ID.
 * 3. update_loan($id, $data)      - Update an existing loan.
 * 4. delete_loan($id)             - Delete a loan by ID.
 * 5. get_all_loans()              - Get a list of all loans.
 *
 * 
 */

Class Loan_api_model extends CI_Model{
    /**
    * Add a New Loan Record
    * 
    * This method inserts a new loan record into the 'loans' table in the database.
    * It takes an associative array of loan data and attempts to insert it into the database.
    * 
    * @param array $data An associative array containing loan details.
    *
    * @return bool Returns true if the loan record was inserted successfully,
    *        otherwise returns false.
    * 
    * 
    */    
    function add_loan($data){
        $this->db->insert('loans',$data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * Get Loan Details by Loan ID
    * 
    * This method retrieves a specific loan record from the 'loans' table based on the provided loan ID.
    * It returns loan details such as the user ID, loan amount, interest rate, duration, and start date.
    * 
    * @param int $id The ID of the loan to retrieve.
    * 
    * @return array|Returns an associative array with loan details if found,
    *                    or null if no record is found with the specified ID.
    * 
    */
    function get_loan_by_id($id){
        $this->db->select('lender_id,borrower_id,loan_amount,interest_rate,duration_years,start_date,status,created_at');
        $this->db->from('loans');
        $this->db->where('id',$id);
        $rs=  $this->db->get();
        return $rs->row_array();
    }
    /**
    * Update a Loan Record by ID
    * 
    * This method updates an existing loan record in the database based on the provided loan ID.
    * 
    * @param int $id The ID of the loan to update.
    * @param array $data An associative array containing the fields to update.
    * @return bool Returns true if the update was successful, false otherwise.
    */
   public function update_loan($id, $data) {
       return $this->db->update('loans', $data,array('id'=>$id)); // Returns true on success, false on failure
   }
   /**
    * Check if a Loan Record exist based on the provided loan ID
    * 
    * This method checks if a loan record exist in the database based on the provided loan ID.
    * 
    * @param int $id The ID to check if an record exist.
    * @return array|Returns an associative array with record id if found,
    *                    or null if no record is found with the specified ID.
    */
   public function check_record_exist($id){
        $this->db->select('id');
        $this->db->from('loans');
        $this->db->where('id',$id);
        $rs=  $this->db->get();
        return $rs->row_array();
   }
   
   /**
    * Delete a Loan Record by ID
    * 
    * This method deletes a loan record from the database based on the provided loan ID.
    * 
    * @param int $id The ID of the loan to delete.
    * @return bool Returns true if the deletion was successful, false otherwise.
    */
   public function delete_loan($id) {
       return $this->db->delete('loans',array('id'=>$id)); // Returns true on success, false on failure
   }
   
   /**
    * Get All Loans
    * 
    * This method retrieves all loan records from the database.
    * 
    * @return array Returns an array of loan records or an empty array if no records are found.
    */
   public function get_all_loans() {
       $this->db->select('lender_id,borrower_id,loan_amount,interest_rate,duration_years,start_date,status,created_at');
       $this->db->from('loans');
       $rs=  $this->db->get();
       return $rs->result_array(); // Return as an array
   }
   /**
    * Check if a valid lender exist based on the provided lender ID and role associated with it.
    * 
    * This method checks if a valid lender exist based on the provided lender ID and role associated with it.
    * 
    * @param int $lender_id The ID to check if an record exist.
    * @return array|Returns an associative array with record id if found,
    *                    or null if no record is found with the specified ID.
    */
   
    public function check_lender($lender_id){
        $this->db->select('us.id');
        $this->db->from('users us');
        $where=array('us.id'=>$lender_id,'us.role'=>'lender');
        $this->db->where($where);
        $rs=  $this->db->get();
        return $rs->row_array();
    }
   /**
    * Check if a valid lender exist based on the provided lender ID and role associated with it.
    * 
    * This method checks if a valid lender exist based on the provided lender ID and role associated with it.
    *              Also checks if the lender is a original lender of the loan
    * 
    * @param int $lender_id The ID to check if an record exist.
    * @param int $id The ID to uniquely identify a loan.
    * @return array|Returns an associative array with record id if found,
    *                    or null if no record is found with the specified ID.
    */
   
    public function check_lender_id($id, $lender_id){
        $this->db->select('us.id');
        $this->db->from('users us');
        $this->db->join('loans ln','ln.lender_id=us.id','left');
        $where=array('ln.id'=>$id,'ln.lender_id'=>$lender_id,'us.role'=>'lender');
        $this->db->where($where);
        $rs=  $this->db->get();
        return $rs->row_array();
    }
   /**
    * Check if a valid borrower exist based on the provided borrower ID and role associated with it.
    * 
    * This method checks if a valid borrower exist based on the provided borrower ID and role associated with it.
    * 
    * @param int $borrower_id The ID to check if an record exist.
    * @return array|Returns an associative array with record id if found,
    *                    or null if no record is found with the specified ID.
    */
   
    public function check_borrower_id($borrower_id){
        $this->db->select('id');
        $this->db->from('users');
        $where=array('id'=>$borrower_id,'role'=>'borrower');
        $this->db->where($where);
        $rs=  $this->db->get();
        return $rs->row_array();
    }
    /**
    * Get lender ID.
    * 
    * This method checks the row ID of the loan and returns the lender ID associalted with it.
    * 
    * @param int $id The ID to uniquely indentify a row.
    * @return array|Returns an associative array with lender id if found,
    *                    or null if no record is found with the specified ID.
    */
    public function get_lender_id($id){
        $this->db->select('lender_id');
        $this->db->from('loans');
        $this->db->where('id',$id);
        $rs=  $this->db->get();
        return $rs->row_array();
    }
    /**
    * Get User by Email, Role and Password
    *
    * This method fetches a user from the database using the provided email, role and password.
    *
    * @param string $email The user's email address.
    * @param string $password The user's password address.
    * @param string $role The user's role (either 'lender' or 'borrower').
    * @return array|null Returns the user data if found, otherwise null.
    */
   public function get_user_by_email_role($email,$password, $role) {
       $this->db->select('email, role, name');
       $this->db->from('users');
       $this->db->where('email', $email);
       $this->db->where('password', md5($password));
       $this->db->where('role', $role);
       $query = $this->db->get();
       return $query->row_array();
   }

}