<?php
/**
 * Loan API Controller
 * 
 * This controller manages all loan-related API operations, enabling users to create, update, delete,
 * and retrieve loan records. It handles parameters such as lender ID, borrower ID, loan amount, interest
 * rate, loan duration, and start date to ensure secure and efficient loan management.
 * 
 * PHP Version: 7.4.33 (recommended)
 * 
 * @category    Controller API
 * @package     CodeIgniter
 * @subpackage  Controllers
 * @author      Avijit Chakravarty
 * @created     2024-11-10
 * @updated     2024-11-16
 * @version     1.0
 */

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH.'libraries/RestController.php';
require APPPATH.'libraries/Format.php';
use chriskacerguis\RestServer\RestController;

class Loan_api extends RestController {
    
    /**
     * Constructor to load the Loan API model.
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('loan_api_model');
        $time_zone='America/New_York';
        if(function_exists('date_default_timezone_set')){
            date_default_timezone_set($time_zone);
        }
    }

    /**
     * Create a New Loan
     * 
     * HTTP Method: POST
     * Endpoint: /loan_api/create_loan
     * 
     * This method handles the creation of a new loan entry. It expects a JSON payload 
     * with fields such as lender_id, borrower_id, loan_amount, interest_rate, duration_years and start_date.
     * The method performs input validation to ensure the integrity of data. 
     * 
     * Example Request Body:
     * {
     *   "lender_id": 1,
     *   "borrower_id": 4,
     *   "loan_amount": 20000,
     *   "interest_rate": 15,
     *   "duration_years": 3,
     *   "start_date": "2024-11-25"
     * }
     * 
     * Responses:
     * - 201 Created: Loan created successfully.
     * - 400 Bad Request: Invalid input or missing required fields.
     * 
     * @return void
     */
    public function create_loan_post() {
        // Get the JSON input data
        $data = json_decode(file_get_contents("php://input"), true);
       
        // Check if the Data is Valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(['message' => 'Invalid JSON format'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Validate Required Fields
        $requiredFields = ['lender_id', 'borrower_id', 'loan_amount', 'interest_rate', 'duration_years', 'start_date'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->response(['message' => "Field '$field' is required"], RestController::HTTP_BAD_REQUEST);
                return;
            }
        }

        // Check if the provided lender ID is valid
        $lender_id=$data['lender_id'];
        $valid_lender=  $this->loan_api_model->check_lender($lender_id);
        if(empty($valid_lender)){
            $this->response(['message' => 'Invalid lender ID'], RestController::HTTP_NOT_FOUND);
            return;
        }
        
        // Check if the provided borrower ID is valid
        $borrower_id=$data['borrower_id'];
        $valid_borrower=  $this->loan_api_model->check_borrower_id($borrower_id);
        if(empty($valid_borrower)){
            $this->response(['message' => 'Invalid borrower ID'], RestController::HTTP_NOT_FOUND);
            return;
        }

        // Validate Data Types and Formats
        if (!is_numeric($data['lender_id']) || $data['lender_id'] <= 0) {
            $this->response(['message' => 'Invalid Lender ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        if (!is_numeric($data['borrower_id']) || $data['borrower_id'] <= 0) {
            $this->response(['message' => 'Invalid Borrower ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (!is_numeric($data['loan_amount']) || $data['loan_amount'] <= 0) {
            $this->response(['message' => 'Invalid loan_amount'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (!is_numeric($data['interest_rate']) || $data['interest_rate'] < 0 || $data['interest_rate'] > 100) {
            $this->response(['message' => 'Invalid interest_rate'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (!is_numeric($data['duration_years']) || $data['duration_years'] <= 0) {
            $this->response(['message' => 'Invalid duration_years'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Check if start_date is in valid 'YYYY-MM-DD' format
        $date_format = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($date_format, $data['start_date'])) {
            $this->response(['message' => 'Invalid date format for start_date. Use YYYY-MM-DD'], RestController::HTTP_BAD_REQUEST);
            return;
        }        
        
        // Attempt to create the loan record
        $success = $this->loan_api_model->add_loan($data);
        
        // Return a response based on the success of the insertion
        if ($success) {
            $this->response(['message' => 'Loan created successfully'], RestController::HTTP_CREATED);
        } else {
            $this->response(['message' => 'Failed to create loan'], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    
    /**
    * Update a Loan Record by ID
    * 
    * HTTP Method: PUT
    * Endpoint: /loan_api/update_loan/{id}
    * 
    * This method updates an existing loan record based on the provided loan ID and also checks if the user is a lender or not. It expects a JSON payload 
    * with fields such as lender_id, borrower_id, loan_amount, interest_rate, duration_years, start_date, and status.
    * The method performs input validation to ensure the integrity of data.
    * 
    * Example Request:
    * PUT /loan_api/update_loan/1
    * 
    * Example Request Body (JSON):
    * {
    *   "lender_id": 2,
    *   "borrower_id": 4,
    *   "loan_amount": 25000,
    *   "interest_rate": 12,
    *   "duration_years": 2,
    *   "start_date": "2024-08-17"
    *   "status": "completed"
    * }
    * 
    * Responses:
    * - 200 OK: Loan updated successfully
    * - 400 Bad Request: Invalid input or missing required fields
    * - 404 Not Found: No loan found with the provided ID
    * 
    * @param int $id Loan ID passed as a URL segment
    * 
    * @return void
    */
    public function update_loan_put($id = null) {
        // Validate the ID parameter
        if ($id === null || !is_numeric($id) || $id <= 0) {
            $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        
        // Check if a record exist
        $record_exist= $this->loan_api_model->check_record_exist($id);
        if(empty($record_exist)){
            $this->response(['message' => 'Loan not found'], RestController::HTTP_NOT_FOUND);
            return;
        }
        
        // Get the raw JSON input data
        $data = json_decode(file_get_contents("php://input"), true);

        // Check if the Data is Valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(['message' => 'Invalid JSON format'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Validate Required Fields 
        $allowedFields = ['lender_id', 'borrower_id', 'loan_amount', 'interest_rate', 'duration_years', 'start_date', 'status'];
        foreach ($allowedFields as $field) {
            if (empty($data[$field])) {
                $this->response(['message' => "Field '$field' is required"], RestController::HTTP_BAD_REQUEST);
                return;
            }
        }
        $data['updated_at']=date('Y-m-d H:i:s');
        
        // Check if the provided lender ID is the original lender of the loan
        $lender_id=$data['lender_id'];
        $valid_lender=  $this->loan_api_model->check_lender_id($id, $lender_id);
        if(empty($valid_lender)){
            $this->response(['message' => 'Invalid lender ID'], RestController::HTTP_NOT_FOUND);
            return;
        }
        
        // Check if the provided borrower ID is exists
        $borrower_id=$data['borrower_id'];
        $valid_borrower=  $this->loan_api_model->check_borrower_id($borrower_id);
        if(empty($valid_borrower)){
            $this->response(['message' => 'Invalid borrower ID'], RestController::HTTP_NOT_FOUND);
            return;
        }        
        
        // Check if there's at least one field to update
        if (empty($data)) {
            $this->response(['message' => 'No valid fields provided to update'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Validate Data Types and Formats
        if (isset($data['lender_id']) && !is_numeric($data['lender_id']) || $data['lender_id'] <= 0) {
            $this->response(['message' => 'Invalid Lender ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        if (isset($data['borrower_id']) && !is_numeric($data['borrower_id']) || $data['borrower_id'] <= 0) {
            $this->response(['message' => 'Invalid Borrower ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        if (isset($data['loan_amount']) && (!is_numeric($data['loan_amount']) || $data['loan_amount'] <= 0)) {
            $this->response(['message' => 'Invalid loan_amount'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (isset($data['interest_rate']) && (!is_numeric($data['interest_rate']) || $data['interest_rate'] < 0 || $data['interest_rate'] > 100)) {
            $this->response(['message' => 'Invalid interest_rate'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (isset($data['duration_years']) && (!is_numeric($data['duration_years']) || $data['duration_years'] <= 0)) {
            $this->response(['message' => 'Invalid duration_years'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (isset($data['start_date'])) {
            $date_format = '/^\d{4}-\d{2}-\d{2}$/';
            if (!preg_match($date_format, $data['start_date'])) {
                $this->response(['message' => 'Invalid date format for start_date. Use YYYY-MM-DD'], RestController::HTTP_BAD_REQUEST);
                return;
            }
        }
        
        // Update the loan record in the database using the model
        $success = $this->loan_api_model->update_loan($id, $data);

        // Check if the update was successful
        if ($success) {
            $this->response(['message' => 'Loan updated successfully'], RestController::HTTP_OK);
        } else {
            $this->response(['message' => 'Failed to update loan or loan not found'], RestController::HTTP_NOT_FOUND);
        }
    }

    /**
    * Delete a Loan Record by ID
    * 
    * HTTP Method: DELETE
    * Endpoint: /loan_api/delete_loan/{id}
    * 
    * This method deletes a loan record from the database based on the provided loan ID.
    * It ensures that the loan exists and validates that the correct and original lender ID of the loan is provided 
    * before performing the deletion. 
    *
    * Example Request:
    * DELETE /loan_api/delete_loan/1
    * Body (JSON):
    * {
    *   "lender_id": 2
    * }
    * 
    * Responses:
    * - 200 OK: Loan deleted successfully
    * - 400 Bad Request: Invalid loan ID or lender ID
    * - 404 Not Found: No loan found with the provided ID
    * 
    * @param int $id Loan ID passed as a URL segment
    * 
    * @return void
    */
   public function delete_loan_delete($id = null) {
       // Validate the ID parameter
       if ($id === null || !is_numeric($id) || $id <= 0) {
           $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_BAD_REQUEST);
           return;
       }
       // Check if a record exist
        $record_exist= $this->loan_api_model->check_record_exist($id);
        if(empty($record_exist)){
            $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_NOT_FOUND);
            return;
        }
        // Get the raw JSON input data
        $data = json_decode(file_get_contents("php://input"), true);
        // Check if the Data is Valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(['message' => 'Invalid JSON format'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        // Validate Required Field
        if (empty($data['lender_id'])) {
            $this->response(['message' => "Field Lender ID is required"], RestController::HTTP_BAD_REQUEST);
            return;
        }
        // Validate Data Types and Formats
        if (isset($data['lender_id']) && !is_numeric($data['lender_id']) || $data['lender_id'] <= 0) {
            $this->response(['message' => 'Invalid Lender ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        // Check if the provided lender ID is the original lender of the loan
        $lender_id=$data['lender_id'];
        $valid_lender=  $this->loan_api_model->check_lender_id($id, $lender_id);
        if(empty($valid_lender)){
            $this->response(['message' => 'Invalid lender ID'], RestController::HTTP_NOT_FOUND);
            return;
        }
       // Attempt to delete the loan using the model
       $this->loan_api_model->delete_loan($id);
       $this->response(['message' => 'Loan deleted successfully'], RestController::HTTP_OK);
    }
    
    /**
    * Get a List of All Loans
    * 
    * HTTP Method: GET
    * Endpoint: /loan_api/get_all_loans
    * 
    * This method retrieves a list of all loan records from the database. It returns loan details
    * such as lender ID, borrower ID, loan amount, interest rate, duration, and start date, status, created at.
    * 
    * Example Request:
    * GET /loan_api/get_all_loans
    * 
    * Responses:
    * - 200 OK: Returns a list of all loans
    * - 404 Not Found: No loans found in the database
    * 
    * @return void
    */
   public function get_all_loans_get() {
       // Retrieve all loans from the database using the model
       $loans = $this->loan_api_model->get_all_loans();

       // Check if any loans were retrieved
       if (!empty($loans)) {
           // Return the list of loans with a 200 OK status
           $this->response(['loans' => $loans, 'message' => 'Loans retrieved successfully'], RestController::HTTP_OK);
       } else {
           // Return a 404 Not Found status if no loans are found
           $this->response(['message' => 'No loans found'], RestController::HTTP_NOT_FOUND);
       }
   }

   /**
    * User Login (Lender/Borrower)
    *
    * This method authenticates users (lenders and borrowers) using their email, password, and role.
    * It validates the input, checks for existing users, and verifies the password. If authentication 
    * is successful, it returns user details along with a success message.
    *
    * HTTP Method: POST
    * End point: /loan_api/login
    * Body (JSON):
    * {
    *   "email": "john_doe@example.com",
    *   "password": "123456",
    *   "role": "lender"  // or "borrower"
    * }
    *
    * Validations:
    * - Checks if 'email', 'password', and 'role' fields are provided.
    * - Ensures that the email format is valid.
    * - Verifies if the role is either 'lender' or 'borrower'.
    *
    * Responses:
    * - HTTP 400 (Bad Request): If any required field is missing or invalid.
    * - HTTP 401 (Unauthorized): If authentication fails (incorrect email, password, or role).
    * - HTTP 200 (OK): If authentication is successful, returns user details.
    *
    * @return void Outputs the appropriate HTTP response based on the authentication result.
    */
   public function login_post() {
       // Get the raw JSON input data from the request body
       $data = json_decode(file_get_contents("php://input"), true);

       // Check if the Data is Valid JSON
       if (json_last_error() !== JSON_ERROR_NONE) {
           $this->response(['message' => 'Invalid JSON format'], RestController::HTTP_BAD_REQUEST);
           return;
       }
       // Validate Required Fields
       $requiredFields = ['email', 'password', 'role'];
       foreach ($requiredFields as $field) {
           if (empty($data[$field])) {
               $this->response(['message' => "Field '$field' is required"], RestController::HTTP_BAD_REQUEST);
               return;
           }
       }
       // Validate Email Format
       if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
           $this->response(['message' => 'Invalid email format'], RestController::HTTP_BAD_REQUEST);
           return;
       }
       // Validate Role
       $role = strtolower($data['role']);
       if ($role !== 'lender' && $role !== 'borrower') {
           $this->response(['message' => 'Role must be either "lender" or "borrower"'], RestController::HTTP_BAD_REQUEST);
           return;
       }
       // attempt to fetch the user
       $user = $this->loan_api_model->get_user_by_email_role($data['email'],$data['password'], $role);

       // Check if user exists
       if (empty($user)) {
           $this->response(['message' => 'User not found or role mismatch'], RestController::HTTP_UNAUTHORIZED);
           return;
       }

       // Successful Authentication
       $this->response([
           'message' => 'Login successful',
           'user' => $user
       ], RestController::HTTP_OK);
   }
   
   /**
     * Retrieve a Loan Record by ID
     * 
     * HTTP Method: GET
     * Endpoint: /loan_api/view_loan/{id}
     * 
     * This method retrieves a specific loan record based on its ID. It expects an integer ID as a parameter.
     * The method validates the ID, checks if a loan exists with that ID, and returns the loan data if found.
     * 
     * Example Request:
     * GET /loan_api/view_loan/1
     * 
     * 
     * Responses:
     * - 200 OK: Returns the loan record with fields such as loan_amount, interest_rate, duration_years, etc.
     * - 400 Bad Request: If the ID is missing or invalid.
     * - 404 Not Found: If no loan is found with the given ID.
     * 
     * @param int $id Loan ID passed as a URL segment.
     * 
     * @return void
     */    
    public function view_loan_get($id=NULL){
        //  Validate the ID parameter
        if ($id === null || !is_numeric($id) || $id <= 0) {
            $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        //  Fetch the loan record from the database using the model
        $loan = $this->loan_api_model->get_loan_by_id($id);
        
        // Check if the loan record exists
        if ($loan) {
            // Return the loan record with a 200 OK status
            $this->response($loan, RestController::HTTP_OK);
        } else {
            // Return a 404 Not Found status if no loan record is found
            $this->response(['message' => 'Loan not found'], RestController::HTTP_NOT_FOUND);
        }
    }

    
}





